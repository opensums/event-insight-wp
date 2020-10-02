<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp/ticket-tailor
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight\WpPlugin;

use EventInsight\WpPlugin\Container;

/**
 * This class implements WordPress REST API routes.
 */
class Routes {

    /** @var string WordPress capability for 'root' level permissions. */
    protected $rootPermission = 'manage_options';

    /** @var string Path prefix for all routes in this class. */
    protected $routeNamespace;

    /**
     * Constructor.
     *
     * @param Container $container Dependencies.
     */
    final public function __construct(Container $container) {
        $this->container = $container;
        $this->init();
    }

    protected function init() {}

    /**
     * Register routes with the WP REST dispatcher.
     */
    public function register() {}

    protected function checkPermission(string $permission = null): bool {
        if ($permission === null) {
            return current_user_can($this->rootPermission);
        }
        return current_user_can($permission) || current_user_can($this->rootPermission);
    }

    protected function registerRoute(array $route) {
        register_rest_route($this->routeNamespace, $route['path'], [
            //@TODO need more methods!
            'methods' => 'GET',
            'callback' => function () use ($route) {
                try {
                    return call_user_func_array($route['callback'], func_get_args());
                } catch (HttpException $e) {
                    $data = ['error' => $e->getMessage()];
                    $meta = $e->getMeta();
                    if ($meta !== null) {
                        $data['meta'] = $meta;
                    }
                    return new \WP_REST_Response($data, $e->getStatusCode());
                } catch (\Throwable $e) {
                    return new \WP_REST_Response(['error' => $e->getMessage()], 500);
                }
            },
            'permission_callback' => function () use ($route) {
                return $this->checkPermission($route['permission'] ?? null);
            },
        ]);
    }
}
