(function ( $ ) {
	"use strict";

	function sendShareAjaxRequest(postData, successCallback, errorCallback) {
		$.ajax({
		    type : "post",
		    dataType : "json",
		    url : LinkedInAjaxParams.share_api_url,
		    data : postData,
		    success: function(response) {
                successCallback(response);
		    },
		    error: function(response) {
		        errorCallback(response);
		    }
		});
	}
	
	function sendProfileAjaxRequest(successCallback, errorCallback) {
		$.ajax({
		    type : "get",
		    dataType : "json",
		    url : LinkedInAjaxParams.profile_api_url,
		    success: function(response) {
                successCallback(response);
		    },
		    error: function(response) {
		        errorCallback(response);
		    }
		});
	};

	function sendLogoutAjaxRequest(successCallback, errorCallback) {
		$.ajax({
		    type : "get",
		    dataType : "json",
		    url : LinkedInAjaxParams.logout_url,
		    success: function(response) {
                successCallback(response);
		    },
		    error: function(response) {
		        errorCallback(response);
		    }
		});
	}

	$(function () {
		
	});

}(jQuery));