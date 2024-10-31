<?php

/*
Plugin Name: Quip Support Free - Ultimate Help Desk Solution
Plugin URI: http://quipcode.com
Description: Free version of Quip Support complete customer help desk
Author: QuipCode
Version: 1.0.0
Author URI: http://quipcode.com
Text Domain: quip-support-free
*/

//defines
if (!defined('QUIP_SUPPORT_NAME'))
    define('QUIP_SUPPORT_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('QUIP_SUPPORT_BASENAME'))
    define('QUIP_SUPPORT_BASENAME', plugin_basename(__FILE__));

if (!defined('QUIP_SUPPORT_DIR'))
    define('QUIP_SUPPORT_DIR', WP_PLUGIN_DIR . '/' . QUIP_SUPPORT_NAME);

if (!defined('QUIP_SUPPORT_JS_DIR'))
    define('QUIP_SUPPORT_JS_DIR', plugins_url('js/', __FILE__));

if (!defined('QUIP_SUPPORT_CSS_DIR'))
    define('QUIP_SUPPORT_CSS_DIR', plugins_url('css/', __FILE__));

if (!class_exists('QuipSupport'))
{
    class QuipSupport
    {
        public static $instance;
        public static $VERSION = '1.0.0';
        private $options = 'quip_support_options';
        private $adminMenu, $ticket;
        public $db;

        /**
         * Get the singleton class instance
         *
         * @return QuipSupport singleton
         */
        public static function getInstance()
        {
            if (is_null(self::$instance))
            {
                self::$instance = new QuipSupport();
            }
            return self::$instance;
        }

        /**
         * constructor for QuipSupport
         */
        function __construct()
        {
            $this->includes();
            $this->hooks();

            $this->adminMenu = new QuipSupportAdminMenu();
            $this->ticket = new QuipSupportTicket();
            $this->db = new QuipSupportDatabase();
        }

        private function includes()
        {
            include_once 'include/database.php';
            include_once 'include/admin-menu.php';
            include_once 'include/ticket.php';
            include_once 'include/html.php';
            include_once 'include/templates.php';

            do_action('quip_support_includes');
        }

        private function hooks()
        {
            // Hook to show welcome screen if new activation
            add_action('admin_init', array($this, 'redirect_welcome_screen'), 1);
            // Shortcode to draw front-end customer support page
            add_shortcode('quip_support_form', array($this, 'support_shortcode'));
            // Display ticket by reference number
            add_action('template_redirect', array($this, 'support_listener'));

            // Settings
            add_action('wp_ajax_quip_support_update_settings', array($this, 'update_settings'));
            add_action('wp_ajax_quip_support_update_settings_agents', array($this, 'update_settings_agents'));
            add_action('wp_ajax_quip_support_update_settings_email', array($this, 'update_settings_email'));
            add_action('wp_ajax_quip_support_remove_agent', array($this, 'remove_agent'));

            do_action('quip_support_hooks');
        }

        /**
         * Called on plugin activation
         */
        public static function activate()
        {
            QuipSupport::setup_database();
            QuipSupport::setup_plugin_options();
            // Activate our welcome page
            set_transient('qs_show_welcome_page', 1, 30);

            // Create customer service role
            add_role('qs_customer_service', 'Customer Service', array('read' => true));

            // Add the role to the person activating this plugin (the admin) by default
            $user = wp_get_current_user();
            $user->add_role('qs_customer_service');

            do_action('quip_support_activate');
        }

        /**
         * Called on plugin deactivation
         */
        public static function deactivate()
        {
            remove_role('qs_customer_service');

            do_action('quip_support_deactivate');
        }

        /**
         * Setup the database on activation
         */
        public static function setup_database()
        {
            include_once 'include/database.php';
            QuipSupportDatabase::setup_db();
        }

        /**
         * Set the option defaults on activation
         */
        public static function setup_plugin_options()
        {
            $options = get_option('quip_support_options');
            if (!$options)
            {
                $options['supportEmail'] = get_bloginfo('admin_email');
                $options['showAuditLog'] = '1';
                $options['sendNotifications'] = '1';
                $options['companyLogo'] = '';
                $options['sendSurveyOnClose'] = '0';
                $options['templates'] = array(array('name' => 'Sample Template', 'content' => base64_encode("This is a sample template.\nRegards,\nCustomer Service")));
                $options['createTicketEmailSubject'] = 'Your customer support ticket has been created';
                $options['createTicketEmail'] = base64_encode(QuipSupportHtml::create_ticket_email_default());
                $options['agentUpdateTicketEmailSubject'] = 'Your ticket has been updated';
                $options['agentUpdateTicketEmail'] = base64_encode(QuipSupportHtml::ticket_agent_reply_default());
                $options['customerUpdateTicketEmailSubject'] = 'The customer has updated the ticket';
                $options['customerUpdateTicketEmail'] = base64_encode(QuipSupportHtml::ticket_customer_reply_default());

                $options['version'] = QuipSupport::$VERSION;
                update_option('quip_support_options', $options);
            }
            else if ($options['version'] !== QuipSupport::$VERSION)
            {
                if (!array_key_exists('supportEmail', $options)) $options['supportEmail'] = get_bloginfo('admin_email');
                if (!array_key_exists('showAuditLog', $options)) $options['showAuditLog'] = '1';
                if (!array_key_exists('sendNotifications', $options)) $options['sendNotifications'] = '1';
                if (!array_key_exists('companyLogo', $options)) $options['companyLogo'] = '1';
                if (!array_key_exists('sendSurveyOnClose', $options)) $options['sendSurveyOnClose'] = '0';
                if (!array_key_exists('templates', $options)) $options['templates'] =
                    array(array('name' => 'Sample Template', 'content' => base64_encode("This is a sample template.\nRegards,\nCustomer Service")));
                if (!array_key_exists('createTicketEmailSubject', $options)) $options['createTicketEmailSubject'] = 'Your customer support ticket has been created';
                if (!array_key_exists('createTicketEmail', $options)) $options['createTicketEmail'] = base64_encode(QuipSupportHtml::create_ticket_email_default());
                if (!array_key_exists('agentUpdateTicketEmailSubject', $options)) $options['agentUpdateTicketEmailSubject'] = 'Your ticket has been updated';
                if (!array_key_exists('agentUpdateTicketEmail', $options)) $options['agentUpdateTicketEmail'] = base64_encode(QuipSupportHtml::ticket_agent_reply_default());
                if (!array_key_exists('customerUpdateTicketEmailSubject', $options)) $options['customerUpdateTicketEmailSubject'] = 'The customer has updated the ticket';
                if (!array_key_exists('customerUpdateTicketEmail', $options)) $options['customerUpdateTicketEmail'] = base64_encode(QuipSupportHtml::ticket_customer_reply_default());

                $options['version'] = QuipSupport::$VERSION;
                update_option('quip_support_options', $options);
            }
        }

        /**
         * Redirect to the about/welcome page on activation
         */
        function redirect_welcome_screen()
        {
            // only do this if the user can activate plugins
            if (!current_user_can('manage_options'))
                return;

            // don't do anything if the transient isn't set
            if (!get_transient('qs_show_welcome_page'))
                return;

            delete_transient('qs_show_welcome_page');
            wp_safe_redirect(admin_url('admin.php?page=quip-support-about'));
            exit;
        }

        function update_settings()
        {
            $this->_demo_block();

            $options = get_option('quip_support_options');
            $options['showAuditLog'] = $_POST['showAuditLog'];
            $options['companyLogo'] = $_POST['companyLogo'];
            $options['sendNotifications'] = $_POST['sendNotifications'];
            $options['sendSurveyOnClose'] = $_POST['sendSurveyOnClose'];
            $options['showCreateTicketKB'] = $_POST['showCreateTicketKB'];
            $options['createTicketPage'] = $_POST['createTicketPage'];
            update_option('quip_support_options', $options);
            $this->json_exit(true, 'Settings Updated', '');
        }

        function update_settings_agents()
        {
            $this->_demo_block();

            $wpUserId = $_POST['wpUserId'];
            $user = get_user_by('id', $wpUserId);
            if ($user)
            {
                $user->add_role('qs_customer_service');
            }

            $this->json_exit(true, 'User updated successfully', '');
        }

        function update_settings_email()
        {
            $this->_demo_block();

            $options = get_option('quip_support_options');
            $options['createTicketEmailSubject'] = sanitize_text_field($_POST['createTicketEmailSubject']);
            $options['createTicketEmail'] = base64_encode(nl2br($_POST['createTicketEmail']));
            $options['agentUpdateTicketEmailSubject'] = sanitize_text_field($_POST['agentUpdateTicketEmailSubject']);
            $options['agentUpdateTicketEmail'] = base64_encode(nl2br($_POST['agentUpdateTicketEmail']));
            $options['customerUpdateTicketEmailSubject'] = sanitize_text_field($_POST['customerUpdateTicketEmailSubject']);
            $options['customerUpdateTicketEmail'] = base64_encode(nl2br($_POST['customerUpdateTicketEmail']));
            update_option('quip_support_options', $options);
            $this->json_exit(true, 'Settings Updated', '');
        }

        function remove_agent()
        {
            $this->_demo_block();

            $wpUserId = $_POST['wpUserId'];
            $user = get_user_by('id', $wpUserId);
            if ($user)
            {
                $user->remove_role('qs_customer_service');
                $user->remove_cap('qs_customer_service');
            }

            $this->json_exit(true, 'User updated successfully', '');
        }

        function support_listener()
        {
            // if this is not a request for us
            if (!isset($_GET['qsupport']) || $_GET['qsupport'] == '')
            {
                do_action('quip_support_listener');
                return;
            }

            $options = get_option('quip_support_options');

            
            if (is_user_logged_in())
            {
                $user = wp_get_current_user();
                $ticket = $this->db->get_full_ticket_by_owner_reference($user->ID, $_GET['qsupport']);
            }

            //output customer service portal
            ob_start();
            include 'view/ticket_template.php';
            echo apply_filters('quip_support_ticket_template', ob_get_clean());
            exit;
            
        }

        function support_shortcode($atts)
        {
            // extract the shortcode attributes into local scope, with defaults if not set.
            extract(shortcode_atts(array(
                'showEmail' => '1',
            ), $atts));

            // load front-end resources
            $this->load_resources();

            // load into output buffer and return content for shortcode
            ob_start();
            include QUIP_SUPPORT_DIR . '/view/support_template.php';
            $content = ob_get_clean();
            return apply_filters('quip_support_form_template_output', $content);
        }

        function load_resources()
        {
            // css
            wp_enqueue_style('quip-support-css', plugins_url('/css/quip-support.css', __FILE__), self::$VERSION);

            // js
            wp_enqueue_script("quip-support-js", plugins_url("/js/quip-support.js", __FILE__), array('jquery'), QuipSupport::$VERSION);
            wp_localize_script("quip-support-js", 'quip_support', array('ajaxurl' => admin_url('admin-ajax.php')));

            do_action('quip_support_load_resources');
        }


        /**
         * Convenience function for returning from our standard ajax request
         *
         * @param $success
         * @param $message
         * @param $redirectURL
         */
        public function json_exit($success, $message, $redirectURL)
        {
            header("Content-Type: application/json");
            echo json_encode(array('success' => $success, 'redirectURL' => $redirectURL, 'msg' => $message));
            exit;
        }

        /**
         * Block some functionality during demo mode
         */
        private function _demo_block()
        {
            if (defined('QUIP_SUPPORT_DEMO_MODE'))
                $this->json_exit(true, 'This is disabled in DEMO mode', '');
        }
    }
}

//Get the instance of QuipSupport
QuipSupport::getInstance();

// activation and deactivation hooks
register_activation_hook(__FILE__, array('QuipSupport', 'activate'));
register_deactivation_hook(__FILE__, array('QuipSupport', 'deactivate'));