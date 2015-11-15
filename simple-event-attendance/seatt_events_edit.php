<?php
global $wpdb;
if (isset($_GET['event_id'])) {
	$event_id = (int)$_GET['event_id'];
} else {
	$event_id = '';
}
?>
<div class="wrap">  
<?php include("seatt_header.php"); ?>
          <?php echo "<h2>" . __( 'Simple Event Attendance - Edit Event', 'seatt_trdom' ) . "</h2>";
		  // Kill page if no event_id
		  if ($event_id == '') {
			  die("No event id specified, please visit the main seatt page.");
		  }
		  // Remove all participants
		  if (isset($_GET['clear_event'])) {
			  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d", $event_id));
			  ?>
            <div class="updated"><p><strong><?php _e('All attendees removed.' ); ?></strong></p></div>
            <?php
		  }
		  
		  // Process add form if sent
		  if(isset($_POST['seatt_add_user'])) {
			  $_POST = stripslashes_deep($_POST);
			  $add_username = $_POST['seatt_add_user'];
			  if (username_exists($add_username) != NULL) {
			  	// Check not already registered
				$add_userid = username_exists($add_username);
				$a_registered = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$wpdb->prefix."seatt_attendees WHERE event_id = %d AND user_id = %d", $event_id, $add_userid));
				// If not already registered
				if ($a_registered == "") {
					$user_comment = $_POST['seatt_add_comment'];
					$wpdb->insert($wpdb->prefix.'seatt_attendees', array( 'event_id' => $event_id, 'user_id' => $add_userid, 'user_comment' => $user_comment ), array('%d', '%d', '%s') );
					?>
                    <div class="updated"><p><strong><?php _e('User ' . $add_username . ' added.' ); ?></strong></p></div>
                    <?php
				} else {
					?>
            		<div class="updated"><p><strong><?php _e('User ' . $add_username . ' already on list.' ); ?></strong></p></div>
            		<?php
				}
			} elseif(username_exists($add_username) == NULL) {
				?>
				<div class="updated"><p><strong><?php _e('User ' . $add_username . ' not found.' ); ?></strong></p></div>
				<?php
			}
			  
		  }
		  
		  // Process edit form if sent
          if((isset($_POST['seatt_name']))
			&&(!isset($_POST['seatt_add_user']))) {
			$_POST = stripslashes_deep($_POST);  
			$event_name = $_POST['seatt_name'];
			$event_desc = $_POST['seatt_desc'];
			$event_limit = $_POST['seatt_limit'];
			$event_status = $_POST['seatt_status'];
			$event_start = strtotime($_POST['seatt_start']);
			$event_expire = strtotime($_POST['seatt_expire']);
			
			$wpdb->update($wpdb->prefix.'seatt_events', array( 'event_name' => $event_name, 'event_desc' => $event_desc, 'event_limit' => $event_limit, 'event_start' => $event_start, 'event_expire' => $event_expire, 'event_status' => $event_status ), array( 'id' => $event_id ), array('%s', '%s', '%d', '%s', '%s', '%d'));
			?>
            <div class="updated"><p><strong><?php _e('Event "'.$event_name.'" updated.' ); ?></strong></p></div>
<?php
		  }
		// Process remove if sent
		if (isset($_GET['remove_attendee'])) {
			$remove_attendee = (int)$_GET['remove_attendee'];
		} else {
			$remove_attendee = '';
		}
          if ($remove_attendee != '') {
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."seatt_attendees WHERE id = %d", $remove_attendee));
			$place = $_GET['place'];
			?>
            <div class="updated"><p><strong><?php _e('Attendee '.$place.' removed.' ); ?></strong></p></div>
            <?php
		  }
		  // Get event details
		   $event = $wpdb->get_results($wpdb->prepare("SELECT id, event_name, event_desc, event_limit, event_start, event_expire, event_status FROM ".$wpdb->prefix."seatt_events WHERE id = %d", $event_id));
		   
		   if ($event[0]->id != "") {
			  ?>
		  <p><strong>Event options:</strong></p>
		  <form name="seatt_edit_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		    <p>Event Name<br>
		      <label for="seatt_name"></label>
		    <input name="seatt_name" type="text" id="seatt_name" value="<?php echo $event[0]->event_name; ?>" size="50" maxlength="150">
		    </p>
		    <p>Event Description<br>
		      <label for="seatt_desc"></label>
              <input name="seatt_desc" type="text" id="seatt_desc" value="<?php echo $event[0]->event_desc; ?>" size="80" maxlength="150" />
		    </p>
		    <p>Attendee Limit (enter 0 for no limit)<br>
		      <label for="seatt_limit"></label>
		      <input name="seatt_limit" type="text" id="seatt_limit" value="<?php echo $event[0]->event_limit; ?>" size="14" maxlength="8">
		    </p>
		    <p>Opening Registration Date &amp; Time (set to the past to open immediately)<br />
		      <input name="seatt_start" type="text" id="seatt_start" value="<?php echo date("d-m-Y H:i", $event[0]->event_start); ?>" />
		    eg the date/time is now <?php echo date("d-m-Y H:i", current_time('timestamp')); ?></p>
		    <p>Closing Registration Date &amp; Time<br />
              <input name="seatt_expire" type="text" id="seatt_expire" value="<?php echo date("d-m-Y H:i", $event[0]->event_expire); ?>" />
eg '<?php echo date("d-m-Y H:i", current_time('timestamp') + 604800); ?>' (a week from now)</p>
		    <p>Status<br />
		      <label for="seatt_status"></label>
		      <select name="seatt_status" id="seatt_status">
		        <option value="1"<?php if ($event[0]->event_status) { echo ' selected="selected"'; } ?>>Open</option>
		        <option value="0"<?php if (!$event[0]->event_status) { echo ' selected="selected"'; } ?>>Closed</option>
	          </select>
		    </p>
		    <p>
		      <input type="submit" name="Submit" value="<?php _e('Edit Event', 'seatt_trdom' ) ?>" />
		      <br />
		      <br />
		      Del<a href="admin.php?page=seatt_events&event_id=<?php echo $event_id; ?>&remove_event=1">e</a>te Event? / 
		    Cl<a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>&clear_event=1">e</a>ar all attendees (Deleting an event/attendees is permanent)</p>
</form><br /><hr /><br />

		  <h3>
	      Event participants:</h3>
		  <p><table width="auto" border="0" align="left" cellpadding="5" cellspacing="5">
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
		      <td><?php echo $user->user_comment; ?></td>
		      <td><a href="admin.php?page=seatt_events_edit&event_id=<?php echo $event_id; ?>&remove_attendee=<?php echo $user->id; ?>&place=<?php echo $num; ?>">Remove User</a></td>
	        </tr>
		    <?php
			$num++;
	}
	?>
  </table></p>
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
		    <p><?php 
			 $num = 1;
	foreach ($users as $user) {
		$user_info = get_userdata($user->user_id);
		  echo $user_info->user_email . ", ";
			$num++;
		}
	?></p>
  </blockquote>
  <?php } ?>          
          </div>