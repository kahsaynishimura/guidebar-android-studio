<?php
include(JUMPLEAD_PATH_VIEW . 'includes/header.php');
?>

	<form name="jumplead_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h3>Tracking</h3>
        <p>
            Once you've entered your Tracker ID, we'll automatically embed the Jumplead tracking code on every page.
        </p>

		<table class="form-table">
			<tbody>
				<tr>
					<th>
					    <label for="jumplead_tracker_id">Tracker ID</label><br />
					</th>
					<td>
						<input
						    type="text" id="tracker_id" name="tracker_id" class="regular-text"
						    value="<?php echo $tracker_id ?>" size="20"
                        >
						<p class="description">
						    <a href="https://app.jumplead.com/settings/tracking-code" target="_blank">Get your tracking code</a>
						</p>
					</td>
				</tr>
            </tbody>
		</table>

        <hr />

        <h3>Adding Web Conversion Forms</h3>
        <p>
            Conversion Forms can be embedded on any page using the Jumplead short code.
        </p>
        <h4>Using the Short Code</h4>
        <ol>
            <li>Login to your <a href="https://app.jumplead.com" target="_blank">Jumplead account</a></li>
            <li>Select the <a href="https://app.jumplead.com/conversion/forms" target="_blank">Conversion Form</a> you wish to embed</li>
            <li>Click Generate Form in the Form builder</li>
            <li>Copy the short code</li>
            <li>Paste into any page</li>
        </ol>

        <hr />

        <h3>Create Contacts from Comments</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
					    <label for="jumplead_contacts_from_comments">
					        Enable
					    </label>
					</th>
					<td>
                        <fieldset>
                            <label for="capture_comments">
                                <input name="capture_comments" type="checkbox" value="1"
                                <?php
                                if ($capture_comments) {
                                    echo 'checked="checked"';
                                }
                                ?>
                                >
                            </label>
                        </fieldset>
					</td>
				</tr>
            </tbody>
		</table>

        <hr />

		<p class="submit">
		    <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
		</p>
	</form>
<?php
include(JUMPLEAD_PATH_VIEW . 'includes/footer.php');
?>
