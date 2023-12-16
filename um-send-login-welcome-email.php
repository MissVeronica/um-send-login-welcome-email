<?php
/**
 * Plugin Name:     Ultimate Member - Welcome email at login
 * Description:     Extension to Ultimate Member for sending the Welcome email at user\'s first login.
 * Version:         1.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

Class UM_Welcome_Email_at_Login {

    public $disable_email_notification_welcome_email = true;

    function __construct() {

        add_action( 'um_submit_form_errors_hook_logincheck',  array( $this, 'my_submit_form_errors_hook_logincheck' ), 10, 1 );
        add_filter( 'um_disable_email_notification_sending',  array( $this, 'um_disable_email_notification_sending_approval' ), 10, 4 );
        add_filter( 'um_admin_settings_email_section_fields', array( $this, 'um_admin_settings_email_section_fields_welcome' ), 10, 2 );
    }

    public function um_disable_email_notification_sending_approval( $boolean, $email, $template, $args ) {

        if ( UM()->options()->get( 'welcome_email_at_login' ) == 1 ) {

            if ( $template == 'welcome_email' ) {
                $boolean = $this->disable_email_notification_welcome_email;
            }
        }

        return $boolean;
    }

    public function my_submit_form_errors_hook_logincheck( $args ) {

        if ( UM()->options()->get( 'welcome_email_at_login' ) == 1 ) {

            $user_id = ( isset( UM()->login()->auth_id ) ) ? UM()->login()->auth_id : '';
            um_fetch_user( $user_id );

            $last_login = um_user( '_um_last_login' );

            if ( empty( $last_login )) {
                $this->disable_email_notification_welcome_email = false;
                UM()->mail()->send( um_user( 'user_email' ), 'welcome_email' );
            }
        }
    }

    public function um_admin_settings_email_section_fields_welcome( $section_fields, $email_key ) {

        if ( $email_key == 'welcome_email' ) {

            $section_fields[] = array(
                        'id'            => $email_key . '_at_login',
                        'type'          => 'checkbox',
                        'label'         => __( 'Welcome email at login - Check to activate', 'ultimate-member' ),
                        'tooltip'       => __( 'Uncheck to disable sending the Welcome email at user\'s first login.', 'ultimate-member' ),
                        'conditional'   => array( $email_key . '_on', '=', 1 ),
                    );
        }

        return $section_fields;
    }


}

new UM_Welcome_Email_at_Login();
