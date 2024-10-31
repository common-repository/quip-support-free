<?php

/**
 * Class QuipSupportAdminMenu
 * Manages Admin Menu options and pages.
 */
class QuipSupportAdminMenu
{
    private $capability = 'qs_customer_service';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        if (defined('QUIP_SUPPORT_DEMO_MODE'))
            $this->capability = 'read';
    }

    /**
     * Initialize admin menu
     */
    public function init()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Create the admin menu and sub menu options, attach scripts and styles.
     */
    public function admin_menu()
    {
        // Add the top-level admin menu
        $page_title = 'Quip Support';
        $menu_title = 'Quip Support';
        $capability = $this->capability;
        $menu_slug = 'quip-support-main';
        $function = 'display_support';
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, $function), plugin_dir_url(dirname(__FILE__)) . '/img/icon.png');

        // Add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = __('Dashboard', 'quip-support');
        $menu_hook = add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, array($this, $function));
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts')); //this ensures script/styles only loaded for this plugin admin pages

        $submenu_page_title = __('All Tickets', 'quip-support');
        $submenu_title = __('All Tickets', 'quip-support');
        $submenu_slug = 'quip-support-all-tickets';
        $submenu_function = array($this, 'display_all_tickets');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('My Tickets', 'quip-support');
        $submenu_title = __('My Tickets', 'quip-support');
        $submenu_slug = 'quip-support-my-tickets';
        $submenu_function = array($this, 'display_my_tickets');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('Knowledge Base', 'quip-support');
        $submenu_title = __('Knowledge Base', 'quip-support');
        $submenu_slug = 'quip-support-knowledgebase';
        $submenu_function = array($this, 'display_knowledgebase');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('Settings', 'quip-support');
        $submenu_title = __('Settings', 'quip-support');
        $submenu_slug = 'quip-support-settings';
        $submenu_function = array($this, 'display_settings');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        // Help screen, no additional JS/CSS
        $submenu_page_title = __('Help', 'quip-support');
        $submenu_title = __('Help', 'quip-support');
        $submenu_slug = 'quip-support-help';
        $submenu_function = array($this, 'display_help');
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        $submenu_page_title = __('About', 'quip-support');
        $submenu_title = __('About', 'quip-support');
        $submenu_slug = 'quip-support-about';
        $submenu_function = array($this, 'display_about');
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        // don't show below on submenu (null for $menu_slug)
        $submenu_page_title = 'Customer';
        $submenu_title = 'Customer';
        $submenu_slug = 'quip-support-customer';
        $submenu_function = array($this, 'display_customer');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = 'Ticket';
        $submenu_title = 'Ticket';
        $submenu_slug = 'quip-support-ticket';
        $submenu_function = array($this, 'display_ticket');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        do_action('quip_support_admin_menu', $menu_slug);

    }

    /**
     * Attaches scripts and styles to submenu pages
     */
    public function admin_scripts()
    {
        wp_enqueue_script('quip-support-adminutils-js', plugins_url('/js/adminutils.js', dirname(__FILE__)), array('jquery'), QuipSupport::$VERSION);
        wp_enqueue_script('jquery-ui-dialog');

        wp_enqueue_style('jquery-ui-css', plugins_url('/css/jquery-ui.min.css', dirname(__FILE__)));
        wp_enqueue_style('jquery-ui-structure-css', plugins_url('/css/jquery-ui.structure.min.css', dirname(__FILE__)));
        wp_enqueue_style('jquery-ui-theme-css', plugins_url('/css/jquery-ui.theme.min.css', dirname(__FILE__)));
        wp_enqueue_style('bootstrap-css', plugins_url('/css/wrap-bootstrap.css', dirname(__FILE__)));
        wp_enqueue_style('quip-support-css', plugins_url('/css/quip-support.css', dirname(__FILE__)));

        do_action('quip_support_admin_scripts');
    }

    /**
     * Display support page
     */
    public function display_support()
    {
        $this->admin_enqueue_and_localize('support');
        include QUIP_SUPPORT_DIR . '/view/support_page.php';
    }

    public function display_all_tickets()
    {
        $this->admin_enqueue_and_localize('all_tickets');

        //load the table
        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('QuipSupportTableTickets'))
        {
            require_once(QUIP_SUPPORT_DIR . '/include/tables/quip-support-table-tickets.php');
        }

        $type = isset($_GET['tab']) ? $_GET['tab'] : 'open';
        $table = new QuipSupportTableTickets($type);
        $table->prepare_items();

        include QUIP_SUPPORT_DIR . '/view/all_tickets_page.php';
    }

    public function display_my_tickets()
    {
        $this->admin_enqueue_and_localize('all_tickets');

        //load the table
        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('QuipSupportTableTickets'))
        {
            require_once(QUIP_SUPPORT_DIR . '/include/tables/quip-support-table-tickets.php');
        }

        $type = isset($_GET['tab']) ? $_GET['tab'] : 'open';
        $user = wp_get_current_user();
        $table = new QuipSupportTableTickets($type, $user->ID);
        $table->prepare_items();

        include QUIP_SUPPORT_DIR . '/view/my_tickets_page.php';
    }

    public function display_knowledgebase()
    {
        $this->admin_enqueue_and_localize('knowledgebase');
        include QUIP_SUPPORT_DIR . '/view/knowledgebase_page.php';
    }

    /**
     * Display settings page
     */
    public function display_settings()
    {
        wp_enqueue_media();
        $this->admin_enqueue_and_localize('settings');
        include QUIP_SUPPORT_DIR . '/view/settings_page.php';
    }

    /**
     * Display help page
     */
    public function display_help()
    {
        include QUIP_SUPPORT_DIR . '/view/help_page.php';
    }

    /**
     * Display about page
     */
    public function display_about()
    {
        include QUIP_SUPPORT_DIR . '/view/about_page.php';
    }

    /**
     * Display customer details page
     */
    public function display_customer()
    {
        $this->admin_enqueue_and_localize('customer');

        $id = sanitize_text_field($_GET['id']);
        $customer = QuipSupport::getInstance()->db->get_customer($id);

        if ($customer)
        {
            include QUIP_SUPPORT_DIR . '/view/customer_page.php';
        }
        else
        {
            include QUIP_SUPPORT_DIR . '/view/error_page.php';
        }
    }

    /**
     * Display ticket details page
     */
    public function display_ticket()
    {
        $this->admin_enqueue_and_localize('ticket');

        $id = sanitize_text_field($_GET['id']);
        $ticket = QuipSupport::getInstance()->db->get_full_ticket($id);

        if ($ticket)
        {
            include QUIP_SUPPORT_DIR . '/view/ticket_page.php';
        }
        else
        {
            include QUIP_SUPPORT_DIR . '/view/error_page.php';
        }
    }


    /**
     * Assumes page follows naming convention of JS file, i.e. admin_{page}.js
     *
     * @param $page string Name to use follows convention
     * @param array $extraData Any extra parameters to localize
     */
    private function admin_enqueue_and_localize($page, $extraData = array())
    {
        wp_enqueue_script("quip-support-admin-{$page}-js",
            plugins_url("/js/admin_{$page}.js", dirname(__FILE__)),
            array('quip-support-adminutils-js'), QuipSupport::$VERSION);

        $localizeData = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pageurl' => admin_url("admin.php?page=quip-support-{$page}"),
        );

        $localizeData = array_merge($localizeData, $extraData);

        wp_localize_script("quip-support-admin-{$page}-js", 'quip_support', $localizeData);
    }

}
