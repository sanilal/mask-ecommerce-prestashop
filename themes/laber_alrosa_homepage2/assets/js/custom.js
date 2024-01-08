/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
$(function(){
    $('.laberProd-cate .laber-Tab1 .nav-item').click(function(){
        $('.laberProd-cate .laber-Tab1 .nav-item').removeClass('laber-active');
        $(this).addClass('laber-active');
    });
});

$(document).ready(function ()
{	
	loadding();
	
});
$(document).ready(function () {
    $("i.pe-7s-search").click(function(){
         $("input#input_search").focus();
    })
	$("#grid a").click(function(e) {
		$("#products").removeClass("active_list");
        $("#products").addClass("active_grid");
		setCookie('status_list_product','active_grid',1);
    });
	$("#list a").click(function(e) {
        $("#products").removeClass("active_grid");
		$("#products").addClass("active_list");
		setCookie('status_list_product','active_list',1);
    });
		if(getCookie('status_list_product')!=="" && getCookie('status_list_product')!=="active_grid"){
			$("#products").removeClass("active_grid");
			$("#products").addClass("active_list");
		}
	
	var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
	
$('div#promo-code button.btn.btn-primary').click(function(){
	setTimeout(function(){
      location.reload(true);
    }, 5000); 
})
$('li.cart-summary-line a').click(function(){
	setTimeout(function(){
      location.reload(true);
    }, 5000); 
})

	
	
});
function loadding() {
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
}
	
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}
