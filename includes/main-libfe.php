<?php
if (!defined('_IFS_MAIN_LIB')) {
define('_IFS_MAIN_LIB',1);

if (!defined('_VALID_ADD')) {
	echo 'Direct access to this file not allowed';
	die;
}
else {
	echo 'Direct access to this file not allowed';
	die;
}

if (!function_exists('checkForMagicQuotes')) {
	function checkForMagicQuotes($value) {
		if (get_magic_quotes_gpc()) {
			//added by pilardo dec 06, 2012
			if(is_array($value)) {
				return $value;
			}
			else {
				return stripslashes($value);
			}
		}
		else {
			return $value;
		}
	}
}
	
if (!function_exists('getParam')) {
	function getParam($name,$default='',$forceRequestType='') {
		
		if (isset($_REQUEST[$name])) {
			// if ($_REQUEST[$name]) { // Added May 10, 2011 by Guus, and removed May 28, 2011, also by Guus
			///added by jeram, may 31, 2012; for the webservers that magic quoutes are turned on
			if ($forceRequestType) {
			
				switch (strtolower($forceRequestType)) {
					case 'get': {
						return @checkForMagicQuotes($_GET[$name]);/// @ is to return null if error occurs. Like if method was set to post and you forced it to $_GET
						break;
					}
					case 'post': {
						return @checkForMagicQuotes($_POST[$name]);
						break;
					}
					case 'cookie': {
						return @checkForMagicQuotes($_COOKIE[$name]);
						break;
					}
					default: { ///use $_REQUEST
						break;
					}
				}
				
			}
			
			return checkForMagicQuotes($_REQUEST[$name]);
		}
		else {
			return $default;
		}
	}
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
		case 'sendemail': {
			echo 'Task sendemail';
			die;
		}
		case 'emaillist': {
			$batch=getParam('batch');
			$query="SELECT email FROM $wpdb->prefix"."ifs_mailinglist WHERE status='active'";
			if ($batch) {
				if ($batch!='all') {
					$query.=" AND batchnumber=".$batch;
				}
			}
			//echo $query;
			$count=$wpdb->query($query);
			// echo '<p>Type of result: '.gettype($count).'</p>';
			if (gettype($count)=='integer') {
				$result=$wpdb->get_results($query,'OBJECT');
				if (gettype($result=='array')) {
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
			//echo '<p>E-mail: '.$email.'.</p>';
			$query='SELECT id, email, name, firstName, lastName, source, language, status FROM '.$wpdb->prefix.'ifs_mailinglist WHERE email=\''.mysql_real_escape_string($email).'\'';
			//echo '<p>Query: '.$query.'</p>';
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
			//$wpdb->show_errors();
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
			$data=array('name'=>$name,'firstName'=>$firstName,'lastName'=>$lastName,'email'=>$email,'source'=>$source,'status'=>'active','language'=>$language,'confirmationkey'=>mt_rand().mt_rand().mt_rand().mt_rand().mt_rand());
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
} // define
?>