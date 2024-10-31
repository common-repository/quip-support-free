<div class="wrap about-wrap">
    <h1><img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Support"/> Help</h1>
    <div class="about-text"><?php _e("We're here to help.", 'quip-support'); ?><?php _e('This section contains all you need to know to get started using Quip Support.', 'quip-support'); ?>
        <?php _e("If you need more assistance, please click the support button below to get in touch.", 'quip-support'); ?>
    </div>
    <p>
        <a href="http://quipcode.com/forum" class="button button-primary"><?php _e("Support", 'quip-support'); ?></a>
        <a href="http://quipcode.com" class="button button-primary"><?php _e("Visit our website", 'quip-support'); ?></a>
    </p>
    <div style="padding-top: 20px;"></div>
    <div id="contextual-help-wrap" tabindex="-1">
        <div id="contextual-help-columns">
            <div class="contextual-help-tabs">
                <ul>
                    <li id="tab-link-quick_start" class="active">
                        <a href="#tab-panel-quick_start" aria-controls="tab-panel-quick_start">Quick Start</a>
                    </li>
                    <li id="tab-link-support" class="">
                        <a href="#tab-panel-tickets" aria-controls="tab-panel-glossary">Tickets</a>
                    </li>
                    <li id="tab-link-clients" class="">
                        <a href="#tab-panel-emails" aria-controls="tab-panel-glossary">Emails</a>
                    </li>
                    <li id="tab-link-payments" class="">
                        <a href="#tab-panel-kb" aria-controls="tab-panel-glossary">Knowledge Base</a>
                    </li>
                    <li id="tab-link-settings" class="">
                        <a href="#tab-panel-settings" aria-controls="tab-panel-glossary">Settings</a>
                    </li>
                </ul>
            </div>
            <div class="contextual-help-tabs-wrap" style="background-color: #f6fbfd">
                <div id="tab-panel-quick_start" class="help-tab-content active">
                    <h3 class="title">Quick Start</h3>
                    <p>The following steps are the minimum you need to get started using Quip Support:</p>
                    <ul>
                        <li>Setup your company logo and choose options on the basic settings page.
                            <a href="<?php echo admin_url("admin.php?page=quip-support-settings&tab=basic"); ?>">Basic settings</a>
                        </li>
                        <li>Configure the email templates from the email settings page.
                            <a href="<?php echo admin_url("admin.php?page=quip-support-settings&tab=email"); ?>">Email settings</a>
                        </li>
                        <li>Assign users as Customer Service Agents.
                            <a href="<?php echo admin_url("admin.php?page=quip-support-settings&tab=agents"); ?>">Agent settings</a>
                        </li>
                        <li>Create a new WordPress page to place the
                            <code>[quip_support_form]</code> shortcode on. This displays the "Create Ticket" form.
                        </li>
                        <li>If you want to show a link to the "Create Ticket" page from the Knowledge Base, choose the page in the basic settings.
                            <a href="<?php echo admin_url("admin.php?page=quip-support-settings&tab=basic"); ?>">Basic settings</a>
                        </li>
                        <li>Double check the Knowledge Base page contains the
                            <code>[quip_support_kb]</code> shortcode and that is has the permalink "knowledgebase".
                            This is done automatically on plugin activation but may fail if there are conflicting plugins or pages.
                        </li>
                        <li>That's it! You're now ready to start receiving customer support tickets and displaying knowledge base articles.</li>
                        <li>Remember, if you need any help please get in touch.</li>
                    </ul>
                </div>
                <div id="tab-panel-tickets" class="help-tab-content">
                    <h3 class="title">Tickets</h3>
                    <p>A Ticket is a customer support request and contains all the details sent from the customer who needs help.</p>
                    <p>Tickets are created by the customer from the Create Ticket form. This form can be added to any page with the shortcode:
                        <code>[quip_support_form]</code>.
                        Once the customer creates a ticket it will appear in the Quip Support section of the WordPress dashboard.
                    </p>
                    <p>Tickets in Quip Support have the following components:</p>
                    <ul>
                        <li><strong>Reference</strong>: A unique identifier to easily refer to the ticket</li>
                        <li>
                            <strong>Assignment</strong>: New tickets start as 'Unassigned' and can be assigned to any registered customer service agent.
                            Once assigned, that agent will receive all updates regarding the ticket and the ticket will appear on their 'My Tickets' section.
                        </li>
                        <li><strong>Status</strong>: A ticket can have the following statuses:
                            <ul>
                                <li>
                                    <strong>Unresolved</strong>: New tickets start as unresolved, this means the ticket is not being worked on.
                                </li>
                                <li>
                                    <strong>Ongoing</strong>: This ticket is being worked on by a customer service agent.
                                </li>
                                <li><strong>Resolved</strong>: The ticket has been resolved by customer service.</li>
                            </ul>
                            Ticket statuses can be updated by customer service agents when viewing the ticket details.
                        </li>
                        <li>
                            <strong>Time Taken</strong>: When viewing ticket details the time since the ticket was created is shown.
                        </li>
                        <li>
                            <strong>Ticket Content</strong>: All messages between the customer and the agent are shown in the ticket details.
                            Here you can see all the details of the ticket and also update the status, respond to the customer and close the ticket.
                        </li>
                        <li>
                            <strong>Open & Closed</strong>: New tickets come in as open tickets. When a ticket is complete it can be closed.
                            Closing a ticket will remove it from the list of open tickets and also trigger the customer satisfaction survey if you have
                            enabled that option.
                        </li>
                        <li>
                            <strong>Templates</strong>: When responding to a ticket, the agent may select from a list of previously created templates for a stock response.
                        </li>
                        <li>
                            <strong>Audit Log</strong>: Every action on a ticket is logged in the audit log, from open and closing, responses, assignment and changing status.
                            This log can be viewed on the ticket details page if this option is enabled in the settings.
                        </li>
                    </ul>
                    <h4>Customer Dashboard</h4>
                    <p>When a ticket is created by the customer, a link to the customer dashboard is created using the ticket reference.
                        This customer dashboard is a front-end page that allows the customer to view all details of their ticket.
                        It also allows them to respond to agent replies and will show all the message history for future reference.</p>
                </div>
                <div id="tab-panel-emails" class="help-tab-content">
                    <h3 class="title">Emails</h3>
                    <p>Quip Support automatically sends several emails based upon your settings. You can configure the content of these emails
                        in the Email Settings section and even choose to turn some of them off altogether. The emails sent by Quip Support are:</p>
                    <ul>
                        <li>
                            <strong>Create Ticket</strong>: On ticket creation the customer is sent a confirmation email. This email is mandatory and always sent.
                        </li>
                        <li>
                            <strong>Agent Updated Ticket</strong>: When an agent adds a reply to the ticket, the customer is sent an email. This email is mandatory and always sent.
                        </li>
                        <li>
                            <strong>Customer Updated Ticket</strong>: When the customer adds a reply to the ticket, the agent is sent an email.
                            This email can be turned off in the settings.
                        </li>
                        <li>
                            <strong>Customer Satisfaction Survey </strong>: When a ticket is marked as closed and resolved, an email is sent with links
                            to request feedback from the customer. This email can be turned off in the settings.
                        </li>
                    </ul>
                    <h4>Dynamic Tags</h4>
                    <p>When customizing the email content on the email settings page, you can use special markers called 'Dynamic Tags'.
                        These tags are like placeholders that will be replaced with the relevant content at the time of sending the email.
                        The following tags are available:</p>
                    <ul>
                        <li>For the Create Ticket Email:
                            <ul>
                                <li><code>%%TICKET_TITLE%%</code> - The title of the ticket
                                    <br/>
                                    <code>%%TICKET_LINK%%</code> - A link for the customer to view the ticket details online
                                    <br/>
                                    <code>%%WEBSITE_NAME%%</code> - The name of this website
                                </li>
                            </ul>
                        </li>
                        <li>For the Agent Updated Ticket Email:
                            <ul>
                                <li><code>%%TICKET_TITLE%%</code> - The title of the ticket
                                    <br/>
                                    <code>%%TICKET_LINK%%</code> - A link for the customer to view the ticket details online
                                    <br/>
                                    <code>%%AGENT_NAME%%</code> - The name of the agent who updated the ticket
                                    <br/>
                                    <code>%%TICKET_REPLY%%</code> - The agents reply
                                    <br/>
                                    <code>%%WEBSITE_NAME%%</code> - The name of this website
                                </li>
                            </ul>
                        </li>
                        <li>For the Customer Updated Ticket Email:
                            <ul>
                                <li><code>%%TICKET_TITLE%%</code> - The title of the ticket
                                    <br/>
                                    <code>%%TICKET_LINK%%</code> - A link for the agent to manage the ticket
                                    <br/>
                                    <code>%%TICKET_REPLY%%</code> - The customers reply
                                    <br/>
                                    <code>%%WEBSITE_NAME%%</code> - The name of this website
                                </li>
                            </ul>
                        </li>
                        <li>For the Customer Satisfaction Survey Email:
                            <ul>
                                <li>
                                    <code>%%SURVEY_OPTIONS%%</code> - A choice of options for the customer to rate their experience
                                    <br/>
                                    <code>%%WEBSITE_NAME%%</code> - The name of this website
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <p>NOTE: It is
                        <strong>highly recommended</strong> that if you don't know what you are doing you don't change the email content
                        as using the wrong tags or using invalid code can break the emails.</p>
                    <h4>Testing</h4>
                    <p>Quip Support has an option to send test versions of the emails to the website admin. This allows you to configure the email
                        content and then see exactly what it looks like in an email, before sending it to your customers.<br/>
                        To test the emails, first save your changes in the email settings page, then simply click the "Send Emails" button on that page
                        and the registered admin email will be sent all 4 [TEST] emails.</p>
                    <h4>Troubleshooting</h4>
                    <p>If your emails are not sending, the most likely cause is your web server configuration. Quip Support simply uses the built-in
                        WordPress mailer (<code>wp_mail</code>) so the plugin itself is very unlikely to be the problem.
                        Your web server <strong>must</strong> have sending emails configured, via something like
                        <code>php mail() / sendmail / postfix</code>
                        If you don't understand what this means, please contact your server administrator for help.</p>
                </div>
                <div id="tab-panel-kb" class="help-tab-content">
                    <h3 class="title">Knowledge Base</h3>
                    <p>The Knowledge Base is a great place to create help articles that cover common questions and issues your customers have.
                        Allowing customers to help themselves will save you and your staff lots of time and effort.</p>
                    <p>The Knowledge Base page in Quip Support is automatically created when you activate the plugin and consists of a new
                        WordPress page named "Knowledge Base" which contains the special shortcode
                        <code>[quip_support_kb]</code>.<br/>
                        The Knowledge Base section is made up of the following components:</p>
                    <ul>
                        <li>
                            <strong>Articles</strong>: Articles are similar to WordPress posts and pages and contain a title and content.
                            Articles can be anything from F.A.Q questions and answers to long descriptions on how to use your product.
                        </li>
                        <li>
                            <strong>Topics</strong>: A topic is like a WordPress category and used to group your Articles. Example topics
                            include 'F.A.Q', 'General' and 'Product'.
                        </li>
                    </ul>
                    <h4>Troubleshooting</h4>
                    <p>If the knowledge base failed to install correctly it could be due to a number of things. Firstly, Quip Support expects there
                        not to be another page called "Knowledge Base" during activation. If there is, it will not install the page.</p>
                    <p>Also, certain permalink settings can mean the knowledge base stops working, especially if you change them after installing
                        Quip Support. Quip Support expects the knowledge base to be at the permalink "knowledgebase".
                        <br/>For this website, this means
                        the full URL to the knowledge base Quip Support expects is:
                        <code> <?php echo site_url() . '/knowledgebase' ?></code>. If the
                        knowledge base page is not at this location it will not work.</p>
                    <p>Please note that we are looking into ways of making this more flexible, but for now please ensure the permalinks are setup
                        correctly and the knowledge base page is on a page with the permalink shown above.</p>
                </div>
                <div id="tab-panel-settings" class="help-tab-content">
                    <h3 class="title">Settings</h3>
                    <p>Below is a description of each of the plugin settings to help you configure Quip Support exactly as you want it.</p>
                    <h4>Basic Settings</h4>
                    <ul>
                        <li>
                            <strong>Company Logo</strong>: Here you can upload an image to use as your logo. This will appear on the
                            customer dashboard page.
                        </li>
                        <li>
                            <strong>Show Create Ticket Link on Knowledge Base</strong>: This allows you to show a link to the Create Ticket page
                            directly on the Knowledge Base page. This can help if your user is unable to find any articles to help them and must
                            create a ticket.
                        </li>
                        <li>
                            <strong>Create Ticket Page</strong>: If you choose to show the Create Ticket link on the Knowledge Base page, you must
                            then select the page that contains the Create Ticket shortcode here. This allows Quip Support to display the link correctly.
                        </li>
                        <li>
                            <strong>Show Audit Log</strong>: This option enables the Audit Log view on the ticket details page.
                        </li>
                        <li>
                            <strong>Send Email Notifications To Agents</strong>: You can choose to have Quip Support send emails to your customer service
                            agents registered email address when a customer responds to a ticket they are assigned to.
                            If you choose 'No' here, no emails will be sent and your agents must log into the website and check the Quip Support section for updates.
                        </li>
                        <li>
                            <strong>Send Customer Survey On Ticket Close</strong>: This will send the customer satisfaction survey to customers when a ticket is closed.
                            The ticket must be marked as RESOLVED at the point of closing for the survey to be sent.
                        </li>
                    </ul>
                    <h4>Email Settings</h4>
                    <p>The email settings are described in more detail on the Email section of this help page.</p>
                    <ul>
                        <li><strong>Create Ticket Email Subject</strong>: The subject line for the Create Ticket Email
                        </li>
                        <li><strong>Create Ticket Email</strong>: The content of the Create Ticket Email.</li>
                        <li>
                            <strong>Agent Updated Ticket Email Subject:</strong>: The subject line for the Agent Updated Ticket Email
                        </li>
                        <li><strong>Agent Updated Ticket Email</strong>: The content of the Agent Updated Ticket Email
                        </li>
                        <li>
                            <strong>Customer Updated Email Subject</strong>: The subject line for the Customer Updated Email
                        </li>
                        <li><strong>Customer Updated Ticket Email</strong>: The content of the Customer Updated Email
                        </li>
                        <li>
                            <strong>Customer Satisfaction Survey Email Subject</strong>: The subject line for the Customer Satisfaction Survey Email
                        </li>
                        <li>
                            <strong>Customer Satisfaction Survey Email</strong>: The content of the Customer Satisfaction Survey Email
                        </li>
                        <li>
                            <strong>Send Test Emails</strong>: This button allows you to send test versions of all 4 emails to the
                            WordPress administrator email address. This allows you to see the emails as they would look to customers.
                        </li>
                    </ul>
                    <h4>Agent Settings</h4>
                    <p>Here is where you create new customer service agents. The users must be already registered WordPress users. Adding them here
                        as customer service agents will give them access to Quip Support functionality.</p>
                    <ul>
                        <li>
                            <strong>WordPress User</strong>: Select the registered WordPress user you wish to give access to Quip Support
                        </li>
                    </ul>
                    <p>Note: You can remove access by clicking the 'Remove' link next to the WordPress user name from the list at the bottom of this settings page.</p>
                    <h4>Template Settings</h4>
                    <p>Templates are form or standardized responses you can pre-create that allow your customer service agents to quickly send standard messages to customers.</p>
                    <ul>
                        <li><strong>Template Name</strong>: Create a name for the template for easy recognition</li>
                        <li>
                            <strong>Template Content</strong>: The content of the template response. You can use basic styling similar to when creating WordPress posts and pages.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>