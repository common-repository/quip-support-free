<?php

class QuipSupportTableTickets extends WP_List_Table
{
    private $tableType;
    private $assigned;

    function __construct($type = 'open', $assigned = null)
    {
        parent::__construct(array(
            'singular' => __('Support', 'quip-support'), //Singular label
            'plural' => __('Supports', 'quip-support'), //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));

        $this->tableType = $type;
        $this->assigned = $assigned;
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav($which)
    {
        if ($which == "top")
        {
            //The code that goes before the table is here
            echo '<div class="wrap">';
        }
        if ($which == "bottom")
        {
            //The code that goes after the table is there
            echo '</div>';
        }
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns()
    {
        return $columns = array(
            'reference' => __('Ticket Reference', 'quip-support'),
            'title' => __('Title', 'quip-support'),
            'customer' => __('Customer', 'quip-support'),
            'assigned' => __('Assigned To', 'quip-support'),
            'status' => __('Status', 'quip-support'),
            'created' => __('Date', 'quip-support'),
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns()
    {
        return $sortable = array(
            'assigned' => array('assignedWpUserId', false),
            'customer' => array('customerId', false),
            'created' => array('created', false),
            'status' => array('status', false)
        );
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items()
    {
        global $wpdb;

        // Preparing your query
        $query = "SELECT * FROM " . $wpdb->prefix . "qs_tickets";

        if ($this->tableType == 'open')
        {
            $query .= " WHERE closed = 0";
        }
        else // closed
        {
            $query .= " WHERE closed = 1";
        }

        // check if we are only looking for tickets assigned to user
        if (isset($this->assigned))
        {
            $query .= " AND assignedWpUserId = " . $this->assigned;
        }

        // check if we are searching too
        if (!empty($_GET['s']))
        {
            $search = sanitize_text_field($_GET['s']);
            $query .= " AND (title LIKE '%$search%' OR reference LIKE '%$search%')";
        }

        //Parameters that are going to be used to order the result
        $orderby = !empty($_REQUEST["orderby"]) ? esc_sql($_REQUEST["orderby"]) : 'ASC';
        $order = !empty($_REQUEST["order"]) ? esc_sql($_REQUEST["order"]) : '';
        if (!empty($orderby) && !empty($order))
        {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }
        else  //Default to order this Tickets table by Created date
        {
            $query .= ' ORDER BY created ASC';
        }

        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 10;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0)
        {
            $paged = 1;
        }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage))
        {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
        }

        // Register the pagination
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        //Register the Columns
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Fetch the items
        $this->items = $wpdb->get_results($query);
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows()
    {
        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list($columns, $hidden) = $this->get_column_info();

        //Loop for each record
        if (!empty($records))
        {
            foreach ($records as $rec)
            {
                //Open the line
                echo '<tr id="record_' . $rec->id . '">';
                foreach ($columns as $column_name => $column_display_name)
                {
                    //Style attributes for each col
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if (in_array($column_name, $hidden)) $style = ' style="display:none;"';
                    $attributes = $class . $style;

                    //Display the cell
                    switch ($column_name)
                    {
                        case "reference":
                            $attention = QuipSupport::getInstance()->db->get_ticket_need_response($rec->id);
                            $row = '<td ' . $attributes . '><strong><a href="' . admin_url('admin.php?page=quip-support-ticket&id=' . $rec->id) . '" >' . $rec->reference . '</a></strong>';
                            if ($attention)
                            {
                                $row .= " <img src='" . plugins_url('../img/attention.png', dirname(__FILE__)) . "' alt='attention' width='16' height='16' title='Customer needs response' />";
                            }
                            $row .= '<div class="row-actions visible">';
                            $row .= '<span><a href="assign" data-id="' . $rec->id . '" class="assign-ticket">' . __('Assign', 'quip-support') . '</a> | </span>';
                            $row .= '<span class="delete" ><a href="delete" data-id="' . $rec->id . '" class="delete-ticket">' . __('Delete', 'quip-support') . '</a></span>';
                            $row .= '</div>';
                            $row .= '</td>';
                            echo $row;
                            break;
                        case "title":
                            $row = '<td ' . $attributes . '><strong><a href="' . admin_url('admin.php?page=quip-support-ticket&id=' . $rec->id) . '" >' . stripslashes($rec->title) . '</a></strong></td>';
                            echo $row;
                            break;
                        case "customer":
                            $customer = QuipSupport::getInstance()->db->get_customer($rec->customerId);
                            echo '<td ' . $attributes . '>' .
                                '<a href="' . admin_url('admin.php?page=quip-support-customer&id=' . $rec->customerId) . '">' .
                                '<div style="padding-right: 5px;">' . get_avatar($customer->wpUserId, 32) . '</div>' .
                                $customer->email . '</a></td>';
                            break;
                        case "assigned":
                            $agent = get_user_by('id', $rec->assignedWpUserId);
                            if ($agent == false)
                                $name = 'Unassigned';
                            else
                                $name = $agent->user_login;
                            echo '<td ' . $attributes . '>' . $name . '</td>';
                            break;
                        case "status":
                            echo '<td ' . $attributes . '>' . ucfirst($rec->status) . '</td>';
                            break;
                        case "created":
                            echo '<td ' . $attributes . '>' . date('H:i F jS Y', strtotime($rec->created)) . '</td>';
                            break;
                    }
                }

                //Close the line
                echo '</tr>';
            }
        }
    }
}
