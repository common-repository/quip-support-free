<?php

/**
 * Class QuipSupportController
 * Base class for controllers
 */
class QuipSupportController
{
    protected $db;

    public function __construct()
    {
        include_once 'database.php';
        $this->db = new QuipSupportDatabase();
    }

    public function json_exit($success, $message, $redirectURL)
    {
        header("Content-Type: application/json");
        echo json_encode(array('success' => $success, 'redirectURL' => $redirectURL, 'msg' => $message));
        exit;
    }

    public function send_email($to, $fromName, $fromEmail, $subject, $message, $inHeaders = [], $attachments = [])
    {
        $name = isset($fromName) ? $fromName : get_bloginfo('name');
        $from_email = isset($fromEmail) ? $fromEmail :  get_bloginfo('admin_email');
        $headers[] = "From: $name <$from_email>";
        $headers[] = "Content-type: text/html";
        $headers = array_merge($headers, $inHeaders);

        $html = "<!DOCTYPE html>\n<html lang='en-US'>\n<head>\n<meta charset='utf-8'>\n</head>\n<body>\n";
        $html .= $message;
        $html .= "</body>\n</html>";

        //send
        return wp_mail(
            apply_filters('quip_support_mail_to', $to),
            apply_filters('quip_support_mail_subject', $subject),
            apply_filters('quip_support_mail_html', $html),
            apply_filters('quip_support_mail_headers', $headers),
            apply_filters('quip_support_mail_attachments', $attachments)
        );
    }

    /**
     * Block some functionality during demo mode
     */
    protected function _demo_block()
    {
        if (defined('QUIP_SUPPORT_DEMO_MODE'))
            $this->json_exit(true, 'This is disabled in DEMO mode', '');
    }
}