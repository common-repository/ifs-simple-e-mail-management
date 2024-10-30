<?php

if (!defined('_VALID_ADD')) { echo 'Direct access to this file not allowed'; }

if (!defined('_IS_IFS')) define('_IS_IFS',0);

function invertColor($color) { // Never used or tested
	
	// Takes hex string or integer and returns html color string with #
	// Returns false on unknown argument


	if (gettype($color)=='string') {
		$color=hexdec($color);
	}
	else {
		if (gettype($color)!='integer') {
			return false;
		}
	}
	$color=intVal($color);
	$invertedColor=~$color;
	$invertedColor&0xffffff;
	return '#'.dechex($invertedColor);
}

function ifsGetColorSelector($colorFieldId,$inputFieldId,$functionToCall='',$htmlIdForChange='',$parameter='') {

	static $callNumber;
	
	if (!isset($callNumber)) {
		$callNumber=1;
	}
	else {
		$callNumber+=1;
	}

	$boxWidth=16;
	$boxHeight=16;
	$colorPartsFirstChar=Array('00','33','66','99','cc','ff');
	$colorPartsSecondChar=Array('00','33','66','99','cc','ff');
	$colorPartsThirdChar=Array('00','33','66','99','cc','ff');
	?>
		<script type="text/javascript">
			<!--			
				function ifsSetColors<?php echo $callNumber;?>(foregroundColor,backgroundColor,colorFieldId,inputFieldId) {
					<?php if (defined('_LOCAL_DEVELOPMENT')) { ?>
						//window.alert('Foregroundcolor '+foregroundColor+'. BackgroundColor: '+backgroundColor+'.');
					<?php } ?>
					colorFieldElement=document.getElementById(colorFieldId);
					inputFieldElement=document.getElementById(inputFieldId);
					if (typeof(colorFieldElement=='object')) {
						//colorFieldElement.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						colorFieldElement.style.color=foregroundColor;
						colorFieldElement.style.backgroundColor=backgroundColor;
						if (typeof(inputFieldElement)=='object') {
							inputFieldElement.value=backgroundColor;
							<?php
								if ($functionToCall) {
									echo $functionToCall.'();';
								}
							?>
						}
						else {
							window.alert('Program error in setColors');
						}
					}
					else {
						window.alert('Program error in setColors');
					}
				}
			// ==>
		</script>
	<?php
	$selector='<table cellpadding="0" cellspacing="0" style="margin:0px;padding:0px;border:0px">';
		foreach($colorPartsSecondChar as $element2 => $colorPart2) {
			$selector.='<tr>';
			foreach($colorPartsFirstChar as $element =>$colorPart) {
				foreach ($colorPartsThirdChar as $element => $colorPart3) {
					// Let's just make horizontal rows first
					$color='#'.$colorPart2.$colorPart.$colorPart3;
					$selector.='<td style="width:8px;height:8px;margin:0px;padding:0px;border:0px;background-color:'.$color.'"';
					if ($htmlIdForChange) {
						$selector.=' onmouseover="element=document.getElementById(\''.$htmlIdForChange.'\');element.style.backgroundColor=\''.$color.'\';element2=document.getElementById(\''.$htmlIdForChange.'text\');element2.innerHTML=\''.$color.'\'"';
					}
					$invertedColor='blue';
					if ($colorFieldId) {
						$selector.=' onclick="ifsSetColors'.$callNumber.'(\''.$invertedColor.'\',\''.$color.'\',\''.$colorFieldId.'\',\''.$inputFieldId.'\')"';
					}
					$selector.='>';
					$selector.='</td>';
				}
			}
			$selector.='</tr>';
		}
	$selector.='</table>';
	//$selector.='<p id="'.$name.'id">Color</p>';
	return $selector;
}

