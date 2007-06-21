function markall() {
	var admins = document.getElementsByName("admins[]");

	for (i=0; i < admins[0].options.length; i++) {
		admins[0].options[i].selected = true;
	}
}

function del() {
	var admins = document.getElementsByName("admins[]");
	var nonadmins = document.getElementsByName("nonadmins[]");

	for (i=0; i < admins[0].options.length; i++) {
		if (admins[0].options[i].selected == true) {
			nonadmins[0].appendChild(admins[0].options[i]);
		}
	}
}

function add() {
	var admins = document.getElementsByName("admins[]");
	var nonadmins = document.getElementsByName("nonadmins[]");

	for (i=0; i < nonadmins[0].options.length; i++) {
		if (nonadmins[0].options[i].selected == true) {
			admins[0].appendChild(nonadmins[0].options[i]);
		}
	}
}
