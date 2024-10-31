<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'open';
$agents = get_users(array('role' => 'qs_customer_service'));
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/>
    <h1>Tickets
        <?php if (isset($_GET['s'])): ?>
            <span class="subtitle">Search results for "<?php echo sanitize_text_field($_GET['s']); ?>"</span>
        <?php endif; ?>
    </h1>
    <div id="updateDiv" style="display:none;"></div>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-support-all-tickets&tab=open" class="nav-tab <?php echo $active_tab == 'open' ? 'nav-tab-active' : ''; ?>"><?php _e('Open Tickets', 'quip-support'); ?></a>
        <a href="?page=quip-support-all-tickets&tab=closed" class="nav-tab <?php echo $active_tab == 'closed' ? 'nav-tab-active' : ''; ?>"><?php _e('Closed Tickets', 'quip-support'); ?></a>
    </h2>
    <div class="tab-content">
        <form id="searchTicketsForm" method="get" action="" style="padding-top: 10px;">
            <p class="search-box">
                <input type="hidden" name="page" value="quip-support-all-tickets"/>
                <input type="hidden" name="tab" value="<?php echo $active_tab; ?>"/>
                <label class="screen-reader-text" for="post-search-input">Search Tickets:</label>
                <input type="search" id="post-search-input" name="s" value="">
                <input type="submit" id="search-submit" class="button" value="Search Tickets">
            </p>
        </form>
        <?php if ($active_tab == 'open'): ?>
            <div class="qu-list-table">
                <?php $table->display(); ?>
            </div>
        <?php elseif ($active_tab == 'closed'): ?>
            <div class="qu-list-table">
                <?php $table->display(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- dialog -->
<div id="assignTicketDialog" title="<?php _e('Assign Ticket', 'quip-support'); ?>" style="display:none;">
    <label for="assignTicketUser">Select User: </label>
    <select name="assignTicketUser" id="assignTicketUser">
        <?php foreach ($agents as $a): ?>
            <option value="<?php echo $a->ID; ?>"><?php echo get_the_author_meta('display_name', $a->ID) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="deleteTicketDialog" title="<?php _e('Delete Ticket?', 'quip-support'); ?>" style="display:none;">
    <p><?php _e('This will delete this ticket. Are you sure?', 'quip-support'); ?></p>
</div>