<?php

if (!defined('_VALID_ADD')) die('Direct access not allowed');

require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/main-lib.php');

function ifs_install() {

	global $wpdb;

	$batch=get_option('ifs_default_batch');
	if (!$batch) {
		update_option('ifs_default_batch','1');
	}

	$table=$wpdb->prefix.'ifs_mailinglist';
	$query="CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`firstName` varchar(255) DEFAULT NULL,
		`lastName` varchar(255) DEFAULT NULL,
		`email` varchar(255) NOT NULL,
		`dateTimeAdded` datetime NOT NULL DEFAULT '2013-07-13 00:00:00',
		`source` varchar(255) DEFAULT NULL,
		`status` enum('active','unsubscribed','spamblock','other','subscribed') NOT NULL DEFAULT 'active',
		`language` enum('dutch','english','both','other') NOT NULL,
		`confirmationkey` varchar(255) DEFAULT NULL,
		`confirmed` enum('yes','no') NOT NULL DEFAULT 'no',
		`batchnumber` varchar(255) NOT NULL DEFAULT '1',
		PRIMARY KEY (`id`),
		UNIQUE KEY (`email`(191)));"; // 191 is the maximum safe value for mySQL/InnoDB tables.
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta( $query );
}

function ifs_management() {

	global $wpdb;

	$defaultBatch=get_option('ifs_default_batch');
	$defaultSenderName=get_option('ifs_mailing_sender_name');
	$defaultSenderEmail=get_option('ifs_mailing_sender_email');
	?>
		<h2>Main page IFS simple mass mailer</h2>
		<p>This is the main page of the IFS mass mailer plugin.</p>
		<p>Origin of the plugin was simplifying the sending of quotes for <a href="http://www.inspiration-for-success/">Inspiration for Success</a>, but we thought the functionality might be useful for other people also looking for a sending mailing so a relative small group of people straight from Wordpress.</p>
		<p>We are planning on a version with more options including the WYSIWYG editor, but as WYSIWYG would not always apply to e-mails that was not our priority. Color selection is included now, but not fully tested.</p>
		<p>Next configuration options will include WYSIWYG editor and separating our quote system from 'default' html e-mail sending so the plugin can still meet our needs and be also more generic.</p>
		<p>Other major additions we are planning are options for unsubscribe link, easier inclusion in the front-end of the signup form and sending e-mails individually and not as 'BCC' as it is done now.</p>
		<h2>Mailinglist info</h2>
		<table>
			<?php
				$query="SELECT status, count(status) as count FROM $wpdb->prefix"."ifs_mailinglist GROUP BY status";
				$count=$wpdb->query($query);
				if (gettype($count)=='integer') {
					$result=$wpdb->get_results($query,'OBJECT');
					if (gettype($result)=='array') {
						if (count($result)) {
							$total=0;
							foreach($result as $element => $value) {
								$total+=$value->count;
								echo '<tr>';
									echo '<td><p>People on mailinglist with status '.$value->status.':</p></td>';
									echo '<td><p style="text-align:right">'.$value->count.'</p></td>';
								echo '</tr>';
							}
							echo '<td><hr/><p>Total number of people in mailinglist: </p></td>';
							echo '<td><hr/><p style="text-align:right">'.$total.'</p></td>';
						}
						else {
							echo '<tr><td colspan="2"><p style="color:blue">No users found.</p></td></tr>';
						}
					}
					else {
						echo '<tr><td colspan="2"><p style="color:red">Error during query getting user results. Please report this exact error in the suppor section of <a href="http://wordpress.org/support/plugin/ifs-simple-e-mail-management" target="_blank">http://wordpress.org/support/plugin/ifs-simple-e-mail-management</a>.</p></td></tr>';
					}
				}
				else {
					echo '<tr><td colspan="2"><p style="color:red">Error during query getting users. Please report this exact error in the suppor section of <a href="http://wordpress.org/support/plugin/ifs-simple-e-mail-management" target="_blank">http://wordpress.org/support/plugin/ifs-simple-e-mail-management</a>.</p></td></tr>';
				}
			?>
			<tr><td colspan="2"><h2>Configuration info</h2></td></tr>
			<tr>
				<td><p>Current default batch:</p></td>
				<td><p><?php echo $defaultBatch;?></p></td>
			</tr>
			<tr>
				<td><p>Current default sender name:</p></td>
				<td>
					<p>
						<?php 
							if (!$defaultSenderName) {
								echo '<span style="color:red">Default sender name not configured. Please configure in <a href="'.admin_url().'admin.php?page=ifs-mailer-configuration">configure options</a>.</span>';
							}
							else {
								echo $defaultSenderName;
							}
						?>
					</p>
				</td>
			</tr>
			<tr>
				<td><p>Current default e-mail:</p></td>
				<td>
					<p>
						<?php 
							if (!$defaultSenderEmail) {
								echo '<span style="color:red">Default sender e-mail not configured. Please configure in <a href="'.admin_url().'admin.php?page=ifs-mailer-configuration">configure options</a>.</span>';
							}
							else {
								echo $defaultSenderEmail;
							}
						?>
					</p>
				</td>
			</tr>
		</table>
	<?php
}

