<?php
// seatt_events_admin.php
// Purpose: Template for the main SEATT settings page

global $wpdb;
?>
<div class="wrap">
<?php include("seatt_header.php"); ?>
          <?php    echo "<h2>" . __( 'Simple Event Attendance Summary', 'seatt_trdom' ) . "</h2>"; ?>
<?php

// Clean down event ID by checking if numeric, then casting to integer
if (isset($_GET['event_id'])) {
	if (is_numeric($_GET['event_id'])) {
		$event_id = intval($_GET['event_id']);
	} else {
		$event_id = '';
	}
} else {
	$event_id = '';
}

// Check for remove event, and remove if present
if (($event_id != '') && (isset($_GET['remove_event']))) {
	// We have previously checked event_id
	if ($wpdb->delete($wpdb->prefix.'seatt_events', array('id' => $event_id), array('%d'))) {
		// If we were able to remove the event, remove any attendees
		$wpdb->delete($wpdb->prefix.'seatt_attendees', array('event_id' => $event_id), array('%d'));
		?>
		<div class="updated">
			<p><strong>Event <em><?php echo esc_html($event_id); ?></em> deleted.</strong></p>
		</div>
		<?php
	} else {
		// If event couldn't be removed
		?>
		<div class="error">
			<p><strong>Event not found. Click <a href="admin.php?page=seatt_events">here</a> to reload this page.</strong></p>
		</div>
		<?php
	}
}


// Check for duplicate event
if (($event_id != '') && (isset($_GET['duplicate_event']))) {

	// Get existing event details
	$event = $wpdb->get_row($wpdb->prepare("SELECT event_name, event_desc, event_limit, event_start, event_expire, event_reserves FROM ".$wpdb->prefix."seatt_events WHERE id = %d", $event_id));

	if ($event != NULL) {
	// Create another record
	$wpdb->insert($wpdb->prefix.'seatt_events', array( 'event_name' => $event->event_name, 'event_desc' => $event->event_desc, 'event_limit' => $event->event_limit, 'event_start' => $event->event_start, 'event_expire' => $event->event_expire, 'event_status' => 0, 'event_reserves' => $event->event_reserves ), array('%s', '%s', '%d', '%s', '%s', '%d', '%d') );
	?>
    <div class="updated">
    	<p><strong>Event <em><?php echo esc_html($event_id); ?></em> duplicated.</strong></p>
    </div>
        <?php
	}
}

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
<p>This page lists all events (open and closed) that exist in the database.</p>
<br />
<h2>How to display events in posts and pages</h2>
<!--<p>To display all currently active events, paste the following into the post/page text:</p>
<p><pre>[seatt-list]</pre></p>
<br />-->
<p>To include a specific attendance form on a page/post, paste the tag into the post/page text:</p>
<p><pre>[seatt-form event_id=x]</pre></p>
<p>(x = the event id in the table below). If you haven't created the event_id, or have deleted it, nothing will be displayed.</p>
<br />
<h2>Events</h2>
<table width="auto" border="0" align="left" cellpadding="5" cellspacing="5">
    <tr>
        <th align="left" scope="col">Event ID</th>
        <th align="left" scope="col">Shortcode</th>
        <th align="left" scope="col">Event Name</th>
        <th align="left" scope="col">Time Left</th>
        <th align="left" scope="col">Close Date/Time</th>
        <th align="left" scope="col">Status</th>
        <th align="left" scope="col">Attendees</th>
        <th align="left" scope="col">Options</th>
    </tr>
    <?php
	// Get all listed events
    $events = $wpdb->get_results("SELECT id, event_name, event_limit, event_expire, event_status FROM ".$wpdb->prefix."seatt_events ORDER BY id DESC");
    foreach ($events as $event) {
		$attendees = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event->id));

    // Create and format the time remaining (ceil to take to zero in case this is negative)
    $event_expire_seconds = ceil($event->event_expire - current_time('timestamp'));

    // If zero then mark event as expired
		if ($event_expire_seconds < 0) {
			$event_expire_seconds = 0;
			$event->event_status = 0;
    	}

    // Set variable with formatted time remaining
    $event_expire = sprintf('%02d%s%02d%s%02d%s', floor($event_expire_seconds/3600), 'd ', ($event_expire_seconds/60)%60, 'm ', $event_expire_seconds%60, 's');

    // Set div colour and text for the status
    if(intval($event->event_status) == 0) {
      $event_status_html = '<div style="width:15px;height:15px;background-color:#ff0000" title="Event expired or closed"></div>';
    } else {
      $event_status_html = '<div style="width:15px;height:15px;background-color:#00ff00" title="Event open"></div>';
    }
    //$event_status_2 = '<div style="width:15px;height:15px;background-color:#FFC200" title="Event pending"></div>';

    ?>
    <tr>
        <td><?php echo esc_html($event->id); ?></td>
        <td>[seatt-form event_id=<?php echo esc_html($event->id); ?>]</td>
        <td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo intval($event->id); ?>"><?php echo esc_html($event->event_name); ?></a></td>
        <td><?php echo $event_expire; ?></td>
        <td><?php echo date("d-m-Y H:i", $event->event_expire); ?></td>
        <td><?php echo $event_status_html; ?></td>
        <td><strong><?php echo intval($attendees); ?></strong> / <?php echo intval($event->event_limit); ?></td>
        <td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo intval($event->id); ?>">Edit</a> | <a href="admin.php?page=seatt_events&duplicate_event=1&event_id=<?php echo intval($event->id); ?>">Duplicate</a></td>
    </tr>
    <?php
    }

	if (count($events) == 0) {
	echo "<tr><td colspan=\"8\">No events found.</td></tr>";
}
    ?>
</table>
</div>
