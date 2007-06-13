function markall() {
	for (i=0; i<document.forms[0].elements.length; i++) {
		if (document.forms[0].elements[i].type == "select-multiple") {
			for (c=0; c<document.forms[0].elements[i].options.length; c++) {
				document.forms[0].elements[i].options[c].selected = true;
			}
			break;
		}
	}
}

function del() {
	var text;
	var value;

	for (i=0; i<document.forms[0].elements.length; i++) {
		if (document.forms[0].elements[i].type == "select-multiple") {
			for (c=0; c<document.forms[0].elements[i].options.length; c++) {
				if (document.forms[0].elements[i].options[c].selected == true) {
					text = document.forms[0].elements[i].options[c].text;
					value = document.forms[0].elements[i].options[c].value;

					document.forms[0].elements[i].options[c] = null;

					newOption = new Option(text, value, false, false);

					document.forms[0].elements[i+3].options[document.forms[0].elements[i+3].options.length] = newOption;
				}
			}
			break;
		}
	}
}

function add() {
	var text;
	var value;

	for (i=0; i<document.forms[0].elements.length; i++) {
		if (document.forms[0].elements[i].type == "select-multiple") {
			for (c=0; c<document.forms[0].elements[i+3].options.length; c++) {
				if (document.forms[0].elements[i+3].options[c].selected == true) {
					text = document.forms[0].elements[i+3].options[c].text;
					value = document.forms[0].elements[i+3].options[c].value;

					document.forms[0].elements[i+3].options[c] = null;

					newOption = new Option(text, value, false, false);

					document.forms[0].elements[i].options[document.forms[0].elements[i].options.length] = newOption;
				}
			}
			break;
		}
	}

}
