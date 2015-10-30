<?php global $wpdb; ?>
<div class="wrap">  
<?php include("seatt_header.php"); ?>
          <?php    echo "<h2>" . __( 'Simple Event Attendance Options', 'seatt_trdom' ) . "</h2>"; ?>  
<?php

// Check for remove event, and remove if present
if ((isset($_GET['event_id'])) && (isset($_GET['remove_event']))) {
	$event_id = (int)$_GET['event_id'];
	$wpdb->query("DELETE FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = $event_id");
	$wpdb->query("DELETE FROM ".$wpdb->prefix."seatt_events WHERE id = $event_id");
	?>  
        <div class="updated"><p><strong><?php _e('Event ' . $event_id . ' deleted.' ); ?></strong></p></div>  
        <?php
}

// Check for duplicate event
if ((isset($_GET['event_id'])) && (isset($_GET['duplicate_event']))) {
	$event_id = (int)$_GET['event_id'];
	
	// Get existing event details
	$event = $wpdb->get_results("SELECT event_name, event_desc, event_limit, event_start, event_expire, event_reserves FROM ".$wpdb->prefix."seatt_events WHERE id = ".$event_id);

	if ($event != NULL) {
	// Create another record
	$wpdb->insert($wpdb->prefix.'seatt_events', array( 'event_name' => $event[0]->event_name, 'event_desc' => $event[0]->event_desc, 'event_limit' => $event[0]->event_limit, 'event_start' => $event[0]->event_start, 'event_expire' => $event[0]->event_expire, 'event_status' => 0, 'event_reserves' => $event[0]->event_reserves ) );
	?>  
        <div class="updated"><p><strong><?php _e('Event ' . $event_id . ' duplicated.' ); ?></strong></p></div>  
        <?php
	}
}

// Check for update settings
if (isset($_POST['seatt_hidden'])) {
	$seatt_hidden = $_POST['seatt_hidden'];
} else {
	$seatt_hidden = 'N';
}
if($seatt_hidden == 'Y') {  
        //Form data sent  
       // $data = $_POST['seatt_data'];  
        //update_option('seatt_dbhost', $dbhost);  
        ?>  
  <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>  
        <?php  
    }  

	?>
        <p>To include an attendance form on a page/post, paste the tag into the document text:</p>
        <p> [seatt-form event_id=x] </p>
        <p>(x = the event id in the table below). If you haven't created the event_id, or have deleted it, nothing will be displayed.</p>
    <table width="auto" border="0" align="left" cellpadding="5" cellspacing="5">
      <tr>
        <th align="left" scope="col">Event ID</th>
        <th align="left" scope="col">Shortcode</th>
        <th align="left" scope="col">Event Name</th>
        <th align="left" scope="col">Hours Left</th>
        <th align="left" scope="col">Close Date/Time</th>
        <th align="left" scope="col">Status</th>
        <th align="left" scope="col">Attendees</th>
        <th align="left" scope="col">Options</th>
      </tr>
      <?php
		  $events = $wpdb->get_results("SELECT id, event_name, event_limit, event_expire, event_status FROM ".$wpdb->prefix."seatt_events ORDER BY id DESC");
	foreach ($events as $event) {
		$attendees = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event->id));
	$event_expire = ceil($event->event_expire - current_time('timestamp')) / 3600;
		   if ($event_expire < 0) {
			   $event_expire = 0;
			   $event->event_status = 0;
		   }
		  ?>
      <tr>
        <td><?php echo $event->id; ?></td>
        <td>[seatt-form event_id=<?php echo $event->id; ?>]</td>
        <td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event->id; ?>"><?php echo $event->event_name; ?></a></td>
        <td><?php echo round($event_expire); ?></td>
        <td><?php echo date("d-m-Y H:i", $event->event_expire); ?></td>
        <td><img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/plugins/simple-event-attendance/<?php echo $event->event_status; ?>.gif" width="10" height="10" /></td>
        <td><strong><?php echo $attendees; ?></strong> / <?php echo $event->event_limit; ?></td>
        <td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event->id; ?>">Edit</a> | <a href="admin.php?page=seatt_events&duplicate_event=1&event_id=<?php echo $event->id; ?>">Duplicate</a></td>
      </tr>
      <?php
	}
	?>
    </table>
<!--
To come.
    <h3>Options:</h3>
  <p>Attendees visible to all?</p>
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'seatt_trdom' ) ?>" />  
-->
</div>  