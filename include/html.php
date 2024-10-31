<?php

/**
 * Class QuipSupportHtml
 * Helper class for formatting html output
 */
class QuipSupportHtml
{
    const BS_LABEL = "<span class='label label-%TYPE%'>%TEXT%</span>";

    /**
     * Output bootstrap label based on how long since $dt
     * @param DateTime $dt
     * @return string
     */
    public static function time_ago_label($dt)
    {
        $date = new DateTime();
        $date->setTimestamp(strtotime($dt));
        $interval = $date->diff(new DateTime());

        if ($interval->d < 1)
        {
            $labelText = $interval->format("%h hours & %i minutes ago");
            $labelType = 'success';
        }
        else if ($interval->d < 3)
        {
            $labelText = $interval->format("%d days & %h hours ago");
            $labelType = 'warning';
        }
        else
        {
            $labelText = $interval->format("%d days & %h hours ago");
            $labelType = 'danger';
        }

        return str_replace(["%TYPE%", "%TEXT%"], [$labelType, $labelText], self::BS_LABEL);
    }

    public static function ticket_status_label($status)
    {

        if ($status == 'unresolved')
        {
            $labelType = 'danger';
        }
        else if ($status == 'ongoing')
        {
            $labelType = 'primary';
        }
        else
        {
            $labelType = 'success';
        }

        return str_replace(["%TYPE%", "%TEXT%"], [$labelType, ucfirst($status)], self::BS_LABEL);
    }

    /**
     * As viewed by the support staff on their back end page
     *
     * @param $ticket
     * @param $content
     * @return string
     */
    public static function ticket_content_header($ticket, $content)
    {
        $header = "<h5>Date: " . date('F jS Y H:i', strtotime($content->created)) . "</h5>";
        $from = "<h5>From: %FROM%</h5>";
        $to = "<h5>To: %TO%</h5>";

        if ($content->ownerType == 'customer')
        {
            $customer = QuipSupport::getInstance()->db->get_customer($content->ownerId);
            if ($customer->firstName)
            {
                $from = str_replace("%FROM%", $customer->firstName . ' ' . $customer->lastName, $from);
            }
            else
            {
                $from = str_replace("%FROM%", $customer->email, $from);
            }

            $to = str_replace("%TO%", "Customer Service", $to);
        }
        else
        {
            $customer = QuipSupport::getInstance()->db->get_customer($ticket->customerId);
            $customerService = get_user_by('id', $content->ownerId);
            $loggedIn = wp_get_current_user();
            $fromName = $customerService->user_login;
            if ($content->ownerId == $loggedIn->ID)
                $fromName = "<strong>YOU</strong>";

            $from = str_replace("%FROM%", $fromName, $from);

            if ($customer->firstName)
            {
                $to = str_replace("%TO%", $customer->firstName . ' ' . $customer->lastName, $to);
            }
            else
            {
                $to = str_replace("%TO%", $customer->email, $to);
            }
        }

        $header .= $from . $to;
        return $header;
    }

    /**
     * As viewed by the customer on their front end page
     * @param $content
     * @return string
     */
    public static function ticket_content_header_customer($content)
    {
        $header = "<h5>Date: " . date('F jS Y H:i', strtotime($content->created)) . "</h5>";
        $from = "<h5>From: %FROM%</h5>";
        $to = "<h5>To: %TO%</h5>";

        if ($content->ownerType == 'customer')
        {
            $from = str_replace("%FROM%", "<strong>YOU</strong>", $from);
            $to = str_replace("%TO%", "Customer Service", $to);
        }
        else
        {
            $from = str_replace("%FROM%", "Customer Service", $from);
            $to = str_replace("%TO%", "<strong>YOU</strong>", $to);
        }

        $header .= $from . $to;
        return $header;
    }

    public static function create_ticket_email_default()
    {
        $html = "<h1>Your ticket has been created</h1>";
        $html .= "<p>Your ticket <strong>%%TICKET_TITLE%%</strong> has been created and our support staff will get back to you as soon as possible.</p>";
        $html .= "<p>To check on the status of your ticket you can use the link below.  We will also email you when it is updated.</p>";
        $html .= "%%TICKET_LINK%%";
        $html .= "<p>Kind Regards,<br/>%%WEBSITE_NAME%%</p>";

        return $html;
    }

    public static function create_ticket_email($ticket)
    {
        $options = get_option('quip_support_options');
        $template = stripslashes(base64_decode($options['createTicketEmail']));

        return str_replace(
            array(
                '%%TICKET_TITLE%%',
                '%%TICKET_LINK%%',
                '%%WEBSITE_NAME%%'),
            array(
                stripslashes($ticket->title),
                "<a href='" . site_url() . "?qsupport=" . $ticket->reference . "'>View My Ticket</a>",
                get_bloginfo('name')),
            $template
        );
    }

