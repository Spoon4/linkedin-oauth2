(function ( $ ) {
	"use strict";

	function sendShareAjaxRequest(postData) {
		$.ajax({
		    type : "post",
		    dataType : "json",
		    url : AjaxParams.share_api_url,
		    data : postData,
		    success: function(response) {
		        if(response.type === "success") { // comment success
		            setCommentInfoMessage(response.msgError, "wp");
		            insertComment(response.commentHtml);
		            resetCommentCount(response.commentCount);
		            _gaq.push(['_trackEvent', 'Comment', 'Post', 'To_Wordpress']);
		        } else { // comment failed : response.msgError
		            setCommentInfoMessage("Unable to post the comment on Content Loop : " + response.msgError, "wp");
		        }
		    },
		    error: function(response) {// comment failed : impossible de se connecter au serveur
		        setCommentInfoMessage("Unable to post the comment on Content Loop : unabled to join server.", "wp");
		    }
		});
	}
	
	function sendProfileAjaxRequest(postData) {
		$.ajax({
		    type : "post",
		    dataType : "json",
		    url : AjaxParams.profile_api_url,
		    data : postData,
		    success: function(response) {
		        
		    },
		    error: function(response) {// comment failed : impossible de se connecter au serveur
		        
		    }
		});
	};

	$(function () {
	});

}(jQuery));