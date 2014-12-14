function showChatRoomDetails(room_id) {	
	window.location.href = "view_users_in_chatroom.php?action=expel&room_id=" + room_id;
}

/* for view_users.php */
function toggleAllUsers(checkedState, id) {
	var aCheckBoxes = document.body.getElementsByTagName("input");
	for ( var i=0; i<aCheckBoxes.length; i++ ) {
		var checkBox = aCheckBoxes[i];
		if ( checkBox.id == "doRemove" ) 
			continue;
			
		checkBox.checked = checkedState;
	}
}

function onApplyToUsers() {
	var getParams = "?remove_users_id=";
	
	var action_users_element = document.getElementById("action_users");
	var action_type = action_users_element.value;
	
	if ( action_type == "remove" ) {
		var aCheckBoxes = document.body.getElementsByTagName("input");
		for ( var i=0; i<aCheckBoxes.length; i++ ) {
			var checkBox = aCheckBoxes[i];
			if ( checkBox.id == "doRemove" || checkBox.id == "cb") 
				continue;
				
			if ( checkBox.checked ) {
				var id = checkBox.id;
				getParams += id.substring(5).toString() + ",";
			}
		}
		
		getParams = getParams.substring(0, getParams.length-1);
		
		window.location.href = "remove_users.php" + getParams + "&paged=" + paged;
	}
}

function overRemoveActionLink(id) {
	
	var user_id = id.substring(5);
	
	var aDivs = document.body.getElementsByTagName("div");
	for ( var i=0; i<aDivs.length; i++ ) {
		var div = aDivs[i];
		var divId = div.id;
		
		var sig_DivId = divId.substring(0, 11);
		
		if ( sig_DivId != "action_div_" ) 
			continue;
			
		var each_user_id = divId.substring(11);
		if ( user_id == each_user_id )
			div.style.cssText = "visibility:show";
		else
			div.style.cssText = "visibility:hidden";
	}
}

function outRemoveActionLink(id) {
	
	var user_id = id.substring(5);
	
	var aDivs = document.body.getElementsByTagName("div");
	for ( var i=0; i<aDivs.length; i++ ) {
		var div = aDivs[i];
		var divId = div.id;
		
		var sig_DivId = divId.substring(0, 11);
		
		if ( sig_DivId != "action_div_" ) 
			continue;
			
		var each_user_id = divId.substring(11);
		if ( user_id == each_user_id )
			div.style.cssText = "visibility:hidden";
	}
}

/* for view_users_in_chatroom.php */
function toggleAllUsersInChatroom(checkedState, id) {
	var aCheckBoxes = document.body.getElementsByTagName("input");
	for ( var i=0; i<aCheckBoxes.length; i++ ) {
		var checkBox = aCheckBoxes[i];
		if ( checkBox.id == "doExpel" ) 
			continue;
			
		checkBox.checked = checkedState;
	}
}

function onApplyToUsersInChatroom(room_id) {
	alert("room id :  " + room_id);
	var getParams = "?expel_users_id=";
	
	var action_users_element = document.getElementById("action_users");
	var action_type = action_users_element.value;
	
	if ( action_type == "expel" ) {
		var aCheckBoxes = document.body.getElementsByTagName("input");
		for ( var i=0; i<aCheckBoxes.length; i++ ) {
			var checkBox = aCheckBoxes[i];
			if ( checkBox.id == "doExpel" || checkBox.id == "cb") 
				continue;
				
			if ( checkBox.checked ) {
				var id = checkBox.id;
				getParams += id.substring(5).toString() + ",";
			}
		}
		
		getParams = getParams.substring(0, getParams.length-1);
		
		window.location.href = "expel_users.php" + getParams + "&paged=" + paged + "&room_id=" + room_id;
	}
}
/*
function overRemoveActionLink(id) {
	
	var user_id = id.substring(5);
	
	var aDivs = document.body.getElementsByTagName("div");
	for ( var i=0; i<aDivs.length; i++ ) {
		var div = aDivs[i];
		var divId = div.id;
		
		var sig_DivId = divId.substring(0, 11);
		
		if ( sig_DivId != "action_div_" ) 
			continue;
			
		var each_user_id = divId.substring(11);
		if ( user_id == each_user_id )
			div.style.cssText = "visibility:show";
		else
			div.style.cssText = "visibility:hidden";
	}
}

function outRemoveActionLink(id) {
	
	var user_id = id.substring(5);
	
	var aDivs = document.body.getElementsByTagName("div");
	for ( var i=0; i<aDivs.length; i++ ) {
		var div = aDivs[i];
		var divId = div.id;
		
		var sig_DivId = divId.substring(0, 11);
		
		if ( sig_DivId != "action_div_" ) 
			continue;
			
		var each_user_id = divId.substring(11);
		if ( user_id == each_user_id )
			div.style.cssText = "visibility:hidden";
	}
}
*/