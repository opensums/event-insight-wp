<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp/wp-plugin
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight\WpPlugin;

abstract class AdminPage {

    /** @var array[] JavaScript and CSS assets to load. */
    protected $assets = [];

    /** @var string The capability required to access this page. */
    protected $capability = 'manage_options';

    protected $icon;

    /**
     * @var string The text to be shown in the admin menu (if not set will be
     *             based on the plugin name by the constructor).
     */
    protected $menuLabel;

    /** @var string The parent for the entry in the Admin menu. */
    protected $menuParent;

    /** @var string The slug for the page (will be prefixed by the constructor). */
    protected $pageSlug;

    /**
     * @var string The text to be shown as the page <title> and <h1> heading (if
     *             not set will be based on the plugin name by the constructor).
     */
    protected $pageTitle;

    /** @var Plugin The parent plugin (set by the constructor). */
    protected $plugin;

    protected $sections = [];
    protected $sectionsTemplate;

    /** @var string The name of the template to render. */
    protected $template;

    /** @var array Template variables. */
    protected $templateVars = [];

    // Public methods ------------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @param Plugin The parent plugin.
     */
    public function __construct($container) {
        // Save the dependencies.
        $this->container = $container;

        $plugin = $container->get('plugin');

        // Prefix the page slug with the plugin slug.
        $this->pageSlug = $plugin->slugify($this->pageSlug ?? 'settings');

        // Set default menu label and page title if these have not been set.
        if ($this->menuLabel === null) {
            $this->menuLabel = $plugin->getName();
        }
        if ($this->pageTitle === null) {
            $this->pageTitle = $plugin->getName() . ' Settings';
        }

        // Set template variables.
        $this->templateVars = array_merge([
            'pageSlug' => $this->pageSlug,
            'pageTitle' => $this->pageTitle,
        ], $this->templateVars);

        // Register admin hooks.
        $this->addMenuEntry();
        add_action('admin_enqueue_scripts', [$this, 'addAssets']);
        add_action('admin_init', [$this, 'addSections']);

        // Initialize.
        $this->init();
    }

    /**
     * Add page assets.
     *
     * Invoked as a callback for any admin page.
     *
     * @param string $slug The full slug for the current admin page.
     */
    public function addAssets(string $slug): void {
        // Only add the scripts if we look like we are on the right page (note
        // that the hook for a settings page starts with `settings_page`).
        if (substr($slug, -strlen($this->pageSlug)) !== $this->pageSlug) return;

        $plugin = $this->container->get('plugin');

        foreach ($this->assets as $asset) {
            switch ($asset[0]) {
                case 'style':
                    // Add stylesheet to head.
                    if (isset($asset[2])) {
                        wp_enqueue_style($asset[1], $plugin->getAssetsUrl($asset[2]));
                    } else {
                        wp_enqueue_style($asset[1]);
                    }
                break;

                case 'head':
                    // Add js to head.
                    if (isset($asset[2])) {
                        wp_enqueue_script($asset[1], $plugin->getAssetsUrl($asset[2]), $asset[3] ?? []);
                    } else {
                        wp_enqueue_script($asset[1]);
                    }
                break;

                case 'script':
                    // Add js to the bottom of the page.
                    wp_enqueue_script($asset[1], $plugin->getAssetsUrl($asset[2]), $asset[3] ?? [], false, true);
                break;

                default:
            }
        }
    }

    /**
     * Render the page.
     *
     * Invoked as a callback when the related admin menu item is selected.
     */
    public function render(): void {
        if (!current_user_can($this->capability)) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $pageVars = $this->prepare();

        $this->container->get('plugin')->render($this->template, array_merge(
            $this->templateVars,
            [
                'messagesSlug' => "$this->pageSlug-messages",
            ],
            $pageVars
        ));
    }

    /**
     * $section is an array with keys `id`, `title` and `callback`.
     *
     * Invoked as a callback for each page section.
     */
    public function renderSection(array $section): void {
        if ($this->sectionsTemplate === null) return;
        $this->container->get('plugin')
            ->render($this->sectionsTemplate, array_merge($this->templateVars, $section));
    }

    // Protected methods ---------------------------------------------------------------------------

    protected function init() {}