function display_batch_selector($name='batch',$batchId='batchid') {

	global $wpdb;

	$query="SELECT DISTINCT batchnumber FROM $wpdb->prefix"."ifs_mailinglist";
	$count=$wpdb->query($query);
	// echo '<p>Type of result: '.gettype($count).'</p>';
	if (gettype($count)=='integer') {
		$result=$wpdb->get_results($query,'OBJECT');
		if (gettype($result=='array')) {
			$count=count($result);
			if ($count) {
				if ($count!==1) {
					echo '<p>Choose group: ';
						echo '<select id="'.$batchId.'" name="'.$name.'" style="text-align:right">';
						echo '<option value="0">Select batch</option>';
						echo '<option value="all">All</option>';
						foreach ($result as $element => $row) {
							echo '<option value="'.$row->batchnumber.'">'.$row->batchnumber.'</option>';
						}
						echo '</select>';
					echo '</p>';
				}
				else { // Only one batch
					echo '<p>Send to: '.$result[0]->batchnumber.' (only one group available).';
					echo '<input id="'.$batchId.'" type="hidden" name="'.$name.'" value="all"/>';
					echo '</p>';
				}
			}
			else {
				echo '<p class="note">No records found.</p>';
			}
		}
		else {
			echo '<p class="error">Error during query.</p>';			
		}
	}
	else {
		echo '<p class="error">Error during query.</p>';
	}
}

function ifs_view_email_list() {

	global $wpdb, $ifsAjaxScript;
	?>
		<script type="text/javascript">
			<!--
				function requestList() {
					batch=document.getElementById('batchid').value;
					if (parseInt(batch)!=0) {
						var parameters= {
							batch: batch
						}
						ifsAjaxCall('emaillist',parameters);
					}
					else {
						window.alert('Please choose valid batch.');
					}
				}
			
				function callbacklist(response) {
					check=response.substr(0,2);
					//window.alert(displayString);
					if (check=='ok') {	
						displayString=response.substring(2);
						document.getElementById('resultDiv').innerHTML=displayString;
					}
					else {
						document.getElementById('resultDiv').innerHTML=response;
					}
				}
			// -->
		</script>
	<?php
	ifsAjaxScript('callbacklist');
	echo '<div class=”wrap”>';
	echo '<h2>IFS Management</h2>';
	echo $ifsAjaxScript;
	echo '</div>';
	display_batch_selector();
	?>
		<p><input type="button" onclick="requestList();" value="Get list"></input></p>
		<div id="resultDiv">
		</div>
	<?php
}

