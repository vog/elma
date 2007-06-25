// marks all list entries in element "admins[]" before the form is sent
function markall() {
	var admins = document.getElementsByName("admins[]");

	for (i=0; i < admins[0].options.length; i++) {
		admins[0].options[i].selected = true;
	}
}

// moves a user from the "admins[]" element to the "nonadmins[]" element
// this means that the user is removed from the admin-list
function delAdmin() {
	var admins = document.getElementsByName("admins[]");
	var nonadmins = document.getElementsByName("nonadmins[]");

	for (i=0; i < admins[0].options.length; i++) {
		if (admins[0].options[i].selected == true) {
			nonadmins[0].appendChild(admins[0].options[i]);
		}
	}
}

// moves a user from the "nonadmins[]" element to the "admins[]" element
// this means that the user is added to the admin-list
function addAdmin() {
	var admins = document.getElementsByName("admins[]");
	var nonadmins = document.getElementsByName("nonadmins[]");

	for (i=0; i < nonadmins[0].options.length; i++) {
		if (nonadmins[0].options[i].selected == true) {
			admins[0].appendChild(nonadmins[0].options[i]);
		}
	}
}
