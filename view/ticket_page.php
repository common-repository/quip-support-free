<?php
$options = get_option('quip_support_options');
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/>
    <h1>Ticket Details</h1>
    <div id="updateDiv" style="display:none;"></div>
    <div class="mm-bootstrap-wrapper" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <div class="qs-ticket-content-simple">
                    <div class="list-group">
                        <div class="list-group-item">
                            <h3><?php echo stripslashes($ticket->title) ?>
                                <div class="pull-right">
                                    <?php echo QuipSupportHtml::ticket_status_label($ticket->status) ?>
                                    <?php if ($ticket->closed): ?>
                                        <span class='label label-default'>Closed</span>
                                    <?php else: ?>
                                        <?php echo QuipSupportHtml::time_ago_label($ticket->created); ?>
                                    <?php endif; ?>
                                </div>
                            </h3>
                        </div>
                        <?php foreach ($ticket->content as $c): ?>
                            <div class="qs-ticket-content-item list-group-item">
                                <div class="row">
                                    <div class="col-md-3 qs-message-header">
                                        <?php echo QuipSupportHtml::ticket_content_header($ticket, $c); ?>
                                    </div>
                                    <div class="col-md-9 qs-message-body">
                                        <p class="lead qs-message-text"><?php echo stripslashes(base64_decode($c->content)) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="qs-ticket-reply list-group-item">
                            <form id="ticketReplyForm" action="" method="POST">
                                <input type="hidden" name="action" value="quip_support_ticket_reply"/>
                                <input type="hidden" name="ticketId" value="<?php echo $ticket->id ?>"/>
                                <div class="form-group">
                                    <button class="btn btn-default" id="templatePickerButton">Open Template Picker</button>
                                </div>
                                <div class="form-group">
                                    <?php wp_editor('', 'ticketContent', array('media_buttons' => false, 'teeny' => true)); ?>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Ticket Actions</h3>
                    </div>
                    <div class="panel-body">
                        <div class="qs-ticket-actions" style="padding-bottom: 10px;">
                            <div class="col-md-2">
                                <?php if ($ticket->closed): ?>
                                    <form id="ticketOpenForm" action="" method="POST">
                                        <input type="hidden" name="action" value="quip_support_ticket_open"/>
                                        <input type="hidden" name="ticketId" value="<?php echo $ticket->id ?>"/>
                                        <button class="btn btn-warning" type="submit">Open Ticket</button>
                                    </form>
                                <?php else: ?>
                                    <form id="ticketCloseForm" action="" method="POST">
                                        <input type="hidden" name="action" value="quip_support_ticket_close"/>
                                        <input type="hidden" name="ticketId" value="<?php echo $ticket->id ?>"/>
                                        <button class="btn btn-warning" type="submit">Close Ticket</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <form id="ticketUpdateStatusForm" action="" method="POST" class="form-inline">
                                    <input type="hidden" name="action" value="quip_support_ticket_update_status"/>
                                    <input type="hidden" name="ticketId" value="<?php echo $ticket->id ?>"/>
                                    <div class="form-group">
                                        <label for="updateStatus" class="form-label">Update Status:</label>
                                        <select id="updateStatus" name="updateStatus" class="form-control">
                                            <option value="unresolved" <?php echo ($ticket->status == 'unresolved') ? 'selected' : '' ?> >Unresolved</option>
                                            <option value="ongoing" <?php echo ($ticket->status == 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                                            <option value="resolved" <?php echo ($ticket->status == 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Audit Log</h3>
                        </div>
                        <div class="panel-body">
                            <p>In the premium edition, an audit log is included to keep track of every action relating to tickets.
                                <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a></p>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>
<!-- dialog -->
<div id="templatePickerDialog" title="<?php _e('Select Template', 'quip-support'); ?>" style="display:none;">
    <p>Sorry, this feature is only available in the premium edition.
        <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a></p>
</div>