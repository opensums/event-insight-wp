<?php

namespace UnitTestFixtures;

use EventInsight\WpPlugin\Plugin;
use WP_Mock;

class TestPlugin extends Plugin {
    protected $name = 'Test Plugin';
    protected $slug = 'test-plugin';
    protected $version = '0.0.0-dev';

    public static function factory() {
        WP_Mock::userFunction('plugin_dir_url', [
            'return' => __DIR__,
        ]);
        return new TestPlugin(__DIR__);
    }
}
