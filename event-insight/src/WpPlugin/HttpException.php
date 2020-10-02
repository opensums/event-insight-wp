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

class HttpException extends \Exception {

    protected $meta;

    protected $statusCode = 500;

    public function setMeta(array $meta): self {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta() {
        return $this->meta;
    }

    public function setStatusCode(int $code): self {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }
}
