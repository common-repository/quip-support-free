<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'basic';
$options = get_option('quip_support_options');
$user_query = new WP_User_Query(array('orderby' => 'registered', 'order' => 'ASC'));
$agents = get_users(array('role' => 'qs_customer_service'));
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/>
    <div id="updateDiv" style="display:none;"></div>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-support-settings&tab=basic" class="nav-tab <?php echo $active_tab == 'basic' ? 'nav-tab-active' : ''; ?>"><?php _e('Basic', 'quip-support'); ?></a>
        <a href="?page=quip-support-settings&tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php _e('Email', 'quip-support'); ?></a>
        <a href="?page=quip-support-settings&tab=agents" class="nav-tab <?php echo $active_tab == 'agents' ? 'nav-tab-active' : ''; ?>"><?php _e('Agents', 'quip-support'); ?></a>
        <a href="?page=quip-support-settings&tab=templates" class="nav-tab <?php echo $active_tab == 'templates' ? 'nav-tab-active' : ''; ?>"><?php _e('Templates', 'quip-support'); ?></a>
        <?php do_action('quip_support_settings_page_tabs', $active_tab); ?>
    </h2>
    <div class="tab-content">
        <?php if ($active_tab == 'basic'): ?>
            <form action="" method="post" id="quip-support-settings-form">
                <input type="hidden" name="action" value="quip_support_update_settings"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyLogo">Company Logo:</label>
                        </th>
                        <td>
                            <div id="companyLogoImage" style="display:none;">
                                <img id="companyLogoSrc" src="<?php echo $options['companyLogo']; ?>"/>
                            </div>
                            <input id="companyLogo" type="text" name="companyLogo" value="<?php echo $options['companyLogo']; ?>"/>
                            <button id="uploadImageButton" class="button" type="button" value="Upload Image">Upload Image</button>
                            <a href="clear" id="clearLogo">Clear Logo</a>
                            <p class="description">Your logo to be included on the customer service portal, max 300px wide. Leave blank to use website name instead.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="sendNotifications">Send Email Notifications To Agents:</label>
                        </th>
                        <td>
                            <label class="radio">
                                <input type="radio" name="sendNotifications" value="1" <?php echo ($options['sendNotifications'] == 1) ? 'checked="checked"' : '' ?> > <?php _e('Yes', 'quip-support'); ?>
                            </label> <label class="radio">
                                <input type="radio" name="sendNotifications" value="0" <?php echo ($options['sendNotifications'] == 0) ? 'checked="checked"' : '' ?>> <?php _e('No', 'quip-support'); ?>
                            </label>
                            <p class="description">Should we also send emails to customer service agents when customers respond to tickets? If no, agents must log into website to check responses.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="sendNotifications">Premium Edition:</label>
                        </th>
                        <td>
                            <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a>
                            <p class="description">The premium edition contains many more options here including customization of knowledge base and audit logging.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Settings', 'quip-support'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                </p>
            </form>
        <?php elseif ($active_tab == 'email'): ?>
            <form action="" method="post" id="quip-support-email-settings-form">
                <input type="hidden" name="action" value="quip_support_update_settings_email"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="createTicketEmailSubject">Create Ticket Email Subject:</label>
                        </th>
                        <td>
                            <input type="text" id="createTicketEmailSubject" name="createTicketEmailSubject" class="regular-text" value="<?php echo $options['createTicketEmailSubject'] ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="createTicketEmail">Create Ticket Email:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['createTicketEmail'])), 'createTicketEmail', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description">HTML Email content sent to customer on ticket creation. You can use the following dynamic tags:
                                <br/>
                                <code>%%TICKET_TITLE%%</code> - The title of the ticket
                                <br/>
                                <code>%%TICKET_LINK%%</code> - A link for the customer to view the ticket details online
                                <br/>
                                <code>%%WEBSITE_NAME%%</code> - The name of this website
                                <br/>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="agentUpdateTicketEmailSubject">Agent Updated Ticket Email Subject:</label>
                        </th>
                        <td>
                            <input type="text" id="agentUpdateTicketEmailSubject" name="agentUpdateTicketEmailSubject" class="regular-text" value="<?php echo $options['agentUpdateTicketEmailSubject'] ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="agentUpdateTicketEmail">Agent Updated Ticket Email:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['agentUpdateTicketEmail'])), 'agentUpdateTicketEmail', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description">HTML Email content sent to customer when your customer service agent updates the ticket. You can use the following dynamic tags:
                                <br/>
                                <code>%%TICKET_TITLE%%</code> - The title of the ticket
                                <br/>
                                <code>%%TICKET_LINK%%</code> - A link for the customer to view the ticket details online
                                <br/>
                                <code>%%AGENT_NAME%%</code> - The name of the agent who updated the ticket
                                <br/>
                                <code>%%TICKET_REPLY%%</code> - The agents reply
                                <br/>
                                <code>%%WEBSITE_NAME%%</code> - The name of this website
                                <br/>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="customerUpdateTicketEmailSubject">Customer Updated Email Subject:</label>
                        </th>
                        <td>
                            <input type="text" id="customerUpdateTicketEmailSubject" name="customerUpdateTicketEmailSubject" class="regular-text" value="<?php echo $options['customerUpdateTicketEmailSubject'] ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="customerUpdateTicketEmail">Customer Updated Ticket Email:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['customerUpdateTicketEmail'])), 'customerUpdateTicketEmail', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description">HTML Email content sent to customer service agent when the customer updates their assigned ticket. You can use the following dynamic tags:
                                <br/>
                                <code>%%TICKET_TITLE%%</code> - The title of the ticket
                                <br/>
                                <code>%%TICKET_LINK%%</code> - A link for the agent to manage the ticket
                                <br/>
                                <code>%%TICKET_REPLY%%</code> - The customers reply
                                <br/>
                                <code>%%WEBSITE_NAME%%</code> - The name of this website
                                <br/>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="sendNotifications">Premium Edition:</label>
                        </th>
                        <td>
                            <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a>
                            <p class="description">The premium edition contains other email types plus the ability to send test emails.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Save Settings</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                </p>
            </form>
        <?php elseif ($active_tab == 'agents'): ?>
            <h3>Assign users to Customer Service Support roles</h3>
            <p>Choose which of your registered users you would like to assign as "Customer Service Agents".
                Only those users assigned will be able to view & respond to customer service tickets.</p>
            <form action="" method="post" id="quip-support-create-agent-form">
                <input type="hidden" name="action" value="quip_support_update_settings_agents"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="wpUserId">WordPress User:</label>
                        </th>
                        <td>
                            <select name="wpUserId">
                                <?php foreach ($user_query->results as $wpUser): ?>
                                    <option value="<?php echo $wpUser->ID; ?>"><?php echo $wpUser->display_name . ' (' . $wpUser->user_email . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">This user will be given a customer service role.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button class="button button-primary" type="submit">Create Customer Service User</button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading" style="display: none;"/>
                </p>
            </form>
            <hr/>
            <h3>Customer Service Agents</h3>
            <ol>
                <?php foreach ($agents as $q): ?>
                    <li class="user clearfix">
                        <h4 class="user-name">
                            <?php echo get_the_author_meta('display_name', $q->ID); ?>
                            <a href="remove" data-id="<?php echo $q->ID ?>" class="removeAgent" style="padding-left: 10px;">Remove</a>
                        </h4>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php elseif ($active_tab == 'templates'): ?>
            <h3 class="title">Response Templates</h3>
            <p>Use this form to create new response templates for use with the template picker. Templates are form responses
                to your customers, allowing your customer service agents to select standard replies quickly and easily.</p>
            <p>Sorry, this feature is only available in the premium edition.
                <a href="http://codecanyon.net/item/quip-support-ultimate-help-desk-solution/15641176?tag=QuipCode" class="button button-primary">Buy Quip Support</a>
            </p>
        <?php endif; ?>
        <?php do_action('quip_support_settings_page_tab_content', $active_tab); ?>
    </div>
</div>
