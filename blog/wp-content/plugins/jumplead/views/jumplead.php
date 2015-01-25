<div class="wrap jumplead jumplead-splash">

<?php
include(JUMPLEAD_PATH_VIEW . 'includes/message.php');
?>

    <header>
        <img id="jumplead_logo" src="<?php echo Jumplead::$path; ?>/assets/robot-white.png" />

        <div class="links">
<?php
if (!get_option('jumplead_tracker_id', null)) {
?>
            <a href="http://jumplead.com/join-us" target="_blank">Create Free Account</a>
<?php
}
?>
            <a href="http://app.jumplead.com" target="_blank">Login</a>
        </div>

        <br class="clear" />

        <h2>Jumplead</h2>
        <h3>Inbound Marketing Automation</h3>
        <br class="clear" />

        <img src="http://jumplead.com/i/home-montage.png" alt="Jumplead pages">
    </header>

    <section>
        <h4>Identify, convert and nurture your visitors into customers</h4>

        <div class="videobox">
            <div class="jumpbot">
                <img src="http://jumplead.com/i/robot-666.png" />
            </div>


            <br class="clear" />

            <div class="video">
                <iframe src="//fast.wistia.net/embed/iframe/tdtex2rfy8"
                    allowtransparency="true" frameborder="0" scrolling="no"
                    allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen>
                </iframe>
            </div>
        </div>

        <h4>All-in-one software for inbound marketing</h4>

        <div class="grid">
            <div class="feature">
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/visitor-identification" target="_blank">Identify, chat and connect.</a>
                    </h2>
            		<h3>Prospect Identification.</h3>
            	</hgroup>

            	<a href="http://jumplead.com/features/visitor-identification" target="_blank">
            	    <img src="http://jumplead.com/i/feature-grid-identify.jpg" alt="Visitor Identification">
            	</a>

            	<p>Identify and connect with prospects while they are active on your website; be in the right place at the right time.</p>
            </div>

            <div class="clear"></div>

            <div class="feature">
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/landing-pages" target="_blank">Convert visitors to leads.</a>
                    </h2>
            		<h3>Conversion Forms and Pages.</h3>
            	</hgroup>

            	<a href="http://jumplead.com/features/landing-pages" target="_blank">
            	    <img src="http://jumplead.com/i/feature-grid-convert.jpg" alt="Landing Pages">
                </a>

            	<p>Capture and profile your leads across web forms. Create, manage and score them automatically.</p>
            </div>

            <div class="clear"></div>

            <div class="feature" >
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/automations" target="_blank">Trigger targeted marketing.</a>
            	   </h2>
                   <h3>Marketing Automation.</h3>
            	</hgroup>

        	    <a href="http://jumplead.com/features/automations" target="_blank">
        	        <img src="http://jumplead.com/i/feature-grid-automation.jpg" alt="Marketing Automations">
                </a>

            	<p>Use lead activity to automate sales team notifications, nurture autoresponders and lifecycle stage changes.</p>
            </div>

            <div class="clear"></div>

            <div class="feature">
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/email-marketing" target="_blank">Nurture leads with email.</a>
                    </h2>
                    <h3>Broadcasts and Autoresponders.</h3>
            	</hgroup>

                <a href="http://jumplead.com/features/email-marketing" target="_blank">
            	    <img src="http://jumplead.com/i/feature-grid-email.jpg" alt="Email Campaigns">
            	</a>

            	<p>Nurture leads through sales funnel stages based upon their individual activity; by sales stage or custom tags.</p>
            </div>

            <div class="clear"></div>

            <div class="feature" >
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/contacts" target="_blank">Score and manage leads.</a>
                    </h2>
                    <h3>Contacts and Lifecycle Stages.</h3>
                </hgroup>

            	<a href="http://jumplead.com/features/contacts" target="_blank">
                	<img src="http://jumplead.com/i/feature-grid-contacts.jpg" alt="Contact Management CRM">
            	</a>

            	<p>Contact profiles that are kept up to date by the information gathered from web forms, and scored for engagement.</p>
            </div>

            <div class="clear"></div>

            <div class="feature">
            	<hgroup>
            	    <h2>
            	        <a href="http://jumplead.com/features/analytics" target="_blank">Track and improve performance.</a>
            	   </h2>
            	   <h3 >Marketing Analytics.</h3>
                </hgroup>

                <a href="http://jumplead.com/features/analytics" target="_blank">
                    <img src="http://jumplead.com/i/feature-grid-analytics.jpg" alt="Analytics">
                </a>

            	<p>Monitor conversion rates, traffic and campaigns. Track your search results positions for important phrases.</p>
            </div>
        </div>

        <br class="clear" />
    </section>

<?php
include(JUMPLEAD_PATH_VIEW . 'includes/footer.php');
?>