function ifs_add_email() {
	?>
		<script type="text/javascript">
			<!--
				function callBackForAddEmail(response) {
					//window.alert('Callback for add e-mail');
					resultLocation=document.getElementById('ifsresult');
					if (typeof(resultLocation)=='object') {
						if (response=='waiting') {
							resultLocation.innerHTML='<p class="note">Waiting for result.</p>';
						}
						else {	
							//window.alert(response);
							check=response.substr(0,2);
							//window.alert(displayString);
							if (check=='ok') {	
								displayString=response.substring(2);
								//window.alert('ok');
								document.getElementById('nameid').value='';
								document.getElementById('firstnameid').value='';
								document.getElementById('lastnameid').value='';
								document.getElementById('sourceid').value='';
								document.getElementById('emailid').value='';
								document.getElementById('languageid').value='english';
							}
							else {
								displayString=response;
							}
							resultLocation.innerHTML=displayString;
						}
					}
					else {
						window.alert('Result location not defined.');
					}
				}
			// -->
		</script>
		<?php ifsAjaxScript('callBackForAddEmail');?>
		<h2>Add email.</h2>
		<p>On this page you can add an e-mail address to the mailing list.</p>
		<form action="/" method="post">
			<script type="text/javascript">
				<!--
					function ifsSubmit() {
						nameObject=document.getElementById('nameid');
						firstNameObject=document.getElementById('firstnameid');
						lastNameObject=document.getElementById('lastnameid');
						sourceObject=document.getElementById('sourceid');
						emailObject=document.getElementById('emailid');
						languageObject=document.getElementById('languageid');
						if (nameObject.value=='') {
							window.alert('Please enter name.');
							return false;
						}
						if (firstNameObject.value=='') {
							window.alert('Please enter firstname.');
							return false;
						}
						if (emailObject.value=='') {
							window.alert('Please enter e-mail address.');
							return false;
						}
						callBackForAddEmail('waiting');
						var parameters={
							name: nameObject.value,
							email: emailObject.value,
							firstname: firstNameObject.value,
							lastname: lastNameObject.value,
							language: languageObject.value,
							source: sourceObject.value
						}
						ifsAjaxCall('addemail',parameters);
					}
				// -->
			</script>
			<table>
				<tr>
					<td><p>Name:</p></td>
					<td><p><input id="nameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Firstname:</p></td>
					<td><p><input id="firstnameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Lastname:</p></td>
					<td><p><input id="lastnameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Source:</p></td>
					<td><p><input id="sourceid" type="text" name="source"/></p></td>
				</tr>
				<tr>
					<td><p>E-mail:</p></td>
					<td><p><input id="emailid" type="text" name="email"/></p></td>
				</tr>
				<tr>
					<td><p>Language:</p></td>
					<td>
						<p>
							<select id="languageid" name="language">
								<option value="english" selected="selected">English</option>
								<option value="dutch">Dutch</option>
								<option value="both">Both</option>							
								<option value="other">Other</option>
							</select>
						</p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<p>
							<input type="button" value="Add" onclick="ifsSubmit();"/>
						</p>
					</td>
				</tr>
			</table>
		</form>
		<h2>Result</h2>
		<div id="ifsresult">
		</div>
	<?php
}
function ifs_create_send_email() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/send-e-mail.php');
	ifs_create_send_emailX();
}

function ifs_mailer_configuration() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-simple-e-mail-management/includes/configure.php');

	ifs_mailer_configurationX();
}