    public static function ticket_agent_reply_default()
    {
        $html = "<p>Hi there,<br/>Your ticket <strong>%%TICKET_TITLE%%</strong> has been updated by our customer support member, %%AGENT_NAME%%.</p>";
        $html .= "<p>Below is their response.  <strong>PLEASE DO NOT REPLY TO THIS EMAIL.</strong> If you wish to respond, please click the link at the bottom of this email.</p>";
        $html .= "<p>-------------------------------------------------------------------------------------------------------------</p>";
        $html .= "<pre>%%TICKET_REPLY%%</pre>";
        $html .= "<p>-------------------------------------------------------------------------------------------------------------</p>";
        $html .= "<p>You can update your ticket & more by clicking this link: %%TICKET_LINK%%</p>";
        $html .= "<p>Kind Regards,<br/>%%WEBSITE_NAME%%</p>";

        return $html;
    }

    public static function ticket_agent_reply_email($ticketId, $from, $ticketContent)
    {
        $options = get_option('quip_support_options');
        $ticket = QuipSupport::getInstance()->db->get_ticket($ticketId);
        $template = stripslashes(base64_decode($options['agentUpdateTicketEmail']));

        return str_replace(
            array(
                '%%TICKET_TITLE%%',
                '%%AGENT_NAME%%',
                '%%TICKET_REPLY%%',
                '%%TICKET_LINK%%',
                '%%WEBSITE_NAME%%'),
            array(
                stripslashes($ticket->title),
                $from->display_name,
                $ticketContent,
                "<a href='" . site_url() . "?qsupport=" . $ticket->reference . "'>View My Ticket</a>",
                get_bloginfo('name')),
            $template
        );
    }

    public static function ticket_customer_reply_default()
    {
        $html = "<p>Hi there,<br/>Your assigned ticket <strong>%%TICKET_TITLE%%</strong> has been updated by the customer.</p>";
        $html .= "<p>Below is their response.  <strong>PLEASE DO NOT REPLY TO THIS EMAIL.</strong> If you wish to respond, please click the link at the bottom of this email.</p>";
        $html .= "<p>-------------------------------------------------------------------------------------------------------------</p>";
        $html .= "<pre>%%TICKET_REPLY%%</pre>";
        $html .= "<p>-------------------------------------------------------------------------------------------------------------</p>";
        $html .= "<p>You can manage this ticket by clicking the following link: %%TICKET_LINK%%</p>";
        $html .= "<p>Kind Regards,<br/>%%WEBSITE_NAME%%</p>";

        return $html;
    }

    public static function ticket_customer_reply_email($ticketId, $ticketContent)
    {
        $options = get_option('quip_support_options');
        $ticket = QuipSupport::getInstance()->db->get_ticket($ticketId);
        $template = stripslashes(base64_decode($options['customerUpdateTicketEmail']));

        return str_replace(
            array(
                '%%TICKET_TITLE%%',
                '%%TICKET_REPLY%%',
                '%%TICKET_LINK%%',
                '%%WEBSITE_NAME%%'),
            array(
                stripslashes($ticket->title),
                $ticketContent,
                "<a href='" . admin_url('admin.php?page=quip-support-ticket&id=' . $ticketId) . "'>Manage This Ticket</a>",
                get_bloginfo('name')),
            $template
        );
    }


    /**
     * From a WordPress user object, return their full name or if not defined, their email address
     * @param $user
     * @return string
     */
    public static function wp_user_full_name_or_email($user)
    {
        if (isset($user->first_name) && strlen($user->first_name) > 0)
        {
            $name = $user->first_name;

            if (isset($user->last_name) && strlen($user->last_name) > 0)
            {
                $name .= ' ' . $user->last_name;
            }
        }
        else
        {
            $name = $user->user_email;
        }

        return $name;
    }

    /**
     * Output simple html list of latest tickets
     */
    public static function latest_ticket_list()
    {
        $tickets = QuipSupport::getInstance()->db->get_latest_tickets(5);
        $html = "<div class='list-group'>";
        foreach ($tickets as $t)
        {
            $html .= "<div class='list-group-item'>";
            $html .= "<h4><a href='" . admin_url('admin.php?page=quip-support-ticket&id=' . $t->id) . "'>" . stripslashes($t->title) . "</a></h4>";
            $html .= "<span>" . QuipSupportHtml::ticket_status_label($t->status) . "</span><br>";
            $html .= "<span>" . QuipSupportHtml::time_ago_label($t->created) . "</span>";
            $html .= "</div>";
        }
        $html .= "</div>";

        return $html;
    }

    /**
     * Output simple html list of unresolved tickets
     */
    public static function outstanding_ticket_list()
    {
        $tickets = QuipSupport::getInstance()->db->get_unresolved_tickets(5);
        $html = "<div class='list-group'>";
        foreach ($tickets as $t)
        {
            $html .= "<div class='list-group-item'>";
            $html .= "<h4><a href='" . admin_url('admin.php?page=quip-support-ticket&id=' . $t->id) . "'>" . stripslashes($t->title) . "</a></h4>";
            $html .= "<span>" . QuipSupportHtml::ticket_status_label($t->status) . "</span><br>";
            $html .= "<span>" . QuipSupportHtml::time_ago_label($t->created) . "</span>";
            $html .= "</div>";
        }
        $html .= "</div>";

        return $html;
    }

}