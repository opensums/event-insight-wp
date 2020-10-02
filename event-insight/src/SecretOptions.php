<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight;

use EventInsight\WpPlugin\OptionsGroup;

class SecretOptions extends OptionsGroup {
    protected $keys = [
        'ticket-tailor-api-key' => [],
    ];

}
