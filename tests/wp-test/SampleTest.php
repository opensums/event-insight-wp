<?php
/**
 * Class SampleTest
 *
 * @package Sample_Plugin
 */

/**
 * Sample test case.
 */
class ActivateTest extends WP_UnitTestCase {
    const PLUGIN_PATH = __DIR__.'/../../event-insight/src/';

    /**
     * A single example test.
     */
    function testActivation() {
        // do_action('activate_'.self::PLUGIN_PATH);
        add_option('event_insight_settings', 'test', null, false);
        $setOption = get_option('event_insight_settings');

        // Replace this with some actual testing code.
        $this->assertEquals($setOption, 'test');
    }

    /**
     * A single example test.
     */
    function testNoPersistence() {
        do_action('activate_'.self::PLUGIN_PATH);
        $setOption = get_option('event_insight_settings');

        // Replace this with some actual testing code.
        $this->assertEquals($setOption, false);
    }
}
