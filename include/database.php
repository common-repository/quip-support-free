<?php

if (!class_exists('QuipSupportDatabase'))
{
    class QuipSupportDatabase
    {
        const TICKETS_TABLE = "qs_tickets";
        const CUSTOMERS_TABLE = "qs_customers";
        const TICKET_CONTENT_TABLE = "qs_ticket_content";

        public static function setup_db()
        {
            //require for dbDelta()
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            global $wpdb;

            $table = $wpdb->prefix . 'qs_customers';

            $sql = "CREATE TABLE " . $table . " (
            id INT NOT NULL AUTO_INCREMENT,
            wpUserId INT,
            firstName VARCHAR(500),
            lastName VARCHAR(500),
            email VARCHAR(500) NOT NULL,
            created DATETIME,
            updated DATETIME,
            UNIQUE KEY id (id)
            );";

            //database write/update
            dbDelta($sql);

            /////////////////////////////////////////////

            $table = $wpdb->prefix . 'qs_tickets';

            $sql = "CREATE TABLE " . $table . " (
            id INT NOT NULL AUTO_INCREMENT,
            customerId INT NOT NULL,
            assignedWpUserId INT DEFAULT NULL,
            reference VARCHAR(23),
            title VARCHAR(500) NOT NULL,
            status VARCHAR(100) DEFAULT 'unresolved',
            closed TINYINT(1) DEFAULT 0,
            created DATETIME,
            updated DATETIME,
            UNIQUE KEY id (id)
            );";

            //database write/update
            dbDelta($sql);

            /////////////////////////////////////////////

            $table = $wpdb->prefix . 'qs_ticket_content';

            //NOTE: ownerId == qs_customer::id for customer and WordPress USER ID for support agents

            $sql = "CREATE TABLE " . $table . " (
            id INT NOT NULL AUTO_INCREMENT,
            ticketId INT NOT NULL,
            ownerId INT NOT NULL,
            ownerType VARCHAR(100) DEFAULT 'customer',
            content TEXT,
            created DATETIME,
            updated DATETIME,
            UNIQUE KEY id (id)
            );";

            //database write/update
            dbDelta($sql);

            /////////////////////////////////////////////

        }


        /////  Generic database functionality for all tables ////////
        public function insert_item($table, $item)
        {
            global $wpdb;
            $wpdb->insert($wpdb->prefix . $table, $item);
            return $wpdb->insert_id;
        }

        public function update_item($table, $id, $item)
        {
            global $wpdb;
            $wpdb->update($wpdb->prefix . $table, $item, array('id' => $id));
        }

        public function delete_item($table, $id)
        {
            global $wpdb;
            $wpdb->delete($wpdb->prefix . $table, array('id' => $id));
        }

        public function delete_item_where($table, array $where)
        {
            global $wpdb;
            $wpdb->delete($wpdb->prefix . $table, $where);
        }

        public function get_item($table, $id)
        {
            global $wpdb;
            $table = $wpdb->prefix . $table;
            return $wpdb->get_row("SELECT * FROM $table WHERE id=$id");
        }

        public function get_all_items($table, $limit = null, $offset = null, $order = null)
        {
            global $wpdb;
            $table = $wpdb->prefix . $table;
            $sql = "SELECT * FROM $table";
            if ($order)
            {
                $sql .= " $order";
            }
            if ($limit)
            {
                $sql .= " LIMIT $limit OFFSET $offset"; //verbose method
            }

            return $wpdb->get_results($sql);
        }

        public function get_item_where($table, $where)
        {
            global $wpdb;
            $table = $wpdb->prefix . $table;
            return $wpdb->get_row("SELECT * FROM $table WHERE $where");
        }

        public function get_items_where($table, $where, $limit = null, $offset = null)
        {
            global $wpdb;
            $table = $wpdb->prefix . $table;
            $sql = "SELECT * FROM $table WHERE $where";
            if ($limit)
            {
                $sql .= " LIMIT $limit OFFSET $offset"; //verbose method
            }

            return $wpdb->get_results($sql);
        }

        ////////////////////////////////////////////////////////////////////

        public function create_customer($data)
        {
            return $this->insert_item(self::CUSTOMERS_TABLE, $data);
        }

        public function find_customer($email)
        {
            $sql = "email = '$email'";
            return $this->get_item_where(self::CUSTOMERS_TABLE, $sql);
        }

        public function find_customer_by_wpid($id)
        {
            $sql = "wpUserId = '$id'";
            return $this->get_item_where(self::CUSTOMERS_TABLE, $sql);
        }

        public function get_customer($id)
        {
            return $this->get_item(self::CUSTOMERS_TABLE, $id);
        }

        public function get_total_customers()
        {
            global $wpdb;
            $total = 0;
            $customersTable = $wpdb->prefix . self::CUSTOMERS_TABLE;
            $sql = "SELECT COUNT(*) as total FROM $customersTable";
            $result = $wpdb->get_row($sql);
            if ($result)
            {
                $total = $result->total;
            }

            return $total;
        }

        public function get_customer_tickets($customerId)
        {
            return $this->get_items_where(self::TICKETS_TABLE, "customerId = $customerId");
        }

        ////////////////////////////////////////////////////////////////////

        /**
         * Nicer & more human readable than uniqid().  This base 36 method with length 7 should
         * give over 78 billion unique ids.
         *
         * @param int $length
         * @return string
         */
        public function generate_ticket_reference($length = 7)
        {
            $reference = '';
            $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            for ($i = 0; $i < $length; $i++)
                $reference .= $chars[rand(0, 35)];

            // Double check no ticket exists with reference
            $ticket = $this->get_item_where(self::TICKETS_TABLE, "reference='$reference'");
            if ($ticket)
            {
                $reference = '';
                for ($i = 0; $i < $length; $i++)
                    $reference .= $chars[rand(0, 36)];
            }

            return $reference;
        }

        /**
         * New tickets are always created by customers
         *
         * @param $customerId
         * @param $title
         * @param $content
         * @return int
         */
        public function create_ticket($customerId, $title, $content)
        {
            $ticketId = $this->insert_item(self::TICKETS_TABLE, [
                'customerId' => $customerId,
                'title' => $title,
                'reference' => $this->generate_ticket_reference()
            ]);

            $this->insert_item(self::TICKET_CONTENT_TABLE, [
                'ticketId' => $ticketId,
                'content' => $content,
                'ownerId' => $customerId,
                'ownerType' => 'customer',  // This can also be staff for responses in the ticket.
                'created' => date('y-m-d H:i:s')
            ]);

            return $ticketId;
        }

        public function delete_ticket($id)
        {
            $this->delete_item_where(self::TICKET_CONTENT_TABLE, array('ticketId' => $id));
            $this->delete_item(self::TICKETS_TABLE, $id);
        }

        public function assign_ticket($id, $wpUserId)
        {
            $this->update_item(self::TICKETS_TABLE, $id,
                array('assignedWpUserId' => $wpUserId, 'updated' => date('y-m-d H:i:s')));
        }

        public function update_ticket_status($id, $status)
        {
            $this->update_item(self::TICKETS_TABLE, $id,
                array('status' => $status, 'updated' => date('y-m-d H:i:s')));
        }

        public function close_ticket($id)
        {
            $this->update_item(self::TICKETS_TABLE, $id,
                array('closed' => '1', 'updated' => date('y-m-d H:i:s')));
        }

        public function open_ticket($id)
        {
            $this->update_item(self::TICKETS_TABLE, $id,
                array('closed' => '0', 'updated' => date('y-m-d H:i:s')));
        }

        public function get_ticket($id)
        {
            return $this->get_item(self::TICKETS_TABLE, $id);
        }

        public function get_ticket_content($ticketId, $desc = false)
        {
            $where = "ticketId = $ticketId";
            if ($desc)
                $where .= " ORDER BY created DESC";
            return $this->get_items_where(self::TICKET_CONTENT_TABLE, $where);
        }

        public function get_full_ticket($id)
        {
            $ticket = $this->get_item(self::TICKETS_TABLE, $id);
            if ($ticket)
            {
                $ticket->content = $this->get_ticket_content($id);
            }
            return $ticket;
        }

        public function get_full_ticket_by_reference($reference)
        {
            global $wpdb;
            $ticketTable = $wpdb->prefix . self::TICKETS_TABLE;
            $sql = "SELECT * FROM $ticketTable WHERE reference='$reference' ";
            $ticket = $wpdb->get_row($sql);
            if ($ticket)
            {
                $ticket->content = $this->get_ticket_content($ticket->id, true);
            }

            return $ticket;
        }

        public function get_full_ticket_by_owner_reference($customerWpId, $reference)
        {
            global $wpdb;
            $ticketTable = $wpdb->prefix . self::TICKETS_TABLE;
            $customerTable = $wpdb->prefix . self::CUSTOMERS_TABLE;
            $sql = "SELECT $ticketTable.* FROM $ticketTable JOIN $customerTable ON $customerTable.id = $ticketTable.customerId" .
                " WHERE reference='$reference' AND $customerTable.wpUserId = $customerWpId";
            $ticket = $wpdb->get_row($sql);
            if ($ticket)
            {
                $ticket->content = $this->get_ticket_content($ticket->id, true);
            }

            return $ticket;
        }

        public function add_content_to_ticket($data)
        {
            $this->insert_item(self::TICKET_CONTENT_TABLE, $data);
        }

        public function get_latest_tickets($count)
        {
            return $this->get_items_where(self::TICKETS_TABLE, "closed=0 ORDER BY created DESC", $count, 0);
        }

        public function get_unresolved_tickets($count)
        {
            return $this->get_items_where(self::TICKETS_TABLE, "status='unresolved' ORDER BY created ASC", $count, 0);
        }

        public function get_all_unresolved_tickets()
        {
            return $this->get_items_where(self::TICKETS_TABLE, "status='unresolved' ORDER BY created ASC");
        }

        public function get_total_tickets()
        {
            global $wpdb;
            $total = 0;
            $ticketTable = $wpdb->prefix . self::TICKETS_TABLE;
            $sql = "SELECT COUNT(*) as total FROM $ticketTable";
            $result = $wpdb->get_row($sql);
            if ($result)
            {
                $total = $result->total;
            }

            return $total;
        }

        /**
         * If the last response to the ticket is from the customer, this ticket needs a response.
         *
         * @param $id
         * @return bool
         */
        public function get_ticket_need_response($id)
        {
            global $wpdb;
            $needResponse = false;
            $ticketTable = $wpdb->prefix . self::TICKETS_TABLE;
            $contentTable = $wpdb->prefix . self::TICKET_CONTENT_TABLE;

            $sql = "SELECT $contentTable.created, $contentTable.ownerType FROM $contentTable " .
                "INNER JOIN $ticketTable on $contentTable.ticketId = $ticketTable.id " .
                "WHERE $contentTable.ticketID = $id AND $ticketTable.closed = 0 " .
                "ORDER BY $contentTable.created DESC LIMIT 1";

            $result = $wpdb->get_row($sql);
            if ($result)
            {
                if ($result->ownerType == 'customer')
                    $needResponse = true;
            }

            return $needResponse;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////


    }//end class QuipSupportDatabase
}