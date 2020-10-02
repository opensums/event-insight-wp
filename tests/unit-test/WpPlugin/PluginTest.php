<?php

namespace UnitTests\EventInsight\WpPlugin;

use EventInsight\WpPlugin\Plugin;
use UnitTestFixtures\TestPlugin;

use WP_Mock;
use WP_Mock\Tools\TestCase;

require __DIR__.'/../fixtures/TestPlugin.php';

class PluginTest extends TestCase {
    public function setUp(): void {
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
    }

    public function getPlugin(): TestPlugin {
        return TestPlugin::factory();
    }

    public function testPluginGetName(): void {
        $this->assertEquals('Test Plugin', $this->getPlugin()->getName());
    }

    public function testPluginGetVersion(): void {
        $this->assertEquals('0.0.0-dev', $this->getPlugin()->getVersion());
    }

    public function testPluginSlugifyWithNoArguments(): void {
        $this->assertEquals('test-plugin', $this->getPlugin()->slugify());
    }

    public function testPluginSlugifyWithArguments(): void {
        $this->assertEquals('test_plugin_core_settings', $this->getPlugin()->slugify('core-settings', '_'));
    }
}
