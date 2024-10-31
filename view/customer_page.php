<?php
$tickets = QuipSupport::getInstance()->db->get_customer_tickets($customer->id);
$wpUser = get_user_by('id', $customer->wpUserId);
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/>
    <h1>Customer Details</h1>
    <div id="updateDiv" style="display:none;"></div>
    <div style="padding-right: 5px;"><?php echo get_avatar($customer->wpUserId) ?></div>
    <h2>Customer ID: <?php echo $customer->id ?><br/>
        WordPress User:
        <a href="<?php echo admin_url("user-edit.php?user_id=" . $customer->wpUserId) ?>"><?php echo $wpUser->display_name ?></a><br/>
        <a href="mailto:<?php echo $customer->email ?>"><?php echo $customer->email ?></a></h2>
    <h4>First requested support <?php echo date('H:i F jS Y', strtotime($customer->created)) ?></h4>

    <h3> Tickets</h3>
    <table class="widefat">
        <thead>
        <tr>
            <th> Reference</th>
            <th> Title</th>
            <th> Status</th>
            <th> Date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tickets as $t): ?>
            <tr>
                <td>
                    <a href="<?php echo admin_url('admin.php?page=quip-support-ticket&id=' . $t->id) ?>"><?php echo $t->reference ?></a>
                </td>
                <td>
                    <a href="<?php echo admin_url('admin.php?page=quip-support-ticket&id=' . $t->id) ?>"><?php echo stripslashes($t->title) ?></a>
                </td>
                <td><?php echo ucfirst($t->status) ?></td>
                <td><?php echo date('H:i F jS Y', strtotime($t->created)) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Surveys</h3>
    <p>Quip Support premium edition includes customer satisfaction survey history here. <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a></p>
</div>