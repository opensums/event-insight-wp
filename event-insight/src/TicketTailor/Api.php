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

namespace EventInsight\TicketTailor;

use EventInsight\WpPlugin\HttpException;

class Api {
    /** @var Container Dependencies. */
    protected $container; 

    /** @var mixed[] Settings. */
    protected $apiKey;

    /** @var string API URL. */
    protected $url = 'https://api.tickettailor.com/v1';

    protected $demoUrl = 'https://cdn.jsdelivr.net/gh/opensums/event-data-wp-js@main';
    // protected $demoUrl = 'https://cdn.jsdelivr.net/gh/opensums/event-insight-wp-js@main';

    public function __construct($container) {
        $this->container = $container;
        $wp = $container->get('wp');
        $secrets = $wp->getOption('event_insight_secrets');

        $this->apiKey = ($secrets['ticket-tailor-api-key'] ?? null) ?: null;
    }

    /**
     * Get a collection of events.
     */
    public function getEvents() {
        if ($this->apiKey === null) {
            return [
                'data' => $this->demoRequest('/tests/api-responses/ticket-tailor/events.json'),
            ];
        }
        return $this->apiRequest('/events');
    }

    /**
     * Get a collection of tickets for an event.
     */
    public function getEventTickets($eventId) {
        if ($this->apiKey === null) {
            return [
                'data' => $this->demoRequest('/tests/api-responses/ticket-tailor/tickets.json'),
            ];
        }
        return $this->apiRequest("/issued_tickets?event_id=$eventId");
    }

    protected function demoRequest($path) {
        $response = $this->container->get('wp')->jsonRequest([
            'url' => $this->demoUrl,
            'path' => $path,
        ]);
        if (!(array_key_exists('data', $response) && array_key_exists('data', $response['data']))) {
            $message = 'Could not load demo data.';
            throw (new HttpException($message))->setStatusCode(404);
        }
        return $response['data']['data'];
    }

    protected function apiRequest($path) {
        $response = $this->container->get('wp')->jsonRequest([
            'url' => $this->url,
            'path' => $path,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->apiKey),
            ]
        ]);

        // $statusCode = $response['response']['code'];
        // Handle an error response.
        if (array_key_exists('error', $response)) {
            $request = $response['error'];
            $data = json_decode($request['body'], true);
            $message = $data['message'] ?? $request['response']['message'];
            $statusCode = $request['response']['code'];
            throw (new HttpException($message))->setStatusCode($statusCode);
        }

        // Retry-After 	The number of seconds to wait until the rate limit window resets. This is
        // only sent when the rate limit is reached.
        // $headers['Retry-After'];
        return array_merge($response['data'], [
            'meta' => [
                'rate' => [
                    // The maximum number of requests that the consumer is permitted to make per hour.
                    'limit' => $response['headers']['x-rate-limit-limit'] ?? null,
                    // The number of requests remaining in the current rate limit window.
                    'remaining' => $response['headers']['x-rate-limit-remaining'] ?? null,
                    // The number of seconds left in the current period.
                    'reset' => $response['headers']['x-rate-limit-reset'] ?? null,
                ],
            ],
        ]);
    }
}
