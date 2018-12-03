$(".race-actions").click(function(){
	var id = $(this).data("id");
	$(".details").removeClass("active");
	$(".details."+id).addClass("active");
});