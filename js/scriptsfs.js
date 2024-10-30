function myAlert(response) {
	alert('Got this from the server: ' + response);				
}

function merge_objects(obj1,obj2){
	var obj3 = {};
	for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
	for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
	return obj3;
}

function FeCallBack(response) {
	contentDiv=document.getElementById('frontendaddemail');
	//window.alert('Callback1: '+typeof(contentDiv));
	if (typeof(contentDiv)=='object') {
		if (contentDiv!=null) {
			if (typeof(contentDiv.innerHTML)=='string') {
				contentDiv.innerHTML=response;
			}
		}
	}
}

function ifsFEAjaxCall(task,parameters) {
	//window.alert('Task: '+task);
	if (typeof(task)=='undefined') {
		task='';
	}
	jQuery(document).ready(function($) {
		nameX='na';
		if (task=='submit') {
			//nameX=document.getElementById('nameid').value;
			displayArea=document.getElementById('frontendaddemail');
			emailX=document.getElementById('emailid').value;
		}
		else {
			emailX='';
		}
		if (typeof(displayArea)=='object') {
			displayArea.innerHTML='<p>Please wait...</p>';
		}
		
		var data = {
			action: 'ifs_frontend_action',
			task: task,
			name: nameX,
			email: emailX
		};
		//window.alert(typeof(parameters));
		if (typeof(parameters=='object')) {
			data=merge_objects(data,parameters);
		}
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			FeCallBack(response);
		});
	});
}
ifsFEAjaxCall('showaddemail');