<?php

if (!defined('_VALID_ADD')) die('Direct access not allowed');
function ifs_mailer_configurationX() {
	?>
		<script type="text/javascript">
			<!--
				function callBackForConfiguration(response) {
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
		<?php ifsAjaxScript('callBackForConfiguration');?>
		<h2>Configuration options mailer.</h2>
		<p>On this page you can configure options for the mailer.</p>
		<form action="/" method="post">
			<script type="text/javascript">
				<!--
					function ifsSubmit() {
						nameObject=document.getElementById('nameid');
						emailObject=document.getElementById('emailid');
						batchObject=document.getElementById('batchid');
						wysiwygObject=document.getElementById('ifsusewysiwygid');
						ifsSendIndividualObject=document.getElementById('ifssendindividual');
						if (nameObject.value=='') {
							window.alert('Please enter name.');
							return false;
						}
						if (emailObject.value=='') {
							window.alert('Please enter e-mail address.');
							return false;
						}
						callBackForConfiguration('waiting');
						var parameters={
							name: nameObject.value,
							email: emailObject.value,
							batch: batchObject.value,
							ifsusewysiwyg: wysiwygObject.checked,
							ifssendindividual: ifsSendIndividualObject.checked
						}
						ifsAjaxCall('configuremailer',parameters);
					}
				// -->
			</script>
			<table>
				<tr>
					<?php
						$defaultBatch=get_option('ifs_default_batch');
						if (!$defaultBatch) {
							$defaultBatch=1;
						}
					?>
					<td><p>Default group (was 'batch'):</p></td>
					<td><p><input id="batchid" type="text" name="batch" value="<?php echo $defaultBatch;?>"/></p></td>
					<td><p></p></td>
				</tr>
				<tr>
					<td><p>Sender name:</p></td>
					<td><p><input id="nameid" type="text" size="60" name="name" value="<?php echo get_option('ifs_mailing_sender_name');?>"/></p></td>
					<td><p></p></td>
				</tr>
				<tr>
					<td><p>Sender e-mail:</p></td>
					<td><p><input id="emailid" type="text" size="60" name="email" value="<?php echo get_option('ifs_mailing_sender_email');?>"/></p></td>
					<td><p></p></td>
				</tr>
				<tr>
					<?php
						$useWysiwyg=get_option('ifs-use-wysiwyg-for-email');
					?>
					<td><p>Use WYSIWYG editor:</p></td>
					<td><p><input id="ifsusewysiwygid" type="checkbox" name="ifsusewysiwyg"<?php echo ($useWysiwyg==='true')?' checked="checked"':'';?> value="true"/></p></td>
					<td><p></p></td>
				</tr>
				<tr>
					<?php
						$useIndividual=get_option('ifs-send-individual-emails');
					?>
					<td><p>Use individual e-mails (not BCC):&nbsp;</p></td>
					<td><p><input id="ifssendindividual" type="checkbox" name="ifssendindividual"<?php echo ($useIndividual==='true')?' checked="checked"':'';?> value="true"/></p></td>
					<td><p></p></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<p>
							<input type="button" value="Save" onclick="ifsSubmit();"/>
						</p>
					</td>
					<td><p></p></td>
				</tr>
			</table>
		</form>
		<h2>Result</h2>
		<div id="ifsresult">
		</div>
	<?php
}

?>