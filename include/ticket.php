<?php

include_once 'base.controller.php';

/**
 * Class QuipSupportTicket
 * Handles ticket related functions
 */
class QuipSupportTicket extends QuipSupportController
{
    public function __construct()
    {
        parent::__construct();

        add_action('wp_ajax_quip_support_create_ticket', array($this, 'create_ticket'));
        add_action('wp_ajax_nopriv_quip_support_create_ticket', array($this, 'create_ticket'));
        add_action('wp_ajax_quip_support_assign_ticket', array($this, 'assign_ticket'));
        add_action('wp_ajax_quip_support_delete_ticket', array($this, 'delete_ticket'));
        add_action('wp_ajax_quip_support_ticket_reply', array($this, 'ticket_reply'));
        add_action('wp_ajax_quip_support_get_ticket_template', array($this, 'get_ticket_template'));
        add_action('wp_ajax_quip_support_ticket_customer_reply', array($this, 'ticket_customer_reply'));
        add_action('wp_ajax_nopriv_quip_support_ticket_customer_reply', array($this, 'ticket_customer_reply'));
        add_action('wp_ajax_quip_support_ticket_update_status', array($this, 'update_status'));
        add_action('wp_ajax_quip_support_ticket_close', array($this, 'close_ticket'));
        add_action('wp_ajax_quip_support_ticket_open', array($this, 'open_ticket'));
    }

    /**
     * Create new support ticket from front-end form.  We'll assign this ticket to the logged in user.
     */
    public function create_ticket()
    {
        $title = sanitize_text_field($_POST['title']);
        $details = $_POST['details'];

        if (strlen($title) < 3)
        {
            $this->json_exit(false, "Please add a title for your issue", '');
        }
        if (strlen($details) < 10)
        {
            $this->json_exit(false, "Please add a few more details for your issue", '');
        }
        if (!is_user_logged_in()) //NOTE: shouldn't happen...
        {
            $this->json_exit(false, "You must be logged in to create a support ticket", '');
        }

        $wpUser = wp_get_current_user();
        $wpUserId = $wpUser->ID;

        // Check if this user has ever requested support before
        $customer = $this->db->find_customer_by_wpid($wpUserId);

        if (!$customer)
        {
            $customerId = $this->db->create_customer([
                'wpUserId' => $wpUserId,
                'email' => $wpUser->user_email,
                'firstName' => $wpUser->first_name,
                'lastName' => $wpUser->last_name,
                'created' => date('y-m-d H:i:s')
            ]);
        }
        else
        {
            $customerId = $customer->id;
        }

        // Now create the ticket
        $ticketId = $this->db->create_ticket($customerId, $title, base64_encode(nl2br($details)));
        $ticket = $this->db->get_ticket($ticketId);
        $options = get_option('quip_support_options');

        // Send the email confirmation to customer
        $this->send_email($wpUser->user_email,
            get_bloginfo('name') . " Support",
            null,
            stripslashes($options['createTicketEmailSubject']),
            QuipSupportHtml::create_ticket_email($ticket));

        $this->json_exit(true, 'The ticket was created successfully & you will receive an email shortly.', site_url() . "?qsupport=" . $ticket->reference);
    }

    public function assign_ticket()
    {
        $ticketId = $_POST['id'];
        $wpUserId = $_POST['userId'];
        $this->db->assign_ticket($ticketId, $wpUserId);

        $this->json_exit(true, 'The ticket was assigned successfully', '');
    }

    public function delete_ticket()
    {
        $this->_demo_block();

        $ticketId = $_POST['id'];
        $this->db->delete_ticket($ticketId);
        $this->json_exit(true, 'The ticket was deleted successfully', '');
    }

    /**
     * Save and send response from customer service rep to customer
     */
    public function ticket_reply()
    {
        $ticketId = $_POST['ticketId'];
        $preContent = $_POST['ticketContent'];
        if (strlen($preContent) < 3)
        {
            $this->json_exit(false, 'Please write a more detailed message', '');
        }

        $content = base64_encode(nl2br($preContent));
        $from = wp_get_current_user();
        $ticket = $this->db->get_ticket($ticketId);
        $to = $this->db->get_customer($ticket->customerId);

        $this->db->add_content_to_ticket([
            'ticketId' => $ticketId,
            'ownerType' => 'support',
            'ownerId' => $from->ID,
            'content' => $content,
            'created' => date('y-m-d H:i:s')
        ]);

        $options = get_option('quip_support_options');

        // Send the email reply to customer
        $this->send_email($to->email,
            $from->display_name,
            $from->user_email,
            stripslashes($options['agentUpdateTicketEmailSubject']),
            QuipSupportHtml::ticket_agent_reply_email($ticketId, $from, stripslashes($_POST['ticketContent'])));

        $this->json_exit(true, 'The ticket was updated successfully', '');
    }

    /**
     * Save and send response from customer to customer service
     */
    public function ticket_customer_reply()
    {
        $ticketId = $_POST['ticketId'];
        $preContent = $_POST['ticketContent'];
        if (strlen($preContent) < 3)
        {
            $this->json_exit(false, 'Please write a more detailed message', '');
        }
        $content = base64_encode(nl2br($preContent));
        $ticket = $this->db->get_ticket($ticketId);
        $customer = $this->db->get_customer($ticket->customerId);

        $this->db->add_content_to_ticket([
            'ticketId' => $ticketId,
            'ownerType' => 'customer',
            'ownerId' => $customer->id,
            'content' => $content,
            'created' => date('y-m-d H:i:s')
        ]);

        // Send the email reply to customer service agent
        $options = get_option('quip_support_options');
        if ($options['sendNotifications'] == 1 && $ticket->assignedWpUserId != null)
        {
            $agent = get_user_by('id', $ticket->assignedWpUserId);
            $this->send_email($agent->user_email,
                ($customer->firstName) ? $customer->firstName . ' ' . $customer->lastName : $customer->email,
                $customer->email,
                stripslashes($options['customerUpdateTicketEmailSubject']),
                QuipSupportHtml::ticket_customer_reply_email($ticketId, stripslashes($_POST['ticketContent'])));
        }

        $this->json_exit(true, 'The ticket was updated successfully', '');
    }

    public function get_ticket_template()
    {
        $name = $_POST['name'];
        $options = get_option('quip_support_options');
        $template = array();
        foreach ($options['templates'] as $t)
        {
            if ($t['name'] == $name)
            {
                $template = $t;
                break;
            }
        }

        header("Content-Type: application/json");
        echo json_encode(array('success' => true, 'template' => $template));
        exit;
    }

    public function update_status()
    {
        $ticketId = $_POST['ticketId'];
        $status = $_POST['updateStatus'];
        $this->db->update_ticket_status($ticketId, $status);

        $this->json_exit(true, 'The ticket was updated successfully', '');
    }

    public function close_ticket()
    {
        $ticketId = $_POST['ticketId'];
        $this->db->close_ticket($ticketId);

        $options = get_option('quip_support_options');
        if ($options['sendSurveyOnClose'] == 1)
        {
            $ticket = $this->db->get_ticket($ticketId);
            if ($ticket->status == 'resolved')
            {
                QuipSupport::getInstance()->survey->send_ticket_survey($ticketId);
            }
        }

        $this->json_exit(true, 'The ticket was closed', '');
    }

    public function open_ticket()
    {
        $ticketId = $_POST['ticketId'];
        $this->db->open_ticket($ticketId);

        $this->json_exit(true, 'The ticket was opened', '');
    }
    
}