function ifsAjaxScript($jsCallbackFunctionName='myAlert') {
	?>
		<script type="text/javascript">
			<!--
				function myAlert(response) {
					alert('Got this from the server: ' + response);				
				}
				function merge_objects(obj1,obj2){
					var obj3 = {};
					for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
					for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
					return obj3;
				}
				
				function ifsAjaxCall(task,parameters) {
					//window.alert(parameters.name);
					if (typeof(task)=='undefined') {
						task='';
					}
					jQuery(document).ready(function($) {
						var data = {
							action: 'ifs_action',
							task: task,
							whatever: 1234
						};
						//window.alert(typeof(parameters));
						if (typeof(parameters=='object')) {
							data=merge_objects(data,parameters);
						}
						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						$.post(ajaxurl, data, function(response) {
							<?php echo $jsCallbackFunctionName;?>(response);
						});
					});
				}
			// -->
		</script>
	<?php
}

function ifsSendMail($to,$subject,$messageToSend,$headers,$fromEmail) {

	if (!defined('_DO_NOT_USE_WP_MAIL')) define('_DO_NOT_USE_WP_MAIL',false);

	$headerString='';
	foreach ($headers as $element => $header) {
		$headerString.=$header."\r\n";
	}
	if (!_DO_NOT_USE_WP_MAIL) {
		if (defined('_LOCAL_DEVELOPMENT')) {
			echo '<p>Using wp_mail().</p>';
		}
		if (@wp_mail($to, $subject, $messageToSend, $headerString."\r\n".'From: '.$fromEmail."\r\n")) {
			return true;
		}
	}
	// mail not sent, we use just php mail
	$headerString='';
	foreach ($headers as $element => $header) {
		$headerString.=$header."\r\n";
	}
	if (@mail($to,$subject,$messageToSend,$headerString,'-f '.$fromEmail."\r\n")) {
		return true;
	}
	else { // Some more weird workaround for local system
		foreach ($headers as $element => $header) {
			if (strpos($header,'From')===0) {
				$headerString.=$fromEmail;
			}
			else {
				$headerString.=$header."\r\n";
			}
		}
		if (@mail($to,$subject,$messageToSend,$headerString,'-f '.$fromEmail."\r\n")) {
			return true;
		}
		else {
			return false;
		}
	}
}

