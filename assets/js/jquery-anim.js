$(document).ready(function(){
	$(".nav-bar-hamburger").on("click", function(){
		$(".nav-bar-hamburger").toggleClass("nav-bar-hamburger-open");
		$("nav ul").toggleClass("open");
 	});

 	var headerlink = $(".headerlink");
 	headerlink.on("click", function(){
		$(".nav-bar-hamburger").toggleClass("nav-bar-hamburger-open");
		$("nav ul").removeClass("open");
	});

 	$(".master-popup").length == 1 ? $("main").css("display","none") : $("main").css("display","block");

});
