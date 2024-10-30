<?php

if (!defined('_VALID_ADD')) die('Direct access not allowed');

//require_once(ABSPATH.'/wp-content/plugins/ifs-frontend/includes/main-lib.php');

function ifs_add_email_box($content) {

	if (_IS_IFS) {
		$content=str_replace("[ifsmailersignupbox]",'<div><p class="note">See the signup box in this page.</p></div>',$content);
		return $content;
	}
	else {
		$content=str_replace("[ifsmailersignupbox]",'<div id="frontendaddemail"></div>',$content);
		return $content;
	}
}

function send_ifs_confirmation_email($sendTo,$confirmationKey) {
	
	$fromName=get_option('ifs_mailing_sender_name');
	if (!$fromName) {
		$fromName="Confirmation e-mail address for ".get_bloginfo('name');
	}
	$fromEmail=get_option('ifs_mailing_sender_email');
	if (!$fromEmail) {
		$fromEmail="noreply@inspiration-for-success.com";
	}
	$to=$sendTo;
	$subject="Confirmation for subscription to ".get_bloginfo('name');

	$message='<p>Please confirm your e-mail address by clicking on this link: <a href="'.site_url().'/?ifs_confirmation_key='.$confirmationKey.'">confirmation link</a>.</p>';
	$message.='<p>If the link does not work you can also copy below link in the address field of your browser:</p>';
	$message.='<p>Address to copy: <a href="'.site_url().'/?ifs_confirmation_key='.$confirmationKey.'">'.site_url().'/?ifs_confirmation_key='.$confirmationKey.'</a>.</p>';
	
	$messageToSend="<html>";		
	$messageToSend.="<head>";
	$messageToSend.="<title>";
	$messageToSend.="Title";
	$messageToSend.="</title>";
	$messageToSend.="</head>";
	$messageToSend.='<body>';
	$messageToSend.=$message;
	$messageToSend.="</body>";
	$messageToSend.="</html>";

	$headers=Array();
	$headers[]="From: $fromName <$fromEmail>\r\n";
	$headers[]="Content-Type: text/html; charset=".get_bloginfo('charset')."\r\n";
	return @wp_mail($to, $subject, $messageToSend, $headers);
}

function ifs_frontend_handler() {

	global $wpdb;

	$task=getParam('task');
	$email=getParam('email');
	$name=getParam('name');
	if (($task=='submit')||($task=='showaddemail')) {
		$error=false;
		if ($task=='submit') {
			//echo '<p>Submit</p>';
			//$name=getParam('name');
			$email=getParam('email');
			/*
			if (!$name) {
				echo '<p class="note">Please supply a name.</p>';
				$error=true;
			}
			*/
			if (!is_email($email)) {
				echo '<p class="note">Please supply a valid e-mail address.</p>';
				$error=true;
			}
			else {
				$error=false;
			}
		}
		if ($task=='submit') {
			$confirmationLink=mt_rand().mt_rand().mt_rand().mt_rand().mt_rand();
			$dateTime=gmdate("Y-m-d H:i:s"); // "2013-07-06 10:10:10";
			$batch=get_option('ifs_default_batch','new');
			$data=array('source'=>'Website','status'=>'subscribed','batchnumber'=>$batch,'name'=>$email,'email'=>$email,'confirmationkey'=>$confirmationLink,'confirmed'=>'no','dateTimeAdded'=>$dateTime);
			$result=$wpdb->insert($wpdb->prefix.'ifs_mailinglist',$data);
			if ($result) {
				if (send_ifs_confirmation_email($email,$confirmationLink)) {
					echo '<p class="note">E-mail address '.$email.' has been added to our database and a confirmation e-mail has been sent. Please confirm your e-mail address by clicking on the link in the confirmation e-mail.</p>';
				}
				else {
					echo '<p class="note">E-mail address '.$email.' has been added to our database but an error occurred sending you a confirmation e-mail. Please contact support.</p>';
				}
			}
			else {
				$error=true;
				if ((strpos('a'.strtolower($wpdb->last_error),'duplicate'))&&(strpos('a'.strtolower($wpdb->last_error),'email'))) {
					echo '<p class="note">E-mail address already exists.</p>';
				}
				else {
					echo '<p class="error">Error adding e-mail address, please contact support.';
					if (true) {
						echo ' Error: '.$wpdb->last_error.'.';
					}
					echo '</p>';
				}
			}
		}
		if (($error)||($task!='submit')) {
			if (!_IS_IFS) {
				echo '
					<form action="'.site_url().'/" method="post">
						<table>
							<!--
								<tr>
									<td>
										<p>Please enter your name:</p>
									</td>
									<td>
										<p><input id="nameid" type="text" name="name" value="'.$name.'"/></p>
									</td>
								</tr>
							-->
							<tr>
								<td>
									<p>Please enter your e-mail address:</p>
								</td>
								<td>
									<p><input id="emailid" type="text" name="email" value="'.$email.'"/></p>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<p><input type="submit" value="Submit" onclick="ifsFEAjaxCall(\'submit\');return false;"/></p>
								</td>
							</tr>
						</table>
					</form>
				';
			}
			else {
				$submit=(_IS_IFS)?'Sign up':'Submit'; // Well, all quick and dirty
				$signupText=(_IS_IFS)?'for our <span style="font-weight:bold;color:#D3452A">daily inspirational quote</span> ':'';
				echo '
					<form action="'.site_url().'/" method="post">
						<table>
							<!--
								<tr>
									<td>
										<p>Please enter your name:</p>
									</td>
									<td>
										<p><input id="nameid" type="text" name="name" value="'.$name.'"/></p>
									</td>
								</tr>
							-->
							<tr>
								<td>
									<h3>SIGN UP</h3>
									<p>&nbsp;</p>
									<p>To sign up '.$signupText.'please enter your e-mail address:</p>
								</td>
							</tr>
							<tr>
								<td>
									<p><input id="emailid" type="text" name="email" value="'.$email.'"/></p>
								</td>
							</tr>
							<tr>
								<td>
									<p>&nbsp;</p>
									<p><input type="submit" value="'.$submit.'" onclick="ifsFEAjaxCall(\'submit\');return false;"/></p>
								</td>
							</tr>
							<tr>
								<td>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
								</td>
							</tr>
						</table>
					</form>
				';			
			}
		}
	}
	else {
		echo '<p class="error">Invalid task '.$task.' in ifs_frontend_handler.</p>';
	}
	die;
} // end theme_custom_handler

