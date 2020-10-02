<?php

class AdminPageTest extends WP_UnitTestCase {

    function setAdminUser() {
        wp_set_current_user(self::factory()->user->create([
            'role' => 'administrator',
        ]));
    }

    function test_guest_should_have_no_admin_menu_entries() {

        wp_set_current_user(0);

        do_action('admin_menu');

        // $this->assertEmpty(menu_page_url('event-insight-data', false));
        $this->assertEmpty(menu_page_url('event-insight-settings', false));
        $this->assertEmpty(menu_page_url('event-insight-secrets', false));
    }


    function test_user_with_data_permissions_should_have_data_menu_entry() {

        wp_set_current_user(self::factory()->user->create([
            'role' => 'subscriber',
        ]));

        do_action('admin_menu');

        $this->assertNotEmpty(menu_page_url('event-insight-data', false));
        $this->assertEmpty(menu_page_url('event-insight-settings', false));
        $this->assertEmpty(menu_page_url('event-insight-secrets', false));
    }

    function test_administrator_should_have_all_admin_menu_entries() {

        wp_set_current_user(self::factory()->user->create([
            'role' => 'administrator',
        ]));

        do_action('admin_menu');

        $this->assertNotEmpty(menu_page_url('event-insight-data', false));
        $this->assertNotEmpty(menu_page_url('event-insight-settings', false));
        $this->assertNotEmpty(menu_page_url('event-insight-secrets', false));
    }

    /**
     * A single example test.
     */
    function adminPageDisplays() {
        global $current_screen;

        $this->assertFalse(is_admin());

        // $this->assertTrue(has_action('plugins_loaded', 'load_sos_admin'));

        /*
        global $submenu;
            
        $this->assertFalse( isset( $submenu[ 'edit.php?post_type=sos' ] ) );
        $this->assertFalse( 
                Util::has_action( 'admin_page_sos_settings_page', 
                        $this->sos_options, 'render_settings_page' ) );
        
        $this->sos_options->register_settings_page();
        
        $this->assertTrue( isset( $submenu[ 'edit.php?post_type=sos' ] ) );
        $settings = $submenu[ 'edit.php?post_type=sos' ];
        $this->assertSame( 'Settings', $settings[ 0 ][ 0 ] );
        $this->assertSame( 'administrator', $settings[ 0 ][ 1 ] );
        $this->assertSame( 'sos_settings_page', $settings[ 0 ][ 2 ] );
        $this->assertSame( 'Common Options', $settings[ 0 ][ 3 ] );
        $this->assertTrue( 
                Util::has_action( 'admin_page_sos_settings_page', 
                        $this->sos_options, 'render_settings_page' ) );
        */
    }
}
