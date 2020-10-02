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

namespace EventInsight;

use EventInsight\WpPlugin\Container;

/**
 * This class implements WordPress REST API routes.
 */
class Routes extends WpPlugin\Routes {

    /** @var string WordPress capability for 'root' level permissions. */
    // protected $rootPermission = 'manage_options';

    /** @var string Path prefix for all ticket tailor routes. */
    protected $routeNamespace = 'event-insight/v1';

    /**
     * Register routes with the WP REST dispatcher.
     */
    public function register() {
        $this->registerRoute([
            'path' => '/ticket-tailor/event/(?P<id>[A-Za-z0-9_-]+)/tickets',
            'callback' => function ($params) {
                $api = $this->container->get('ticket-tailor');
                $payload = $api->getEventTickets($params['id']);
                return new \WP_REST_Response($payload, 200);
            },
        ]);

        $this->registerRoute([
            'path' => '/ticket-tailor/events',
            'callback' => function () {
                $api = $this->container->get('ticket-tailor');
                $payload = $api->getEvents();
                return new \WP_REST_Response($payload, 200);
            },
        ]);
    }
}