    /**
     * Add a admin menu entry for the page.
     */
    protected function addMenuEntry(): void {
        // Set up the arguments for the call to the WP function.
        $args = [
            $this->pageTitle,
            $this->menuLabel,
            $this->capability,
            $this->pageSlug,
            [$this, 'render'],
        ];

        // Call the appropriate WP function.
        switch ($this->menuParent) {
            case 'settings':
                // Add an entry under the 'Settings' top-level entry.
                call_user_func_array('add_options_page', $args);
            break;

            case null:
                // Add a top-level entry.
                if ($this->icon !== null) {
                    $args[] = $this->icon;
                }
                call_user_func_array('add_menu_page', $args);
            break;

            default:
                // Add an entry under the top-level entry with the given slug.
                array_unshift($args, $this->menuParent);
                call_user_func_array('add_submenu_page', $args);
        }
    }

    protected function getSections() {
        return [];
    }

    /**
     * Prepare data for rendering.
     */
    protected function prepare(): array {
        return [];
    }

    // Refactor after here -------------------------------------------------------------------------

    protected $groups = [];

    public function addSections(): void {
        $this->sections = $this->getSections();
        foreach ($this->sections as $section) {
            add_settings_section(
                $section['id'],
                $section['title'] ?? null,
                [$this, 'renderSection'],
                $this->pageSlug
            );
        }
        $this->addFields();
    }

    /**
     *
     * @param mixed[] $sections An array of sections. Each section is an array:
     * - `string 'id'` The id for the admin page section.
     * - `string 'title'` HTML for the section's title.
     */
    protected function addFields() {
        $plugin = $this->container->get('plugin');
        foreach ($this->sections ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                // Group names by option group and prefix.
                $group = $field['option'] = $field['option'] ?? $section['option'];
                $this->groups[$group] = true;
                $group = $plugin->slugify($group, '_');
                $field['name'] = "{$group}[{$field['key']}]";

                // Use label_for in preference to id (since WP 4.6).
                $field['label_for'] = $field['id'] = "{$group}-{$field['key']}";
                add_settings_field(
                    $field['id'],
                    $field['label'],
                    [$this, 'renderField'],
                    $this->pageSlug,
                    $section['id'],
                    $field
                );
            }
        }

        // Register all the options groups for the form nonce.
        foreach (array_keys($this->groups) as $group) {
            $optionsGroup = $this->container->get($group);
            $optionsGroup->registerSettingsForm($this->pageSlug);
            $this->values[$group] = $optionsGroup->all();
        }
    }

    /**
     * @see https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
     */
    public function renderField($field) {
        if (is_array($this->values[$field['option']])) {
            $value = $this->values[$field['option']][$field['key']] ?? null;
        } else {
            $value = null;
        }

        // Check which type of field we want
        switch ($field['type'] ?? null) {
            case 'textarea': // If it is a textarea
                printf(
                    '<textarea name="%1$s" id="%2$s" placeholder="%3$s" rows="%5$s"'
                        . ' cols="50">%4$s</textarea>',
                    $field['name'],
                    $field['id'],
                    $field['placeholder'],
                    $value,
                    $field['rows'] ?? 5
                );
                break;
            case 'select': // If it is a select dropdown
                if (!empty($field['options']) && is_array($field['options'])) {
                    $options_markup = '';
                    foreach ($field['options'] as $key => $label) {
                        $options_markup .= sprintf(
                            '<option value="%s" %s>%s</option>',
                            $key,
                            selected($value, $key, false),
                            $label
                        );
                    }
                    printf(
                        '<select name="%1$s" id="%2$s">%3$s</select>',
                        $field['name'],
                        $field['id'],
                        $options_markup
                    );
                }
                break;
            case 'html': // Just render the html
                echo($field['html']);
                break;
            case 'text': // If it is a text field
            default:
                $width = isset($field['width']) ? ' style="width:' . $field['width'] . 'px"' : '';
                $size = isset($field['size']) ? ' size="' . $field['size'] . '"' : '';
                printf(
                    '<input name="%1$s" id="%2$s" type="%3$s" placeholder="%4$s"'
                        . ' value="%5$s"' . $size . $width . '/>',
                    $field['name'],
                    $field['label_for'],
                    $field['type'] ?? 'text',
                    $field['placeholder'] ?? null,
                    $value
                );
        }

        // If there is help text
        if ($helper = $field['helper'] ?? null) {
            printf('<span class="helper"> %s</span>', $helper); // Show it
        }

        // If there is supplemental text
        if ($supplemental = $field['supplemental'] ?? null) {
            printf('<p class="description">%s</p>', $supplemental); // Show it
        }
    }
}
