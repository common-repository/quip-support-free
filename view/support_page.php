<?php
$unresolved = count(QuipSupport::getInstance()->db->get_all_unresolved_tickets());
$totalTickets = QuipSupport::getInstance()->db->get_total_tickets();
$resolved = $totalTickets - $unresolved;
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/>
    <div id="updateDiv" style="display:none;"></div>
    <div class="mm-bootstrap-wrapper" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body" style="text-align: center;">
                        <h4><?php echo $resolved ?> <br/> Solved Problems</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body" style="text-align: center;">
                        <h4><?php echo $unresolved ?> <br/>Unresolved Tickets</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body" style="text-align: center;">
                        <h4><?php echo QuipSupport::getInstance()->db->get_total_customers() ?> <br/>Customers Helped</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Latest Tickets</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo QuipSupportHtml::latest_ticket_list() ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Outstanding Tickets</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo QuipSupportHtml::outstanding_ticket_list() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