function ifs_action_callback() {

	global $wpdb; // this is how you get access to the database

	$id=getParam('id');
	$task=getParam('task');
	$name=getParam('name');
	$firstName=getParam('firstname');
	$lastName=getParam('lastname');
	$email=getParam('email');
	$source=getParam('source');
	$status=getParam('status');
	$language=getParam('language');
	switch ($task) {
		case 'configuremailer': {
			$name=getParam('name');
			$email=getParam('email');
			$batch=getParam('batch');
			$oldBatch=get_option('ifs_default_batch');
			if ($oldBatch==$batch) {
				$message0='<p class="note">Batch not changed. Is and was '.$batch.'</p>';
				$result0=true;
			}
			else {
				$result0=update_option('ifs_default_batch',$batch,'','no');
				if ($result0) {
					$message0='<p class="note">Batch name changed from '.$oldBatch.' into '.$batch.'</p>';
				}
				else {
					$message0='<p class="error">Error saving default batch.</p>';
				}
			}			
			//echo '<p>Name: '.$name.'</p>';
			//echo '<p>E-mail: '.$email.'</p>';
			if (!is_email($email)) {
				echo '<p class="error">Invalid e-mail address</p>';
			}
			else {
				$oldName=get_option('ifs_mailing_sender_name');
				$oldEmail=get_option('ifs_mailing_sender_email');
				if ($oldName==$name) {
					$message1='<p class="note">Name not changed. Is and was '.$name.'</p>';
					$result1=true;
				}
				else {
					$result1=update_option('ifs_mailing_sender_name',$name,'','no');
					if ($result1) {
						$message1='<p class="note">Sender name changed from '.$oldName.' into '.$name.'</p>';
					}
					else {
						$message1='<p class="error">Error saving sender name.</p>';
					}
				}
				if ($oldEmail==$email) {
					$message2='<p class="note">E-mail not changed. Is and was '.$email.'</p>';
					$result2=true;
				}
				else {
					$result2=update_option('ifs_mailing_sender_email',$email,'','no');
					$message2='<p class="note">Sender e-mail changed from '.$oldEmail.' into '.$email.'</p>';
				}			
				$useWysiwyg=getParam('ifsusewysiwyg');
				//echo '<p>Parameter: '.$useWysiwyg.'</p>';
				$useWysiwygOption=get_option('ifs-use-wysiwyg-for-email');
				//echo '<p>Option: '.$useWysiwygOption.'</p>';
				$result3=true;
				if ($useWysiwyg=='true') {
					if ($useWysiwygOption=='true') { // It was set before
						echo '<p class="note">Use wysiwyg editor setting did not change and is and was set to \'use wysiwyg\'.</p>';
					}
					else { // Value changed from false to true
						if (update_option('ifs-use-wysiwyg-for-email','true')) {
							echo '<p class="note">Use wysiwyg editor setting turned on.</p>'; 			
						}
						else {
							$result3=false;
							echo '<p class="error">Error setting wysiwyg editor option. Please contact support.</p>';
						}
					}
				}
				else { // $useWysiwyg is false
					if ($useWysiwygOption=='false') {
						echo '<p class="note">Use wysiwyg editor setting did not change and is and was set to \' not use wysiwyg editor\'.</p>';
					}
					else {
						if (update_option('ifs-use-wysiwyg-for-email','false')) {
							echo '<p class="note">Use wysiwyg editor turned off.</p>'; 			
						}
						else {
							$result3=false;
							echo '<p class="error">Error setting wysiwyg editor option. Please contact support.</p>';
						}
					}
				}
				
				$sendIndividual=getParam('ifssendindividual');
				$sendIndividualOption=get_option('ifs-send-individual-emails');
				$result4=true;
				if ($sendIndividual=='true') {
					if ($sendIndividualOption=='true') { // It was set before
						echo '<p class="note">Use individual e-mails setting did not change and is and was set to \'use individual e-mails\'.</p>';
					}
					else { // Value changed from false to true
						if (update_option('ifs-send-individual-emails','true')) {
							echo '<p class="note">Use individual e-mails setting turned on.</p>'; 			
						}
						else {
							$result4=false;
							echo '<p class="error">Error setting individual e-mails option. Please contact support.</p>';
						}
					}
				}
				else { // $sendIndividual is false
					if ($sendIndividualOption=='false') {
						echo '<p class="note">Use individual e-mails setting did not change and is and was set to \' not use individual e-mails\'.</p>';
					}
					else {
						if (update_option('ifs-send-individual-emails','false')) {
							echo '<p class="note">Use individual e-mails setting turned off.</p>'; 			
						}
						else {
							$result4=false;
							echo '<p class="error">Error setting individual e-mails option. Please contact support.</p>';
						}
					}
				}

				if (($result0)&&($result1)&&($result2)&&$result3&&$result4) {
					echo '<p>Options have been saved.</p>'.$message0.$message1.$message2;
				}
				else {
					echo '<p class="error">Error saving options.</p>';
				}
			}
			die;
		}
		case 'sendemail': {
			$batch=getParam('batch');
			//echo '<p>Batch: '.$batch.'.</p>';
			$fromName=get_option('ifs_mailing_sender_name');
			if (!$fromName) {
				echo '<p class="error">Sender from name not set. Please configure this in configure options.</p>';
				die;
			}
			$fromEmail=get_option('ifs_mailing_sender_email');
			if (!$fromEmail) {
				echo '<p class="error">Sender from name not set. Please configure this in configure options.</p>';
				die;
			}
			$to="$fromName <$fromEmail>";
			$subject=stripslashes(getParam('subject')); // Also here, should not be needed to use stripslashes
			if (!$subject) {
				echo '<p class="error">Please fill in subject.</p>';
				die;
			}
			//echo '<p>Subject: '.$subject.'.</p>';
			$background=getParam('background');
			$color=getParam('color');
			//echo '<p>Background: '.$background.'.</p>';
			if (_IS_IFS) { // Save the quote stuff
				$quote=stripslashes(getParam('quote'));
				echo '<p>Quote: '.$quote.'.</p>';
				$quotedPerson=stripslashes(getParam('quotedperson'));
				$quotedPersonColor=getParam('quotedpersoncolor'); // Should not have slashes
				$table=$wpdb->prefix.'ifs_quotes';
				if (!update_option('ifs_last_quote',$quote,'','no')) {
					// echo '<p class="error">Color not saved.</p>';
				}
				if (!update_option('ifs_last_quoted_person',$quotedPerson,'','no')) {
					// echo '<p class="error">Color not saved.</p>';
				}
				if (!update_option('ifs_last_quoted_person_color',$quotedPersonColor,'','no')) {
					// echo '<p class="error">Color not saved.</p>';
				}
				$now=gmdate("Y-n-d H:i:s");
				if (function_exists('esc_sql')) {
					$quoteX=esc_sql($quote);
					$quotedPersonX=esc_sql($quotedPerson);
				}
				else {
					$quoteX=mysql_real_escape_string($quote);
					$quotedPersonX=mysql_real_escape_string($quotedPerson);
				}
				$query="INSERT $table (quote, quoted_person,type,date_and_time_added) VALUES ('$quoteX','$quotedPersonX','daily','$now')";
				echo '<p>'.$query.'</p>';
				if (!$wpdb->query($query)) {
					echo '<p>Error storing quote. Normally this would indicate a duplicate entry.</p>';
				}
				$htmlMessage=stripslashes(getParam('displayedhtml'));
				if (!$htmlMessage) {
					echo '<p class="error">Please enter (html) message</p>';
					die;
				}
				//echo '<p>Quote: '.$quote.'</p>';
				//echo '<p>Person: '.$quotedPerson.'</p>';
				//echo '<p>Message html: '.htmlspecialchars($htmlMessage).'</p>';
				//die;
			}
			$message=stripslashes(getParam('message')); // Not sure why we need to do this, definitely wrong
			if (!$message) {
				echo '<p class="error">Please enter message</p>';
				die;
			}
			//echo '<p>'.htmlspecialchars($message).'</p>';
			$messageToSend="<html>";		
			$messageToSend.="<head>";
			$messageToSend.="<title>";
			$messageToSend.="Title";
			$messageToSend.="</title>";
			$messageToSend.="</head>";
			$messageToSend.='<body style="color:'.$color.';background:'.$background.'">';
			if (_IS_IFS) {
				$messageToSend.=$htmlMessage;
			}
			else {
				$messageToSend.=$message;
			}
			$messageToSend.="</body>";
			$messageToSend.="</html>";
			// Save values
			if (!update_option('ifs_last_mailing_color',$color,'','no')) {
				// echo '<p class="error">Color not saved.</p>';
			}
			if (!update_option('ifs_last_mailing_background',$background,'','no')) {
				// echo '<p class="error">Background not saved.</p>';
			}
			if (!update_option('ifs_last_mailing_subject',$subject,'','no')) {
				// echo '<p class="error">Subject not saved.</p>';
			}
			if (!update_option('ifs_last_mailing_message',$message,'','no')) {
				// echo '<p class="error">Message not saved.</p>';
			}
			// End save values
			// echo '<p>'.htmlspecialchars($messageToSend).'</p>';
			$headers=Array();
			$headers[]="From: $fromName <$fromEmail>";
			$headers[]="Content-Type: text/html; charset=".get_bloginfo('charset');
			if (defined('_IS_IFS')) {
				$headers[]="Errors-To: <info@inspiration-for-success.com>";
				$headers[]="Return-Path: <info@information-for-success.com>";			
			}
			/*
			echo '<p>';
			foreach($headers as $element=> $header) {
				echo htmlspecialchars($header);
			}
			echo '</p>';
			*/
			$query="SELECT email, firstName, confirmationkey FROM $wpdb->prefix"."ifs_mailinglist WHERE status='active'";
			if ($batch) {
				if ($batch!='all') {
					$query.=" AND batchnumber='".$batch."'";
				}
			}
			//echo $query;
			$count=$wpdb->query($query);
			// echo '<p>Type of result: '.gettype($count).'</p>';
			if (gettype($count)=='integer') {
				$result=$wpdb->get_results($query,'OBJECT');
				if (gettype($result)=='array') {
					if (count($result)) {
						echo '<p>';
							if (get_option('ifs-send-individual-emails')=='true') {
								$sendCount=0;
								if (defined('_LOCAL_DEVELOPMENT')&&(_LOCAL_DEVELOPMENT)) {
									$headers[]='Bcc:'.$fromEmail;
								}
								$messageSave=$messageToSend;
								foreach ($result as $element => $person) {
									$messageToSend=$messageSave;
									$to=$person->email;
									$messageToSend=str_replace('[e-mail]',$to,$messageToSend);
									if ($person->firstName) {
										$firstName=$person->firstName;
									}
									else {
										$firstName=$person->email;
									}
									if (!$person->confirmationkey) {
										$confirmationkey='98x46y7143z';
									}
									else {
										$confirmationkey=$person->confirmationkey;
									}
									//echo '<p>To: '.htmlspecialchars($to).'</p>';
									//echo '<p>Before: '.htmlspecialchars($messageToSend).'</p>';
									$messageToSend=str_replace('[firstname]',$firstName,$messageToSend);
									$messageToSend=str_replace('[unsubscribelink]',get_site_url().'/?ifs-unsubscribe=true&amp;ifs-email='.$to.'&amp;ifs-confirmation-key='.$confirmationkey,$messageToSend);
									$messageToSend=str_replace('[unsubscribe]','<a href="'.get_site_url().'/?ifs-unsubscribe=true&amp;ifs-email='.$to.'&amp;ifs-confirmation-key='.$confirmationkey.'">unsubscribe</a>',$messageToSend);
									$messageToSend=str_replace('[Unsubscribe]','<a href="'.get_site_url().'/?ifs-unsubscribe=true&amp;ifs-email='.$to.'&amp;ifs-confirmation-key='.$confirmationkey.'">Unsubscribe</a>',$messageToSend);
									//echo '<p>After: '.htmlspecialchars($messageToSend).'</p>';
									if (ifsSendMail($to,$subject,$messageToSend,$headers,$fromEmail)) {
										$sendCount++;
									}
								}
								if ($sendCount) {
									if ($sendCount==$count) {
										echo 'E-mail sent successfully to '.$sendCount.' people.';
									}
									else { // Unlikely
										echo 'E-mail sent successfully to '.$sendCount.' people out of total '.$count.'.';
									}
								}
								else {
									echo 'Error sending e-mail.';								
								}
							}
							else {
								$to=$fromEmail;
								foreach($result as $element => $person) {
									$headers[]='Bcc:'.$person->email;
								}
								if (ifsSendMail($to,$subject,$messageToSend,$headers,$fromEmail)) {
									echo 'E-mail sent successfully to '.$count.' people.';
								}
								else {
									echo 'Error sending e-mail.';
								}
							}
						echo '</p>';
					}
					else {
						echo 'ok<p class="note">No people found.</p>';
					}
				}
				else {
					echo '<p class="error">Error during query.</p>';			
				}
			}
			else {
				echo '<p class="error">Error during query.</p>';
			}
			die;
		}
		case 'emaillist': {
			$batch=getParam('batch');
			$query="SELECT email FROM $wpdb->prefix"."ifs_mailinglist WHERE status='active'";
			if ($batch) {
				if ($batch!='all') {
					$query.=" AND batchnumber='".$batch."'";
				}
			}
			//echo $query;
			$count=$wpdb->query($query);
			// echo '<p>Type of result: '.gettype($count).'</p>';
			if (gettype($count)=='integer') {
				$result=$wpdb->get_results($query,'OBJECT');
				if (gettype($result)=='array') {
					if (count($result)) {
						echo 'ok<p>Number of people: '.$count.'.</p>';
						echo '<p>';
							foreach ($result as $element => $person) {
								echo $person->email.'; ';
							}
							echo '</p>';
					}
					else {
						echo 'ok<p class="note">No people found.</p>';
					}
				}
				else {
					echo '<p class="error">Error during query.</p>';			
				}
			}
			else {
				echo '<p class="error">Error during query.</p>';
			}
			break;
		}
		case 'fetchemail': {
			//echo '<p>Task fetchemail.</p>';
			if (!is_email($email)) {
				echo '<p class="error">Invalid e-mail address.</p>';
				die;
			}	
			if (function_exists('esc_sql')) {
				$emailX=esc_sql($email);				
			}
			else {
				$emailX=mysql_real_escape_string($email);
			}
			$query='SELECT id, email, name, firstName, lastName, source, language, status FROM '.$wpdb->prefix.'ifs_mailinglist WHERE email=\''.$emailX.'\'';
			$result=$wpdb->get_row($query);
			if (gettype($result)=='object') {
				if ($result->email==$email) { // ok
					echo 'ok';
					echo 'result={
						id: '.$result->id.',
						firstname: \''.$result->firstName.'\',
						lastname: \''.$result->lastName.'\',
						source: \''.$result->source.'\',
						language: \''.$result->language.'\',
						status: \''.$result->status.'\',
						name: \''.$result->name.'\'
					};';
					//echo 'var result={id: \''.$result->id.'\', e-mail: \''.$result->email.'\'};';
				}
				else {
					echo '<p class="error">E-mail address not found or database error 1.</p>';
				}
			}
			else {
				echo '<p class="error">E-mail address not found or database error 2.</p>';
			}
			break;
		}
		case 'saveeditemail': {
			/*
			echo '<p>';
			echo 'Task: '.$task.', ';
			echo 'name: '.$name.', ';
			echo 'Firstname: '.$firstName.', ';
			echo 'Lastname: '.$lastName.', ';
			echo 'e-mail: '.$email.'.';
			echo '</p>';
			*/
			if (!$id) {
				echo '<p class="error">Request requires id.</p>';				
			}
			/*
			else {
				echo '<p class="note">Id: '.$id.'.</p>';
				echo '<p class="note">E-mail: '.$email.'.</p>';
				echo '<p class="note">Name: '.$name.'.</p>';
				echo '<p class="note">Firstname: '.$firstName.'.</p>';
				echo '<p class="note">Lastname: '.$lastName.'.</p>';
				echo '<p class="note">Source: '.$source.'.</p>';
				echo '<p class="note">Language: '.$language.'.</p>';
				echo '<p class="note">Status: '.$status.'.</p>';
			}
			*/
			if (WP_DEBUG) {
				$wpdb->show_errors();
			}
			$data=array('name'=>$name,'firstName'=>$firstName,'lastName'=>$lastName,'email'=>$email,'source'=>$source,'status'=>$status,'language'=>$language);
			$data=array('name'=>$name,'firstName'=>$firstName,'lastName'=>$lastName,'source'=>$source,'language'=>$language,'status'=>$status);
			$where=array('email'=>$email,'id'=>$id); // We'll use a double check for now.
			$result=$wpdb->update($wpdb->prefix.'ifs_mailinglist',$data,$where);
			if ($result) {
				echo 'okfe<p>User with id '.$id.' and e-mail address '.$email.' has been updated.</p>';
			}
			else {
				echo '<p class="error">Error saving data to database with error ';
				echo '<span style="font-style:italic">'.$wpdb->last_error.'</span>';
				echo '.</p>';
			}
			break;
		}
		case 'addemail': {
			if (!$language) {
				$language='english';
			}
			if (!$name) {
				echo '<p class="error">Please supply a name.</p>';
				die;		
			}
			
			if (!$firstName) {
				echo '<p class="error">Please supply a firstname.</p>';
				die;		
			}
			
			if (!is_email($email)) {
				echo '<p class="error">Invalid e-mail address.</p>';
				die;
			}	
			$batch=get_option('ifs_default_batch');
			if (!$batch) {
				echo '<p class="Error">Batch not set.</p>';
				die;
			}
			$dateTime=gmdate("Y-m-d H:i:s"); // "2013-07-06 10:10:10";
			$data=array('batchnumber'=>$batch,'name'=>$name,'firstName'=>$firstName,'dateTimeAdded'=>$dateTime,'lastName'=>$lastName,'email'=>$email,'source'=>$source,'status'=>'active','language'=>$language,'confirmationkey'=>mt_rand().mt_rand().mt_rand().mt_rand().mt_rand());
			$result=$wpdb->insert($wpdb->prefix.'ifs_mailinglist',$data);
			if ($result) {
				echo 'ok<p>User with e-mail address '.$email.' has been added.</p>';
			}
			else {
				echo '<p class="error">Error adding user to database with error ';
				echo '<span style="font-style:italic">'.$wpdb->last_error.'</span>';
				echo '.</p>';
			}
			break;
		}
		default: {
			$whatever = intval( $_POST['whatever'] );
			$task=getParam('task');
			$whatever += 10;
			echo $whatever;
		}
	}
	die(); // this is required to return a proper result
}	
?>