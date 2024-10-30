<?php

if (!defined('_VALID_ADD')) die('Direct access not allowed');

if (!defined('_IS_IFS')) define('_IS_IFS',0);
if (!defined('_LOCAL_DEVELOPMENT')) define ('_LOCAL_DEVELOPMENT',false);

function ifs_create_send_emailX() {

	$useWPEditor=get_option('ifs-use-wysiwyg-for-email')=='true';

	?>
		<script type="text/javascript">
			<!--

				showHtmlCalled=false;
			
				function callBackForSendEmail(response) {
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
								displayString='<p>E-mail has been sent.</p>';
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
				
				function sendEmail() {
					if (!showHtmlCalled) {
						window.alert('Please check the result first.');
						showHtml();
						return;
					}
					batchObject=document.getElementById('batchid');
					batch=batchObject.value;
					if (batch==0) {
						window.alert('Please select batch.');
					}
					else {
						showHtml();
						subjectObject=document.getElementById('subjectid');
						foregroundObject=document.getElementById('htmlcolorid');
						backgroundObject=document.getElementById('htmlbackgroundid');
						messageObject=document.getElementById('messageid');
						displayedHtmlObject=document.getElementById('htmlmessage');
						<?php if (_IS_IFS) { ?>
							quoteObject=document.getElementById('ifsquote');
							quotePersonObject=document.getElementById('ifsquoteperson');
							quotePersonColorObject=document.getElementById('htmlquotepersonid');
						<?php } ?>
						callBackForSendEmail('waiting');
						var parameters={batch: batch, subject: subjectObject.value, message: messageObject.value, subtask: 'fetch', 
						color: foregroundObject.value, 
						<?php if (_IS_IFS) { ?>
							quote:quoteObject.value,
							quotedperson:quotePersonObject.value,
							quotedpersoncolor:quotePersonColorObject.value,
						<?php } ?>
						displayedhtml:displayedHtmlObject.innerHTML,
						background:backgroundObject.value};
						ifsAjaxCall('sendemail',parameters);
					}
				}
			// -->
		</script>
		<?php ifsAjaxScript('callBackForSendEmail');?>
		<h1>Create and send e-mail</h1>
		<h2>Notes</h2>
		<p>Creating html e-mails is a bit more complicated than one would think but with this mailer plugin we try to make it as simple as possible, while keeping all options for advanced users.</p>
		<p>This is why we also recommend not to use the html editor, but enter html manually. Of course you can also just type text and change the colors only. There is an option to turn the html editor on but please note the e-mail may not turn out as you intend.</p>
		<p>Main feature of our mailer is that you can choose (foreground) color and background color by clicking.</p>
		<p>Support of batches for if your hosting provider has limits on the number of e-mails. You can change the default batch in the configuration screen.</p>
		<h2>Supply the data for the e-mail</h2>
		<form id="ifsform">
						<h2>Colors</h2>
						<div style="float:left">
							<h3>Color</h3>
							<p>Click to select:</p>
							<?php 
								$currentColor=get_option('ifs_last_mailing_color');
								echo ifsGetColorSelector('fgcolorid','htmlcolorid','showHtml','fgchangecolorid');
								//echo '<p>Color: <span id="clickedcolor">not selected</span>. Inverted: <span id="invertedclickedcolor">not selected</span></p>';
							?>
							<p>Color: <input id="htmlcolorid" type="text" size="8" value="<?php echo $currentColor;?>"/> <span style="background-color:<?php echo $currentColor;?>" id="fgcolorid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span style="background-color:<?php echo $currentColor;?>" id="fgchangecolorid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span id="fgchangecoloridtext"></span></p>
						</div>
						<div style="float:left"><p style="margin-left:1em"></p></div>
						<div style="float:left">
							<h3>Background color</h3>
							<p>Click to select:</p>
							<?php 
								$currentBackgroundColor=get_option('ifs_last_mailing_background');
								echo ifsGetColorSelector('bgid','htmlbackgroundid','showHtml','changebgid');
								//echo '<p>Backgroundcolor: <span id="clickedcolor">not selected</span>. Inverted: <span id="invertedclickedcolor">not selected</span></p>';
							?>
							<p>Background color: <input id="htmlbackgroundid" type="text" size="8" value="<?php echo $currentBackgroundColor;?>"/> <span style="background-color:<?php echo $currentBackgroundColor;?>" id="bgid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span style="background-color:<?php echo $currentBackgroundColor;?>" id="changebgid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span id="changebgidtext"></span></p>
						</div>
						<?php if (_IS_IFS) { ?>
							<div style="float:left"><p style="margin-left:1em"></p></div>
							<div style="float:left">
								<h3>Quote person color</h3>
								<p>Click to select:</p>
								<script type="text/javascript">
									<!--
										function setPersonColorAndShow() {
											/*
											messageObject=document.getElementById('messageid');
											displayObject=document.getElementById('htmlmessage');
											quoteObject=document.getElementById('ifsquote');
											quotePersonObject=document.getElementById('ifsquoteperson');								
											quoteInHtml=document.getElementById('quoteid');
											if (typeof(quoteInHtml)=='object') {
												quoteInHtml.innerHTML=quoteObject.value;
											}
											messageObject.value=displayObject.innerHTML;
											*/
											showHtml(); // This will copy the stuff to the display area. We can use that to change the quote and person in a simple way and then copy it back to the form field.
										}
									// -->
								</script>
								<?php 
									$currentPersonColor=get_option('ifs_last_quoted_person_color');
									echo ifsGetColorSelector('personid','htmlquotepersonid','setPersonColorAndShow','changepersonid');
									//echo '<p>Backgroundcolor: <span id="clickedcolor">not selected</span>. Inverted: <span id="invertedclickedcolor">not selected</span></p>';
								?>
								<p>Color quote person: <input id="htmlquotepersonid" type="text" size="8" value="<?php echo $currentPersonColor;?>"/> <span style="background-color:<?php echo $currentPersonColor;?>" id="personid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span style="background-color:<?php echo $currentPersonColor;?>" id="changepersonid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span id="changepersonidtext"></span></p>
							</div>
						<?php } ?>
						<div class="clear"></div>
				<?php if (_IS_IFS) { ?>
					<?php
						$lastQuote=get_option('ifs_last_quote');
						$lastQuotedPerson=get_option('ifs_last_quoted_person');
					?>
					<h2>IFS specific</h2>
					<p>Quote:</p>
					<textarea id="ifsquote" cols="100" rows="2"><?php echo htmlspecialchars($lastQuote);?></textarea>
					<p>Quote person:</p>
					<p><input type="text" size="100" id="ifsquoteperson" value="<?php echo htmlspecialchars($lastQuotedPerson);?>"/></p>
				<?php } ?>
				<h2>Message</h2>
				<p>Subject: <input id="subjectid" type="text" size="40" value="<?php echo get_option('ifs_last_mailing_subject');?>"/></p>
				<p>The text in the textbox below will just be copied in the body of the message. Please note that the final display will depend on the computer and the e-mail system of the receiver. You can check the display by cliking the 'Show display button'.</p>
				<p>When configured to send individual e-mails we support including first name with the string [firstname] and e-mail address with [e-mail] from the database fields and [unsubscribe] and [unsubscribelink].</p>
				<p>The text [unsubscribe] (or [Unsubscribe]) will put the word unsubscribe as a link to unsubscribe from the mailing list. The text [unsubscribelink] will just put the unsubscribelink so you can control the lay-out and the text.</p>
				<?php 
					if ($useWPEditor) { 
						$content=get_option('ifs_last_mailing_message');
						$settings=Array('media_buttons'=>false,'teeny'=>false);
						wp_editor($content,'messageid',$settings);
					}
					else {
						?>
							<textarea id="messageid" cols="80" rows="10"><?php echo get_option('ifs_last_mailing_message');?></textarea> 
						<?php
					}
				?>
			<h2>Display sample and submit area</h2>
			<?php 
				$version=get_bloginfo('version');
				if (_LOCAL_DEVELOPMENT) {
					echo '<p>Wordpress version: '.$version.'.</p>';
				}
			?>
			<p><input type="button" onclick="showHtml();" value="Show display"/><?php echo ($useWPEditor&&$version<'4.3')?' (Will switch mode to html)':'';?></p>
			<div id="htmlmessage">Display area.
			</div>
			<?php
				display_batch_selector();
			?>
			<p><input type="button" onclick="sendEmail();" value="Send e-mail"/></p>
			<div id="ifsresult">
			</div>
		</form>
		<script type="text/javascript">
			<!--
				document.getElementById('subjectid').focus();
				
				function showHtml() {
					showHtmlCalled=true;
					<?php if ($useWPEditor) { ?>
						htmlObject=document.getElementById('messageid-html');
						tmceObject=document.getElementById('messageid-tmce');
						// window.alert(typeof(htmlObject)+htmlObject.style.visibility);
						if (typeof(switchEditors.switchto)=='function') {
							switchEditors.switchto(htmlObject);
							switchEditors.switchto(tmceObject);
						}
						else {
							<?php if (_LOCAL_DEVELOPMENT) { ?>
								window.alert('switchEditors.switchto is not a function');
							<?php } ?>
						}
					<?php } ?>
					htmlMessage=document.getElementById('htmlmessage');					
					htmlMessage.style.color=document.getElementById('htmlcolorid').value;
					htmlMessage.style.backgroundColor=document.getElementById('htmlbackgroundid').value;
					message=document.getElementById('messageid').value;
					quoteColorObject=document.getElementById('');
					<?php if (_IS_IFS) { ?>
						quoteObject=document.getElementById('ifsquote');
						quotePersonObject=document.getElementById('ifsquoteperson');
						quotePersonColorObject=document.getElementById('htmlquotepersonid');
						message=message.replace('[quote]',quoteObject.value);
						message=message.replace('[quoteperson]','<span style="color:'+quotePersonColorObject.value+'">'+quotePersonObject.value);
					<?php } ?>
					message=message.replace('[unsubscribe]','<?php echo '<a href="'.get_site_url().'/?ifs-unsubscribe=true">unsubscribe</a>';?>');
					htmlMessage.innerHTML=message;
				}				
			// -->
		</script>
	<?php
}
?>