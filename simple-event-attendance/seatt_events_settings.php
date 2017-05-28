<?php
// seatt_events_settings.php
// Purpose: Template for the SEATT settings page (not currently implemented)

global $wpdb; ?>
<div class="wrap">
<?php include("seatt_header.php"); ?>
          <?php    echo "<h2>" . __( 'Simple Event Attendance Settings', 'seatt_trdom' ) . "</h2>"; ?>
<?php

// Check for update settings (not currently in use)
if (isset($_POST['seatt_hidden'])) {
	$seatt_hidden = $_POST['seatt_hidden'];
} else {
	$seatt_hidden = 'N';
}
if	($seatt_hidden == 'Y') {
	//Form data sent
	// $data = $_POST['seatt_data'];
	//update_option('seatt_dbhost', $dbhost);
	?>
	<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
	<?php
	}

// Page content start
?>
<p>This options page is due in an upcoming release.</p>

<form method="POST" action="">
<?php settings_fields('seatt_events_settings');	//pass slug name of page, also referred
                                        //to in Settings API as option group name
do_settings_sections( 'seatt_events_settings' ); 	//pass slug name of page
submit_button();
?>
</form>

<!--
To come.

    <h3>Options:</h3>

  <p>Attendees visible to all?</p>

        <input type="submit" name="Submit" value="<?php _e('Update Options', 'seatt_trdom' ) ?>" />

-->

</div>
