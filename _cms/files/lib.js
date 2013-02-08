function showInElement(html, id_element) {
	view = document.getElementById('view_onmouseover');
	if (view.checked) {
		/*element = document.getElementById(id_element);
		element.innerHTML = html;
		element.style.display = 'block';*/
		$('#' + id_element).html(html);
		$('#' + id_element).show();
	}
}

function sendTag(field, tag) {
	tag = tag.replace(/#/g, '"');
	window.opener.putTag(field, tag);
	window.close();
}

function sendPath(field, path) {
	path = path.replace(/#/g, '"');
	window.opener.putPath(field, path);
        /*var thumb_path = path.split('/');
        var image_filename = '/uploads/thumbs/' + thumb_path[thumb_path.length-1];
        window.opener.putPath('image', image_filename);*/
	window.close();
}

function insert(field, type) {
	showPopFiles(field, type);
}

function showPopFiles(field, type) {
	width = screen.width / 1.5;
	height = screen.height / 1.5;

	leftVal = (screen.width - width) / 2;
	topVal = (screen.height - height) / 3.5;
	window.open('/_cms/files/files.php?field=' + field + '&type=' + type,'insert_image' + field,'width=' + width + ',height=' + height + ',scrollbars=yes,resizable=yes,top=' + topVal + ',left=' + leftVal)
}

function putTag(field, tag) {
	/*obj_field = document.getElementById(field);
	obj_field.focus();

    //IE support
    if (document.selection) {
        obj_field.focus();
        sel = document.selection.createRange();
		sel.text = tag;
        //obj_field.insert.focus();
    }
    //MOZILLA/NETSCAPE support
    else if (obj_field.selectionStart || obj_field.selectionStart == "0") {
        var startPos = obj_field.selectionStart;
        var endPos = obj_field.selectionEnd;
        var chaineSql = obj_field.value;

        obj_field.value = chaineSql.substring(0, startPos) + tag + chaineSql.substring(endPos, chaineSql.length);
    } else {
        obj_field.value += tag;
    }*/
	InsertHTML(tag);
}
function InsertHTML(tag)
{
	// Get the editor instance that we want to interact with.
	var oEditor = CKEDITOR.instances.editor1 ;

	// Check the active editing mode.
	if (oEditor.mode == 'wysiwyg' ) {
            // Insert the desired HTML.
            oEditor.insertHtml( tag ) ;
	}
	else {
            alert( 'You must be on WYSIWYG mode!' ) ;
        }
}


function putPath(field, path) {
	obj_field = document.getElementById(field);
	obj_field.value = path;
	obj_field.focus();
}

function mountTag(field, nform) {
    with(nform) {
	tag = '';
	tag = '<img src=#' + path.value + '# title=#' + title.value + '# alt=#' + title.value + '# align=#' + align.value + '# border=#' + border.value + '# hspace=#' + hspace.value + '# vspace=#' + vspace.value + '# />';
	sendTag(field, tag);
	return false;
    }
}

function goSelect(obj) {
	if (obj.value.indexOf('del') >= 0) {
		if (confirm('Você tem certeza que deseja excluir?')) {
			window.location.href = obj.value;
			return;
		} else {
			obj.selectedIndex = 0;
		}
	}

	if (obj.value.indexOf('subscriptions') >= 0) {
		//window.open(obj.value,'_blank');
		window.location.href = obj.value;
		obj.selectedIndex = 0;
	}

	if (obj.value != '') {
		window.location.href = obj.value;
		obj.selectedIndex = 0;
	}
}

function checkInArray(arr,str) {
	for (i=0; i<arr.length; i++) {
		if (arr[i] == str) {
			return true;
		}
	}
	return false;
}

function confirmRedirect(txt_alter, txt_confirm, url) {
	q_confirm = '';
	if (typeof(log_txt) == 'undefined') {
	    q_confirm = txt_confirm;
	} else {
	    if (log_txt == '') {
		    q_confirm = txt_confirm;
	    } else {
		    q_confirm = txt_alter + '\n' + log_txt + '\n' + txt_confirm;
	    }
	}
	if (confirm(q_confirm)) {
		window.location.href = url;
	}
}

function setCookie(c_name, value, expiredays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) {
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
				return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}

array_global = new Array();
function attNewTag(id_campo) {
	if (array_global[id_campo] == undefined) {
		array_global[id_campo] = 0;
	} else {
		array_global[id_campo]++;
	}
	tagcores = new Array();
	tagcores[0]   = '#FCC';
	tagcores[1]   = '#FBB';
	tagcores[2]   = '#FAA';
	tagcores[3]   = '#F99';
	tagcores[4]   = '#F88';
	tagcores[5]   = '#F88';
	tagcores[6]   = '#F99';
	tagcores[7]   = '#FAA';
	tagcores[8]   = '#FBB';
	tagcores[9]   = '#FCC';
	tagcores[10]  = '#FFF';
	if(array_global[id_campo] <= 21) {
		document.getElementById(id_campo).style.backgroundColor = tagcores[array_global[id_campo]];
		array_global[id_campo]++;
		setTimeout("attNewTag('"+id_campo+"')",100);
	} else {
		array_global[id_campo] = 1;
		return;
	}
}