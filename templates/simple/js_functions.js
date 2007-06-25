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

// marks all list entries in element "domainsin[]"
function markAllDomains() {
	var domainsin = document.getElementsByName("domainsin[]");

	for (i=0; i < domainsin[0].options.length; i++) {
		domainsin[0].options[i].selected = true;
	}
}

// moves a domain from the "domainsin[]" element to the "domains[]" element
// this means that the domain is removed from the user's list of administrated
// domains
function delDomain() {
	var domainsin = document.getElementsByName("domainsin[]");
	var domains = document.getElementsByName("domains[]");

	for (i=0; i < domainsin[0].options.length; i++) {
		if (domainsin[0].options[i].selected == true) {
			domains[0].appendChild(domainsin[0].options[i]);
		}
	}
}

// moves a domain from the "domains[]" element to the "domainsin[]" element
// this means that the domain is added to the user's list of administrated
// domains
function addDomain() {
	var domainsin = document.getElementsByName("domainsin[]");
	var domains = document.getElementsByName("domains[]");

	for (i=0; i < domains[0].options.length; i++) {
		if (domains[0].options[i].selected == true) {
			domainsin[0].appendChild(domains[0].options[i]);
		}
	}
}
