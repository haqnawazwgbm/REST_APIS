$(document).ready(function(){
	$('.alert-noti span.fa.fa-times').click(function(){
		$('.alert-noti').css({'visibility':'hidden','opacity':'0','z-index':'-1'});
	});
	$('button.menu-btn').click(function(){
		alert();
		$('.main-sidebar-bg').toggleClass('show-menu-bar');
	});
	$('button.todo-list-bar').click(function(){
		alert();
		$('.todo-main-main').toggleClass('show-todo-bar');
	});


});
function startTime() {
		var today = new Date();
		var h = today.getHours();
		var m = today.getMinutes();
		var s = today.getSeconds();
		m = checkTime(m);
		s = checkTime(s);
		document.getElementById('time').innerHTML =
		h + ":" + m + ":" + s;
		var t = setTimeout(startTime, 500);
	}
	function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}