function ifs_edit_email() {
	?>
		<script type="text/javascript">
			<!--
				function callBackForEditEmail(response) {
					//window.alert('Callback for add e-mail');
					resultLocation=document.getElementById('ifsresult');
					if (typeof(resultLocation)=='object') {
						if (response=='waiting') {
							resultLocation.innerHTML='<p class="note">Waiting for result.</p>';
						}
						else {	
							//window.alert(response);
							check=response.substr(0,2);
							//window.alert(displayString);
							if (check=='ok') {	
								check2=response.substr(0,4);
								if (check2=='okfe') {
									displayString='Data has been saved.';
									document.getElementById('idid').innerHTML='';
									document.getElementById('nameid').value='';
									document.getElementById('firstnameid').value='';
									document.getElementById('lastnameid').value='';
									document.getElementById('sourceid').value='';
									document.getElementById('languageid').style.visibility='hidden';
									document.getElementById('statusid').style.visibility='hidden';
								}
								else { 
									displayString='<p>Data has been fetched. You can edit and then save.</p>';
									returnedToEvalResult=response.substring(2);
									//window.alert(returnedToEvalResult);
									//displayString+='<p>';
									//displayString+=returnedToEvalResult;
									//displayString+='</p>';
									eval(returnedToEvalResult);
									document.getElementById('idid').innerHTML=result.id;
									document.getElementById('nameid').value=result.name;
									document.getElementById('firstnameid').value=result.firstname;
									document.getElementById('lastnameid').value=result.lastname;
									document.getElementById('sourceid').value=result.source;
									document.getElementById('languageid').value=result.language;
									document.getElementById('languageid').style.visibility='visible';
									document.getElementById('statusid').value=result.status;
									document.getElementById('statusid').style.visibility='visible';
								}
							}
							else {
								displayString=response;
							}
							resultLocation.innerHTML=displayString;
						}
					}
					else {
						window.alert('Result location not defined.');
					}
				}
			// -->
		</script>
		<?php ifsAjaxScript('callBackForEditEmail');?>
		<h2>Edit email.</h2>
		<p>On this page you can edit an e-mail address of the mailing list.</p>
		<form action="/" method="post">
			<script type="text/javascript">
				<!--
					function ifsEditSubmit() {
						id=document.getElementById('idid').innerHTML;
						nameObject=document.getElementById('nameid');
						firstNameObject=document.getElementById('firstnameid');
						lastNameObject=document.getElementById('lastnameid');
						sourceObject=document.getElementById('sourceid');
						emailObject=document.getElementById('emailid');
						languageObject=document.getElementById('languageid');
						statusObject=document.getElementById('statusid');
						if (emailObject.value=='') {
							window.alert('Please enter e-mail address.');
							return false;
						}
						if (nameObject.value=='') {
							window.alert('Please enter name.');
							return false;
						}
						if (firstNameObject.value=='') {
							window.alert('Please enter firstname.');
							return false;
						}
						callBackForEditEmail('waiting');
						var parameters={
							id: id,
							name: nameObject.value,
							email: emailObject.value,
							firstname: firstNameObject.value,
							lastname: lastNameObject.value,
							language: languageObject.value,
							source: sourceObject.value,
							status: statusObject.value
						}
						ifsAjaxCall('saveeditemail',parameters);
					}
					
					function fetchEmail() {
						emailObject=document.getElementById('emailid');
						document.getElementById('submitid').style.visibility='visible';
						var parameters={subtask: 'fetch', email: emailObject.value};
						ifsAjaxCall('fetchemail',parameters);
					}
					
				// -->
			</script>
			<table>
				<tr>
					<td><p>Id:</p></td>
					<td><p id="idid"></p></td>
				</tr>
				<tr>
					<td><p>E-mail:</p></td>
					<td><p><input id="emailid" type="text" name="email"/>&nbsp;<input type="button" onclick="fetchEmail();" value="Fetch"></input></p></td>
				</tr>
				<tr>
					<td><p>Name:</p></td>
					<td><p><input id="nameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Firstname:</p></td>
					<td><p><input id="firstnameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Lastname:</p></td>
					<td><p><input id="lastnameid" type="text" name="name"/></p></td>
				</tr>
				<tr>
					<td><p>Source:</p></td>
					<td><p><input id="sourceid" type="text" name="source"/></p></td>
				</tr>
				<tr>
					<td><p>Language:</p></td>
					<td>
						<p>
							<select style="visibility:hidden" id="languageid" name="language">
								<option value="english" selected="selected">English</option>
								<option value="dutch">Dutch</option>
								<option value="both">Both</option>							
								<option value="other">Other</option>
							</select>
						</p>
					</td>
				</tr>
				<tr>
					<td><p>Status:</p></td>
					<td>
						<p>
							<select style="visibility:hidden" id="statusid" name="status">
								<option value="active" selected="selected">Active</option>
								<option value="spamblock">Spam block</option>
								<option value="subscribed">Subscribed</option>							
								<option value="unsubscribed">Unsubscribed</option>							
								<option value="other">Other</option>
							</select>
						</p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<p>
							<input id="submitid" style="visibility:hidden" type="button" value="Save" onclick="ifsEditSubmit();"/>
						</p>
					</td>
				</tr>
			</table>
		</form>
		<h2>Result</h2>
		<div id="ifsresult">
		</div>
	<?php
}

function ifs_mgt_menu () {
	add_menu_page('IFS Mass Mail','IFS Mass Mail','manage_options','ifs-management','ifs_management');
	add_submenu_page('ifs-management','Add e-mail','Add e-mail address','manage_options','add-email','ifs_add_email');
	add_submenu_page('ifs-management','Edit e-mail','Edit e-mail address','manage_options','edit-email','ifs_edit_email');
	add_submenu_page('ifs-management','View list','View e-mail list','manage_options','view-email','ifs_view_email_list');
	add_submenu_page('ifs-management','E-mail','Create/send e-mail','manage_options','create-send-email','ifs_create_send_email');
	add_submenu_page('ifs-management','Configuration','Configuration options','manage_options','ifs-mailer-configuration','ifs_mailer_configuration');
}

?>