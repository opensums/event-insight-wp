<?php

namespace UnitTests\EventInsight\WpPlugin;

use UnitTestFixtures\TestPlugin;

use WP_Mock;
use WP_Mock\Tools\TestCase;

class OptionsTest extends TestCase {

    protected static $plugin;

    public static function setUpBeforeClass(): void {
        self::$plugin = TestPlugin::factory();
    }

    public function setUp(): void {
        WP_Mock::setUp();
    }

    public function tearDown(): void {
        WP_Mock::tearDown();
    }

    protected function getOptions() {
        return new Options(self::$plugin);
    }

    public function testCanGetOption(): void {
        $this->assertTrue(true);
    }
}
