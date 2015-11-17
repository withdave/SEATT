<?php
global $wpdb;

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
	
?>
<div class="wrap">  
<?php include("seatt_header.php"); ?>

<?php echo "<h2>" . __( 'Simple Event Attendance - Edit Event', 'seatt_trdom' ) . "</h2>";
// Kill page if no event_id, and check to see if in DB
if ($event_id == '') {
	die("<div class=\"error\"><p><strong>No event ID specified, please reload the main SEATT page.</strong></p></div>");
} else {		  
	// Check to see whether event exists
	$seatt_this_limit = $wpdb->get_var($wpdb->prepare("SELECT event_limit FROM ".$wpdb->prefix."seatt_events WHERE id = %d", $event_id));
	if ($seatt_this_limit == NULL) {
		die("<div class=\"error\"><p><strong>No valid event found, please reload the main SEATT page.</strong></p></div>");
	}
}

// Remove all participants
if (isset($_GET['clear_event'])) {
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event_id));
	?>
	<div class="updated">
    	<p><strong>All attendees removed.</strong></p>
    </div>
	<?php
}
		  
// Register user to event
if (isset($_POST['seatt_add_user'])) {
	$_POST = stripslashes_deep($_POST);
	$add_username = sanitize_text_field($_POST['seatt_add_user']);
	  
	// Check username exists in wordpress system
	if (username_exists($add_username) != NULL) {
		// Check whether user is already registered for event
		$add_userid = username_exists($add_username);
		if ($wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d AND user_id = %d", $event_id, $add_userid)) == NULL) {
			
			// Sanitise any comments
			$user_comment = sanitize_text_field($_POST['seatt_add_comment']);
			if (strlen(trim($user_comment)) == 0) {
				$user_comment = "";
			}
			
			// Check there's still space to add user
			if ($seatt_this_limit > ($wpdb->get_var($wpdb->prepare("SELECT count(user_id) FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event_id)))) {
				
				// Check the event is active and not expired
				if ($wpdb->get_var($wpdb->prepare("SELECT event_status FROM ".$wpdb->prefix."seatt_events WHERE id = %d AND event_expire >= %d", $event_id, time())) == 1) {
			
					// Create registration
					$wpdb->insert($wpdb->prefix.'seatt_attendees', array( 'event_id' => $event_id, 'user_id' => $add_userid, 'user_comment' => $user_comment), array('%d', '%d', '%s') );
					?>
					<div class="updated">
						<p><strong>User <em><?php echo esc_html($add_username); ?></em> registered to event.</strong></p>
					</div>
					<?php 
				} else {
					// Else if event not active
					?>
					<div class="error">
						<p><strong>User <em><?php echo esc_html($add_username); ?></em> was not added, as the event status is closed, or the event closing date has passed.</strong></p>
					</div>
					<?php						
				}
			} else {
				// Else if no space to add the user
				?>
				<div class="error">
					<p><strong>User <em><?php echo esc_html($add_username); ?></em> was not added, as the event is fully subscribed.</strong></p>
				</div>
				<?php
			}
		} else {
			// Else if the user was already registered for the event
			?>
			<div class="updated">
				<p><strong>User <em><?php echo esc_html($add_username); ?></em> was already registered for the event.</strong></p>
			</div>
			<?php
		}
	} else {
		// Else if username doesn't exist
		?>
		<div class="error">
			<p><strong>User <em><?php echo esc_html($add_username); ?></em> not found.</strong></p>
		</div>
		<?php
	}
}
// END
// Edit event details
elseif ((isset($_POST['seatt_name'])) && (!isset($_POST['seatt_add_user']))) {
		
	$_POST = stripslashes_deep($_POST);  
	$event_name = sanitize_text_field($_POST['seatt_name']);
	$event_desc = sanitize_text_field($_POST['seatt_desc']);
	$event_limit = intval($_POST['seatt_limit']);
	$event_status = intval($_POST['seatt_status']);
	$event_start = strtotime($_POST['seatt_start']);
	$event_expire = strtotime($_POST['seatt_expire']);
	
	// Ensure required fields contain values, update if true
	if ((strlen(trim($event_name)) > 0) && ($event_start) && ($event_expire) && (strlen(intval($event_status)) == 1)) {
		$wpdb->update($wpdb->prefix.'seatt_events', array( 'event_name' => $event_name, 'event_desc' => $event_desc, 'event_limit' => $event_limit, 'event_start' => $event_start, 'event_expire' => $event_expire, 'event_status' => $event_status ), array( 'id' => $event_id ), array('%s', '%s', '%d', '%s', '%s', '%d'));
		
		?>
		<div class="updated">
        	<p><strong>Event <em><?php echo esc_html($event_name); ?></em> updated.</strong></p>
        </div>
		<?php
	} else {
		// If not all required fields are present
		?>
		<div class="error">
        	<p><strong>Event not updated, error in submitted values or omitted field.</strong></p>
        </div>
		<?php
	}
}
// END

// Remove user registration
// This needs improving
if (isset($_GET['remove_attendee'])) {
	if (is_numeric($_GET['remove_attendee'])) {
		$remove_attendee = intval($_GET['remove_attendee']);
		if ($wpdb->delete($wpdb->prefix.'seatt_attendees', array('id'=>$remove_attendee, 'event_id' => $event_id), array('%d', '%d'))) {
			$place = intval($_GET['place']);
			?>
			<div class="updated">
				<p><strong>Attendee <em><?php echo esc_html($place); ?></em> removed.</strong></p>
			</div>
			<?php
		}
	}
}
// END

// GET EVENT DETAILS FOR PAGE
$event = $wpdb->get_row($wpdb->prepare("SELECT id, event_name, event_desc, event_limit, event_start, event_expire, event_status FROM ".$wpdb->prefix."seatt_events WHERE id = %d", $event_id));

// Check to see if a value has been returned   
if ($event->id != "") {
	?>
	<p><strong>Event options:</strong></p>
		<form name="seatt_edit_form" method="post" action="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>">
	<p>Event Name*<br>
		<label for="seatt_name"></label>
		<input name="seatt_name" type="text" id="seatt_name" value="<?php echo esc_html($event->event_name); ?>" size="50" maxlength="150">
	</p>
	<p>Event Description<br>
		<label for="seatt_desc"></label>
		<input name="seatt_desc" type="text" id="seatt_desc" value="<?php echo esc_html($event->event_desc); ?>" size="80" maxlength="150" />
	</p>
	<p>Attendee Limit (enter 0 for no limit)*<br>
		<label for="seatt_limit"></label>
		<input name="seatt_limit" type="text" id="seatt_limit" value="<?php echo esc_html($event->event_limit); ?>" size="14" maxlength="8">
	</p>
	<p>Opening Registration Date &amp; Time*<br />
		<input name="seatt_start" type="text" id="seatt_start" value="<?php echo date("d-m-Y H:i", esc_html($event->event_start)); ?>" />
		eg the server date/time is currently '<a onclick="document.getElementById('seatt_start').value='<?php echo date("d-m-Y H:i", current_time('timestamp')); ?>';"><?php echo date("d-m-Y H:i", current_time('timestamp')); ?></a>' (dd-mm-yyyy hh:mm)</p>
	<p>Closing Registration Date &amp; Time<br />
		<input name="seatt_expire" type="text" id="seatt_expire" value="<?php echo date("d-m-Y H:i", esc_html($event->event_expire)); ?>" />
eg a week from now is '<a onclick="document.getElementById('seatt_expire').value='<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>';"><?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?></a>' (dd-mm-yyyy hh:mm)</p>
	<p>Status*<br />
		<label for="seatt_status"></label>
		<select name="seatt_status" id="seatt_status">
			<option value="1"<?php if ($event->event_status) { echo ' selected="selected"'; } ?>>Open</option>
			<option value="0"<?php if (!$event->event_status) { echo ' selected="selected"'; } ?>>Closed</option>
		</select>
	</p>
	<p>*Required fields</p>
	<p>
		<input type="submit" name="Submit" value="<?php _e('Edit Event', 'seatt_trdom' ) ?>" />
	<br />
	<br />
	Del<a href="admin.php?page=seatt_events&event_id=<?php echo $event_id; ?>&remove_event=1">e</a>te Event? / 
	Cl<a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>&clear_event=1">e</a>ar all attendees (Deleting an event/attendees is permanent)</p>
	</form><br />
	<hr /><br />
	
	<h3>
	Event participants:</h3>
	<p>
	<table width="auto" border="0" align="left" cellpadding="5" cellspacing="5">
		<tr>
			<th align="left" scope="col">Participant #</th>
			<th align="left" scope="col">Username</th>
			<th align="left" scope="col">Email</th>
			<th align="left" scope="col">Comment</th>
			<th align="left" scope="col">Options</th>
		</tr>
		<?php
		$users = $wpdb->get_results($wpdb->prepare("SELECT id, user_id, user_comment FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d ORDER BY id ASC", $event_id));
		$num = 1;
		foreach ($users as $user) {
			$user_info = get_userdata($user->user_id);
			?>
			<tr>
				<td><?php echo $num; ?></td>
				<td><?php echo $user_info->user_login; ?></td>
				<td><a href="mailto:<?php echo $user_info->user_email; ?>"><?php echo $user_info->user_email; ?></a></td>
				<td><?php echo esc_html($user->user_comment); ?></td>
				<td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>&remove_attendee=<?php echo $user->id; ?>&place=<?php echo $num; ?>">Remove User</a></td>
			</tr>
			<?php
			$num++;
		}
		?>
	</table>
	</p>
	<p style="clear:both;">&nbsp;</p>
	<p style="clear:both;"><strong>Add Participant by wordpress username:</strong>	      </p>
	<form id="add_user" name="add_user" method="post" action="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>">
	<p>
	<label for="seatt_add_user"></label>
	Username<br />
	<input name="seatt_add_user" type="text" id="seatt_add_user" size="40" maxlength="150" />
	</p>
	<p>Comment<br />
	<label for="seatt_add_comment"></label>
	<input name="seatt_add_comment" type="text" id="seatt_add_comment" size="40" maxlength="40" />
	</p>
	<p>
	<input type="submit" name="seatt_add_user_submit" id="seatt_add_user_submit" value="Add User" />
	</p>
	</form>
	<p style="clear:both;"><strong>Participant Emails:</strong></p>
	<p>To keep the plugin simple, no mass emailer is included. If you really want to email everyone you can copy the list below into your BCC to field and email them that way.</p>
	<blockquote>
	<p>
	<?php 
	$num = 1;
	foreach ($users as $user) {
		$user_info = get_userdata($user->user_id);
		echo $user_info->user_email . "; ";
		$num++;
	}
	?>
	</p>
	</blockquote>
	<?php
	} 
?>          
</div>