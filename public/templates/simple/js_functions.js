// marks all list entries in element "admins[]"
function markAllAdmins() {
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

// marks all list entries in element "adminofdomains[]"
function markAllDomains() {
	var nlo_adminofdomains = document.getElementsByName("nlo_adminofdomains[]");

	for (i=0; i < nlo_adminofdomains[0].options.length; i++) {
		nlo_adminofdomains[0].options[i].selected = true;
	}
}

// moves a domain from the "adminofdomains[]" element to the "domains[]" element
// this means that the domain is removed from the user's list of administrated
// domains
function delDomain() {
	var nlo_adminofdomains = document.getElementsByName("nlo_adminofdomains[]");
	var nlo_availabledomains = document.getElementsByName("nlo_availabledomains[]");

	for (i=0; i < nlo_adminofdomains[0].options.length; i++) {
		if (nlo_adminofdomains[0].options[i].selected == true) {
			nlo_availabledomains[0].appendChild(nlo_adminofdomains[0].options[i]);
		}
	}
}

// moves a domain from the "domains[]" element to the "adminofdomains[]" element
// this means that the domain is added to the user's list of administrated
// domains
function addDomain() {
	var nlo_adminofdomains = document.getElementsByName("nlo_adminofdomains[]");
	var nlo_availabledomains = document.getElementsByName("nlo_availabledomains[]");

	for (i=0; i < nlo_availabledomains[0].options.length; i++) {
		if (nlo_availabledomains[0].options[i].selected == true) {
			nlo_adminofdomains[0].appendChild(nlo_availabledomains[0].options[i]);
		}
	}
}

// hide redirectoptions when redirect is disabled, show redirectoptions when redirect is enabled
function switchRedirect() {
	var redirect = document.getElementsByName("nlo_redirectstatus");
	var keep = document.getElementsByName("nlo_keepstatus");
	var keeptr = document.getElementById("keepoption");
	var recipienttr = document.getElementById("recipientoption");

	if (redirect[0].checked == true) {
		var display = "table-row";
	} else {
		var display = "none";
	}

	keeptr.style.display = display;
	keep[0].checked = true;
	recipienttr.style.display = display;
}
