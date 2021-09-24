
<script type="text/javascript">
	/*author: ben taiba*/

	var picWidth = 0; // incrementing picture width
	var picSizeLimit = 40; // picture size limit
	var picSizeCounter = 0; // counter for number of image increments

	/*Show or hide the post box on click the post icon*/
	function show_hide_postbox() {
		document.getElementById('post-div').style.display = "block";
		element = document.getElementById('postbox').style;
		element.display = 'block';
	}

	/*Show field to add a new interest*/
	function interest() {
	  document.getElementById('interest-field').innerHTML = "<input type='text' class='form-control' id='interested' name='interested' placeholder='interested in?'><span class='btn btn-default btn-sm' onclick='addInterest()' style='cursor: pointer;'>+</span>";
	}

	/*Add a new interest to user's interests list*/
	function addInterest(){ 
	  var user = document.getElementById('userpass').value;
	  var areaOfInterest = document.getElementById('interested').value;

	  if (areaOfInterest.length == 0) {
	    // return function if field is empty
	    return;
	  }
	  
	  var agent = new XMLHttpRequest();
	  
	  if (agent) {
	    var ds = "codesPHP/add_interest.php?user="+user+"&aoi="+areaOfInterest; // data source
	    agent.open("GET", ds, true);

	    agent.onreadystatechange = function(){
	      if(agent.readyState == 4 && agent.status == 200){ //alert(agent.responseText);
	        if (agent.responseText == '1') {
	          document.getElementById('new-field').innerHTML += "<p><a href='home.php?sort="+areaOfInterest+"'><span class='label label-default'>"+ areaOfInterest +"</span></a></p>";
	          document.getElementById('interest-field').innerHTML = "<p><span class='btn btn-default btn-sm' onclick='interest()' style='cursor: pointer;margin-top:10px;'>Add</span></p>";
	        } else { alert(agent.responseText); }
	      }
	    }
	  }
	  agent.send(null);
	}

	/*When a user likes a post*/
	function thumbsUp(postId, likedBy) {
	  	currentNumLikes = document.getElementById("num_likes_"+postId).innerHTML; // number of likes

	  // mark the post liked by this user
	  var agent = new XMLHttpRequest();
	  
	  if (agent) {
	    var ds = "codesPHP/like.php?postId="+postId+"&likedBy="+likedBy; // data source
	    agent.open("GET", ds, true);
	    
	    agent.onreadystatechange = function(){
	      if (agent.readyState == 4 && agent.status == 200){
	        if (agent.responseText == '1') {
	          // add 1 to this like numbers
	          document.getElementById("num_likes_"+postId).innerHTML = parseInt(currentNumLikes)+1;
	          // get the clicked thumbs up button and change it to green thumb
	          $('#up-'+postId)
	            .attr('src', 'icons/greenthumb.png')
	            .attr('onclick', '')
	            .width(20)
	            .height(20);

	          // reload page
	          //location.reload();
	        } else {
	          alert(agent.responseText);
	        }
	      }
	    }
	  }
	  agent.send(null);
	}

	/* initialise the like animation */
	function initLike(postId, likedBy){
		thumbsUp(postId, likedBy);
		//(document.getElementById(pageeId).offsetWidth + 40) + 'px';
		var img = document.getElementById('pic-'+postId).style;
		document.getElementById('like-btn').style.zIndex = 100;
		document.getElementById('like-btn').style.position = 'absolute';
		document.getElementById('like-btn').style.top = (img.offsetTop) + 'px';
		document.getElementById('like-btn').style.left = (img.offsetWidth) + 'px';
		document.getElementById('like-btn').style.display = 'block';

		increaseImage();

		return;
	}

	/* like animation */
	function increaseImage() {
		document.getElementById('like-btn').width = (picWidth+=4);

		if (++picSizeCounter==picSizeLimit) { 
			count = 0;
			while(count < 30) { document.getElementById('like-btn').width = (picWidth-=4); ++count; }
			picWidth=0;picSizeLimit=40;picSizeCounter=0;
			//document.getElementById('like-div').style.display = 'none';
			// end function call
			return; 
		}
		setTimeout(function(){increaseImage()}, 5);
	}

	/*Hobb a friend*/
	function addFriend(firstUsername, secondUsername, postId) {
	  // add a friend to this guy

	  // mark the post liked by this user
	  var agent = new XMLHttpRequest();
	  
	  if (agent) {
	    var ds = "codesPHP/connect_people.php?first="+firstUsername+"&second="+secondUsername; // data source
	    agent.open("GET", ds, true);
	    
	    agent.onreadystatechange = function(){
	      if(agent.readyState == 4 && agent.status == 200){
	        if (agent.responseText == "1") {
	          // get the clicked thumbs up button and change it to green thumb
	          //document.getElementById("add-"+postId).style.display = "none";
	          window.location.href = "home.php?ref=add-"+postId;
	        }
	      }
	    }
	  }
	  agent.send(null);

	  document.getElementById(postId).style.display = 'none';
	}

	/*Comment on post*/
	function commentOnPost(postId, username){ 
	  // get comment and usernames
	  comment = document.getElementById("comment_box_"+postId).value;
	  usernames = document.getElementById('usernames').value;

	  // check that comment is not empty
	  if (comment.length == 0) { return; }

	  var agent = new XMLHttpRequest();
	  
	  if (agent) {
	    var ds = "codesPHP/comment_on_post.php?postId="+postId+"&username="+username+"&comment="+comment; // data source
	    agent.open("GET", ds, true);
	    
	    agent.onreadystatechange = function(){
	      if(agent.readyState == 4 && agent.status == 200){
	        if (agent.responseText) {//alert(postId+username+comment+usernames);
	          document.getElementById("new_comment_"+postId).innerHTML += "<p class='small-text'>"+usernames+": "+comment+"</p>";
	          comment = document.getElementById("comment_box_"+postId).value = "";
	        } else {
	          alert(agent.responseText);
	        }
	      }
	    }
	  }
	  agent.send(null);
	}

	/* Show the 'x' icon */
	function showRemove(id) {
		document.getElementById(id+"-x").style.visibility = "visible";
	}

	/* Hide the 'x' icon */
	function hideRemove(id) {
		document.getElementById(id+"-x").style.visibility = "hidden";
	}

	/* Remove interest for this user*/
	function removeInterest(interest, user) {
		var agent = new XMLHttpRequest();
	  
		if (agent) {
			var ds = "codesPHP/remove_interest.php?interest="+interest+"&user="+user; // data source
			agent.open("GET", ds, true);

			agent.onreadystatechange = function(){
			  if(agent.readyState == 4 && agent.status == 200){
			    if (agent.responseText == '1') {
			      document.getElementById(""+interest).style.display = "none";
			    } else {
			      alert(agent.responseText);
			    }
			  }
			}
		}
		agent.send(null);
	}

	function messageFromUser() {
		var text = document.getElementById("message_to_admin").value;
		// verify text field is not empty
		if (text.length == 0) {
			return;
		}
		var current_url = "";
		var agent = new XMLHttpRequest();
	  
		if (agent) {
			var ds = "codesPHP/user_message.php?message="+text; // data source
			agent.open("GET", ds, true);

			agent.onreadystatechange = function(){
			  if(agent.readyState == 4 && agent.status == 200){
			    if (agent.responseText == '1') {
			    	if (window.location.href.indexOf("?") > -1) {
			    		window.location.href = window.location.href + "&msg=Thanks for messaging us!";
			    	} else {
			    		window.location.href = window.location.href + "?msg=Thanks for messaging us!";
			    	}
			    } else {
			      alert(agent.responseText);
			    }
			  }
			}
		}
		agent.send(null);
	}

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		element = document.getElementById('post-div');
	    if (event.target == element) {
	        element.style.display = "none";
	    }

	    document.getElementById('search-result').style.display = "none";
	}

	function escape(event) {
		var x = event.keyCode;
		if (x == 27) {
			document.getElementById('msg-display').style.display = 'none';
		}
	}
</script>

