// funzione per mostrare/nascondere la password
$(".toggle-password").click(function () {
	$(this).find('i').toggleClass("fa-eye-slash fa-eye");
	var input = $($(this).attr("toggle"));
	if (input.attr("type") == "password") {
		input.attr("type", "text");
	} else {
		input.attr("type", "password");
	}
});

// funzione per mostrare/nascondere la repeat password
$(".toggle-password2").click(function () {
	$(this).find('i').toggleClass("fa-eye-slash fa-eye");
	var input = $($(this).attr("toggle"));
	if (input.attr("type") == "password") {
		input.attr("type", "text");
	} else {
		input.attr("type", "password");
	}
});