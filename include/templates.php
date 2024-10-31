<?php

if (!function_exists('qu_sp_ticket_js_vars'))
{
    /**
     * Get data needed by javascript code for the ticket template page
     *
     * @return mixed|void
     */
    function qu_sp_ticket_js_vars()
    {
        return apply_filters('quip_support_ticket_js_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }
}


if (!function_exists('qu_sp_ticket_header'))
{
    /**
     * Output the headers for the ticket template
     *
     */
    function qu_sp_ticket_header()
    {
        ?>
        <link rel="stylesheet" href="<?php echo QUIP_SUPPORT_CSS_DIR ?>bootstrap.css" type="text/css" media="screen">
        <link rel="stylesheet" href="<?php echo QUIP_SUPPORT_CSS_DIR ?>quip-support.css" type="text/css" media="screen">
        <?php
        do_action('quip_support_ticket_header_styles');
        ?>
        <script type="text/javascript" src="<?php echo site_url() ?>/wp-includes/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo site_url() ?>/wp-includes/js/jquery/jquery-migrate.min.js"></script>
        <?php
        do_action('quip_support_ticket_header_scripts');
        ?>
        <script type="text/javascript" src="<?php echo QUIP_SUPPORT_JS_DIR ?>quip-support.js?<?php echo QuipSupport::$VERSION; ?>"></script>
        <script type="text/javascript">
            /* <![CDATA[ */
            var quip_support = <?php echo json_encode(qu_sp_ticket_js_vars()); ?>;
            /* ]]> */
        </script>
        <?php

        do_action('quip_support_ticket_header');
    }
}