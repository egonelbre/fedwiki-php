document.addEventListener("DOMContentLoaded", function(event){
	"use strict";
	var signinLink = document.getElementById('signin');
	if (signinLink) {
		signinLink.onclick = function(ev) {
			ev.preventDefault();
			navigator.id.request();
		};
	}

	var signoutLink = document.getElementById('signout');
	if (signoutLink) {
		signoutLink.onclick = function(ev) {
			ev.preventDefault();
			navigator.id.logout();
		};
	}

	navigator.id.watch({
		loggedInUser: "",
		onlogin: function(assertion){
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "/login", true);
			
			var formData = new FormData();
			formData.append("assertion", assertion);
			xhr.send(formData);
			xhr.onload = function(ev){
				if(xhr.status === 200){
					signoutLink.classList.remove("hidden");
					signinLink.classList.add("hidden");
				} else {
					navigator.id.logout();
				}
			};
		},
		onlogout: function(){
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "/logout", true);
			var formData = new FormData();
			formData.append("logout", true);
			xhr.send(formData);
			xhr.onload = function(ev){
				signinLink.classList.remove("hidden");
				signoutLink.classList.add("hidden");
			};
		}
	});	
});