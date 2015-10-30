<div class="wrap">  
<?php include("seatt_header.php"); ?>
          <?php    echo "<h2>" . __( 'Simple Event Attendance - Add Event', 'seatt_trdom' ) . "</h2>";
		  // Process form if sent
          if(isset($_POST['seatt_name'])) {
			$_POST = stripslashes_deep($_POST);  
			$event_name = $_POST['seatt_name'];
			$event_desc = $_POST['seatt_desc'];
			$event_limit = $_POST['seatt_limit'];
			$event_start = strtotime($_POST['seatt_start']);
			$event_expire = strtotime($_POST['seatt_expire']);
			
			global $wpdb;
			$wpdb->insert($wpdb->prefix.'seatt_events', array( 'event_name' => $event_name, 'event_desc' => $event_desc, 'event_limit' => $event_limit, 'event_start' => $event_start, 'event_expire' => $event_expire, 'event_status' => 1, 'event_reserves' => 0 ) );
			?>
            <div class="updated"><p><strong><?php _e('Event "'.$event_name.'" added.' ); ?></strong></p></div>
            <?php
		  }
			  ?>
		  <p>Please fill in the details below to add an event.</p>
		  <form name="seatt_add_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		    <p>Event Name<br>
		      <label for="seatt_name"></label>
		    <input name="seatt_name" type="text" id="seatt_name" size="50" maxlength="150">
            </p>
		    <p>Event Description<br>
		      <label for="seatt_desc"></label>
		      <input name="seatt_desc" type="text" id="seatt_desc" size="80" maxlength="150">
		    </p>
		    <p>Attendee Limit (enter 0 for no limit)<br>
		      <label for="seatt_limit"></label>
		      <input name="seatt_limit" type="text" id="seatt_limit" size="14" maxlength="8" value="0">
		    </p>
		    <p>Opening Registration Date &amp; Time (set to the past to open immediately)<br />
              <input name="seatt_start" type="text" id="seatt_start" value="<?php echo date("d-m-Y H:i", current_time('timestamp')); ?>" />
eg the date/time is now <?php echo date("d-m-Y H:i", current_time('timestamp')); ?></p>
            <p>Closing Registration Date &amp; Time<br />
              <input name="seatt_expire" type="text" id="seatt_expire" value="<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>" />
              eg '<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>' (a week from now)</p>
		    <p>
		      <input type="submit" name="Submit" value="<?php _e('Add Event', 'seatt_trdom' ) ?>" />
		    </p>
  </form>
		  <p>&nbsp;</p>
          
          </div>