/*author: ben taiba*/

function show_hide_chatbox() {
	document.getElementById('post-div').style.display = "block";
	element = document.getElementById('postbox').style;
	element.display = 'block';
}

function updateTop5(pagerId, pageeId) {
	// update the messages for this hobber
	document.getElementById('messages_file').innerHTML = "<iframe src='php_includes/get_messages.php?pagerId="+ pagerId +"&pageeId="+ pageeId +"' style='width: 250px;height: 250px;border: none;margin: 0px 0;'></iframe>";

	setTimeout(function() {updateMessages(pagerId, pageeId)}, 5000);
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	element = document.getElementById('post-div');
    if (event.target == element) {
        element.style.display = "none";
    }
}

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});