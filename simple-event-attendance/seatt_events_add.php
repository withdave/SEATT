<?php
// seatt_events_add.php
// Purpose: Template for the add event SEATT settings page
?>

<div class="wrap">
<?php include("seatt_header.php"); ?>
          <?php    echo "<h2>" . __( 'Simple Event Attendance - Add Event', 'seatt_trdom' ) . "</h2>";
		  // Process form if sent
          if(isset($_POST['seatt_name'])) {
			$_POST = stripslashes_deep($_POST);
			$event_name = sanitize_text_field($_POST['seatt_name']);
			$event_desc = wp_kses_post($_POST['seatt_desc']);
			$event_limit = intval($_POST['seatt_limit']);
			$event_start = strtotime($_POST['seatt_start']);
			$event_expire = strtotime($_POST['seatt_expire']);

			// Ensure required fields contain values, insert if true
			if ((strlen(trim($event_name)) > 0) &&
				($event_start) &&
				($event_expire)) {

				global $wpdb;
				$wpdb->insert($wpdb->prefix.'seatt_events', array( 'event_name' => $event_name, 'event_desc' => $event_desc, 'event_limit' => $event_limit, 'event_start' => $event_start, 'event_expire' => $event_expire, 'event_status' => 1, 'event_reserves' => 0 ), array('%s', '%s', '%d', '%s', '%s', '%d', '%d') );
				?>
				<div class="updated">
                	<p><strong>Event <?php echo esc_html($event_name); ?> added.</strong></p>
                </div>
				<?php

			  } else {

				// If required fields missing
			  	?>
                <div class="error">
                	<p><strong>Please ensure that all required fields contain values, and that dates are in a valid format.</strong></p>
                </div>
                <?php
			}
		  }
			  ?>
		  <p>Please fill in the details below to add an event.</p>
		  <form name="seatt_add_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		    <p>Event Name*<br>
		      <label for="seatt_name"></label>
		    <input name="seatt_name" type="text" id="seatt_name" size="50" maxlength="150">
            </p>
		    <p>Event Description<br>
            <?php
            // Open in WP editor
            $content = '';
            $editor_id = 'seatt_desc';

            wp_editor( $content, $editor_id, array( 'media_buttons' => true, 'wpautop' => true, 'textarea_rows' => 5 ) );
            ?>
            </p>
		    <p>Attendee Limit (enter 0 for no limit)*<br>
		      <label for="seatt_limit"></label>
		      <input name="seatt_limit" type="text" id="seatt_limit" size="14" maxlength="8" value="0">
		    </p>
		    <p>Opening Registration Date &amp; Time*<br />
              <input name="seatt_start" type="text" id="seatt_start" value="<?php echo date("d-m-Y H:i", current_time('timestamp')); ?>" />
eg the server date/time is currently '<a onclick="document.getElementById('seatt_start').value='<?php echo date("d-m-Y H:i", current_time('timestamp')); ?>';"><?php echo date("d-m-Y H:i", current_time('timestamp')); ?></a>' (dd-mm-yyyy hh:mm)</p>
            <p>Closing Registration Date &amp; Time*<br />
              <input name="seatt_expire" type="text" id="seatt_expire" value="<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>" />
              eg a week from now is '<a onclick="document.getElementById('seatt_expire').value='<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>';"><?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?></a>' (dd-mm-yyyy hh:mm)</p>
            <p>*Required fields</p>
            <p>
		      <input type="submit" name="Submit" value="<?php _e('Add Event', 'seatt_trdom' ) ?>" />
		    </p>
  		  </form>
		  <p>&nbsp;</p>
          </div>