global $ifsConfirmationChecked, $ifsUnsubscribeChecked;
$ifsConfirmationChecked=false; // Only do once
$ifsUnsubscribeChecked;

function check_ifs_confirmation() {

	global $wpdb, $ifsConfirmationChecked;

	//echo '<p>Check confirmation</p>';
	if (isset($_REQUEST['ifs_confirmation_key'])&&(!$ifsConfirmationChecked)) {
		$ifsConfirmationChecked=true;
		$confirmationKey=getParam('ifs_confirmation_key');
		if ($confirmationKey) {
			//echo '<p class="note">Confirmation key: '.getParam('ifs_confirmation_key').'.</p>';
			$query="SELECT id, confirmed FROM $wpdb->prefix"."ifs_mailinglist WHERE confirmationkey='$confirmationKey'";
			$count=$wpdb->query($query);
			if (gettype($count)=='integer') {
				$result=$wpdb->get_results($query,'OBJECT');
				if (gettype($result=='array')) {
					if (count($result)) { // Can only be one as unique key
					$value=$result[0];
					if ($value->confirmed=='no') {
							//echo '<p>Ok, false.</p>';
							$query="UPDATE $wpdb->prefix"."ifs_mailinglist SET confirmed='yes', status='active' WHERE confirmationkey='$confirmationKey'";
							if ($wpdb->query($query)) {
								echo '<p>Your e-mail address has been confirmed. Thank you for confirming it.</p>';
							}
							else {
								echo '<p class="error">An unexpected error occurred while confirming your e-mail address. Please contact support.</p>';
							}
						}
						else {
							echo '<p class="note">Your e-mail has already been confirmed.</p>';
						}
					}
					else {
						echo '<p class="error">Invalid confirmation link.</p>';
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
		else {
			echo '<p>Invalid confirmation key.</p>';
		}
	}
}

function ifs_unsubscribe() {

	global $wpdb, $ifsUnsubscribeChecked;

	//echo '<p>Unsubscribe check.</p>';
	if (isset($_REQUEST['ifs-unsubscribe'])&&(!$ifsUnsubscribeChecked)) {
		$ifsUnsubscribeChecked=true;
		$email=getParam('ifs-email');
		$confirmationKey=getParam('ifs-confirmation-key');
		if ($email) {
			//echo '<p class="note">Confirmation key: '.getParam('ifs_confirmation_key').'.</p>';
			$query="SELECT id, confirmationkey FROM $wpdb->prefix"."ifs_mailinglist WHERE email='$email'";
			$count=$wpdb->query($query);
			if (gettype($count)=='integer') {
				$result=$wpdb->get_results($query,'OBJECT');
				if (gettype($result=='array')) {
					if (count($result)) { // Can only be one as unique key
						$value=$result[0];
						if (!$value->confirmationkey) {
							$value->confirmationkey='98x46y7143z';
						}
						if ($value->confirmationkey==$confirmationKey) {
							//echo '<p>Ok, false.</p>';
							$query="UPDATE $wpdb->prefix"."ifs_mailinglist SET status='unsubscribed' WHERE id='$value->id'";
								if ($wpdb->query($query)) {
									echo '<p>Your have been unsubscribed from our mailing list. Thank you for having been part of it.</p>';
								}
								else {
									echo '<p class="error">An unexpected error occurred while trying to unsubscribe you. Please contact support.</p>';
								}
							}
							else {
								echo '<p class="error">We can only unsubscribe you with va valid confirmation key. Please send an e-mail  to <a href="mailto:'.get_option('ifs_mailing_sender_email').'">'.get_option('ifs_mailing_sender_name').'</a> and ask to be unsubscribed.</a>.</p>';
							}
					}
					else {
						echo '<p class="note">We don\'t recognize your e-mail address.</p>';
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
		else {
			echo '<p>No e-mail address supplied.</p>';
		}
	}
}

function ifs_front_end_register_script() {
	wp_enqueue_script('jquery');
	wp_register_script('ifs-signup',plugins_url().'/ifs-simple-e-mail-management/js/scriptsfs.js',false,array(0=>'jquery'),false,true);
	wp_enqueue_script('ifs-signup');
}

function ifs_ajaxurl() {
	?>
		<script type="text/javascript">
			<!--
				var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			// -->
		</script>
	<?php
}

?>