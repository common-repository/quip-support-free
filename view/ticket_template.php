<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Customer Support Portal', 'quip-support'); ?> | <?php echo get_bloginfo('name') ?> <?php echo wp_title('|', true, 'right'); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
    <meta name="robots" content="noindex, nofollow"/>
    <?php qu_sp_ticket_header(); ?>
</head>
<body style="background-color: #cccccc">
<div class="row" style="padding-top: 20px"></div>
<div class="container" style="background-color: #fff; padding-top: 20px; border-radius: 5px; min-height: 600px;">
    <div class="row" style="display:none" id="error-message">
        <div class="col-md-12">
            <p class="alert alert-danger" id="error-message-text"></p>
        </div>
    </div>
    <div class="row" style="display:none" id="success-message">
        <div class="col-md-12">
            <p class="alert alert-success" id="success-message-text"></p>
        </div>
    </div>
    <div class="row" style="padding-bottom: 20px;">
        <div class="col-md-4">
            <?php if ($options['companyLogo'] != ''): ?>
                <a href="<?php echo site_url() ?>"><img src="<?php echo $options['companyLogo'] ?>" alt="Logo"/></a>
            <?php else: ?>
                <h2><a href="<?php echo site_url() ?>"><?php echo get_bloginfo('name'); ?></a></h2>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="pull-right">
                <h2>Customer Support Portal</h2>
            </div>
        </div>
    </div>
    <?php if (!is_user_logged_in()): ?>
        <p>Please login to view your ticket.</p>
        <div>
            <h3>Login</h3>
            <?php wp_login_form() ?>
        </div>
    <?php else: ?>
        <?php $user = wp_get_current_user(); ?>
        <?php if (!isset($ticket)): ?>
            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md-12">
                    <p>Hi <?php echo QuipSupportHtml::wp_user_full_name_or_email($user) ?>, <br/>
                        <br/><strong>You currently have no tickets with this reference number.</strong>
                        Perhaps you mistyped or clicked the wrong link? Please check your email for the link and try again.
                    </p>
                </div>
            </div>
            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md-12">
                    <a href="<?php echo site_url() ?>" class="btn btn-primary btn-lg">Return to Home Page</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md-12">
                    <p>Hi <?php echo $user->user_email ?>, <br/>
                        <br/>Welcome to our customer support portal. Below you can see your ticket details and also send messages to our support staff.
                    </p>
                    <?php if ($ticket->closed == 1): ?>
                        <div class="alert alert-info" role="alert">
                            This ticket has been closed.
                            <strong><a href="<?php echo site_url() ?>">Back to website</a></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="qs-ticket-content-simple">
                        <h3>Ticket Title: <?php echo stripslashes($ticket->title) ?>
                            <div class="pull-right">
                                <span class="label label-primary"><?php echo ucfirst($ticket->status) ?></span>
                            </div>
                        </h3>
                        <?php if ($ticket->closed != 1): ?>
                            <div class="qs-ticket-reply">
                                <form id="ticketReplyForm" action="" method="POST">
                                    <input type="hidden" name="action" value="quip_support_ticket_customer_reply"/>
                                    <input type="hidden" name="ticketId" value="<?php echo $ticket->id ?>"/>
                                    <div class="form-group">
                                        <label for="ticketContent">Enter your message below:</label>
                                        <textarea id="ticketContent" name="ticketContent" rows="4" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                    <a href="<?php echo site_url() ?>">Back to website</a>
                                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                                </form>
                            </div>
                        <?php endif; ?>
                        <div class="list-group">
                            <div class="list-group-item">
                                <h3>Message History</h3>
                            </div>
                            <?php foreach ($ticket->content as $c): ?>
                                <div class="qs-ticket-content-item list-group-item">
                                    <div class="row">
                                        <div class="col-md-3 qs-message-header">
                                            <?php echo QuipSupportHtml::ticket_content_header_customer($c); ?>
                                        </div>
                                        <div class="col-md-9 qs-message-body">
                                            <p class="lead qs-message-text"><?php echo stripslashes(base64_decode($c->content)) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($ticket->closed != 1): ?>
                <div class="alert alert-info" role="alert">
                    Our support staff aim to respond within <strong>24</strong> to
                    <strong>48</strong> hours but are usually much quicker.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>