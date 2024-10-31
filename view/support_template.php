<div id="quip-support-section">
    <?php if (!is_user_logged_in()): ?>
        <h1>Customer Support</h1>
        <p>Please login to create a support ticket.</p>
        <div>
            <h3>Login</h3>
            <?php wp_login_form() ?>
        </div>
    <?php else: ?>
        <?php $user = wp_get_current_user(); ?>
        <div class="" style="display:none" id="quip-support-message">
        </div>
        <form action="" method="POST" id="createSupportTicketForm" class="qs-form">
            <input type="hidden" name="action" value="quip_support_create_ticket"/>
            <p>
                <label for="title">How can we help?</label>
                <input id="title" name="title" type="text" placeholder="Type the subject of your issue here..." class="rounded"/>
            </p>
            <p>
                <label for="details">Describe the issue you need help with:</label>
                <textarea id="details" name="details" rows="10" cols="30" class="rounded"></textarea>
            </p>
            <p>
                You will receive an email confirmation at your registered email address:
                <strong><?php echo $user->user_email; ?></strong>
            </p>
            <p>
                <button type="submit">Submit</button>
                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
            </p>
        </form>
    <?php endif; ?>
</div>

