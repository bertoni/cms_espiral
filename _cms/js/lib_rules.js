function openClose(obj) {
	var cla = obj.attr('href');
	if (obj.hasClass('active') == true) {
		obj.removeClass('active');
		$('.'+cla).hide();
	} else {
		obj.addClass('active');
		$('.'+cla).fadeIn(300);
	}
}

function setRuleProfile(rule, profile, value, ct_module, tp_content) {
	$.ajax({
		type: 'POST',
		url: '/inc/functions_ajax.inc.php',
		data: 'rule=' + rule + '&profile=' + profile + '&content_module=' + ct_module + '&type_content=' + tp_content + '&value=' + value + '&action=setRuleProfileContentModuleTypeContent',
		dataType: 'json',
		async: true,
		success: function(data) {
			if (data.status == '0') {
				if ($(this).is(':checked') == true) {
					$(this).attr('checked', false);
				} else {
					$(this).attr('checked', true);
				}
				setaMsgGeral('erro', data.msg);
			}
		}
	});
}

function setRuleUser(rule, user, value, ct_module, tp_content) {
	$.ajax({
		type: 'POST',
		url: '/inc/functions_ajax.inc.php',
		data: 'rule=' + rule + '&user=' + user + '&content_module=' + ct_module + '&type_content=' + tp_content + '&value=' + value + '&action=setRuleUserContentModuleTypeContent',
		dataType: 'json',
		async: true,
		success: function(data) {
			if (data.status == '0') {
				setaMsgGeral('erro', data.msg);
			}
		}
	});
}

function setRuleTypeContent(rule, tp_content, value) {
	$.ajax({
		type: 'POST',
		url: '/inc/functions_ajax.inc.php',
		data: 'rule=' + rule + '&type_content=' + tp_content + '&value=' + value + '&action=setRuleActionTypeContent',
		dataType: 'json',
		async: true,
		success: function(data) {
			if (data.status == '0') {
				setaMsgGeral('erro', data.msg);
			}
		}
	});
}

function setBackProfileMo(obj, id) {
	var val = obj.is(':checked');
	if (val) {
    	var clas = obj.parent().attr('class');
    	$('a.open_more').each(function(){
    		if ($(this).attr('href') == clas) {
    			$(this).parent().find('input[type="checkbox"]').attr('checked', val);

    			var ref  = $(this).parent().find('input[type="checkbox"]').val().split('|');
    			var prof = id;

    			setRuleProfile(ref[0], prof, (val ? 1 : 0), ref[1], '');

    			setBackProfileMo($(this).parent().find('input[type="checkbox"]'), id);
    		}
    	});
	}
}

function setBackProfileTy(obj, id) {
	var val = obj.is(':checked');
	if (val) {
    	var clas = obj.parent().attr('class');
    	$('a.open_more').each(function(){
    		if ($(this).attr('href') == clas) {
    			$(this).parent().find('input[type="checkbox"]').attr('checked', val);

    			var ref  = $(this).parent().find('input[type="checkbox"]').val().split('|');
    			var prof = id;

    			setRuleProfile(ref[0], prof, (val ? 1 : 0), '', ref[1]);

    			setBackProfileTy($(this).parent().find('input[type="checkbox"]'), id);
    		}
    	});
	}
}

function setBackUserMo(obj, id) {
	var val = obj.val().split('|');
	if (val[0] == 1) {
    	var clas = obj.parent().attr('class');
    	$('a.open_more').each(function(){
    		if ($(this).attr('href') == clas) {

    			$(this).parent().find('input[type="radio"]').each(function(){
    				var  val_parent = $(this).val().split('|');
    				if (val_parent[0] == val[0]) {
    					$(this).attr('checked', true);
    					setRuleUser(val_parent[1], id, val[0], val_parent[2]);
    				}
    			});
    			setBackUserMo($(this).parent().find('input[type="radio"]'), id);
    		}
    	});
	}
}

function setBackUserTy(obj, id) {
	var val = obj.val().split('|');
	if (val[0] == 1) {
    	var clas = obj.parent().attr('class');
    	$('a.open_more').each(function(){
    		if ($(this).attr('href') == clas) {

    			$(this).parent().find('input[type="radio"]').each(function(){
    				var  val_parent = $(this).val().split('|');
    				if (val_parent[0] == val[0]) {
    					$(this).attr('checked', true);
    					setRuleUser(val_parent[1], id, val[0], '', val_parent[2]);
    				}
    			});
    			setBackUserTy($(this).parent().find('input[type="radio"]'), id);
    		}
    	});
	}
}

function setBackTypesContents(obj, id) {
	var val = obj.is(':checked');
	if (val) {
    	var clas = obj.parent().attr('class');
    	$('a.open_more').each(function(){
    		if ($(this).attr('href') == clas) {
    			$(this).parent().find('input[type="checkbox"]').attr('checked', val);

    			var ref  = $(this).parent().find('input[type="checkbox"]').val().split('|');

    			setRuleTypeContent(ref[0], ref[1], (val ? 1 : 0));

    			setBackTypesContents($(this).parent().find('input[type="checkbox"]'), id);
    		}
    	});
	}
}

function setFrontProfileMo(obj, id, val) {
	obj.each(function(){
		var refe = $(this).find('input[type="checkbox"]').val().split('|');
		setRuleProfile(refe[0], id, (val ? 1 : 0), refe[1], '');

		$(this).find('input[type="checkbox"]').attr('checked', val);
	});
}

function setFrontProfileTy(obj, id, val) {
	obj.each(function(){
		var refe = $(this).find('input[type="checkbox"]').val().split('|');
		setRuleProfile(refe[0], id, (val ? 1 : 0), '', refe[1]);

		$(this).find('input[type="checkbox"]').attr('checked', val);
	});
}

function setFrontUserMo(obj, id, val) {
	obj.each(function(){
		var refe = $(this).find('input[type="radio"]:checked').val().split('|');
		setRuleUser(refe[1], id, val, refe[2]);

		if (val != refe[0]) {
			$(this).find('input[type="radio"]').each(function(){
				if ($(this).val() == (val+'|'+refe[1]+'|'+refe[2])) {
					$(this).attr('checked', true);
				}
			});
		}
	});
}

function setFrontUserTy(obj, id, val) {
	obj.each(function(){
		var refe = $(this).find('input[type="radio"]:checked').val().split('|');
		setRuleUser(refe[1], id, val, '', refe[2]);

		if (val != refe[0]) {
			$(this).find('input[type="radio"]').each(function(){
				if ($(this).val() == (val+'|'+refe[1]+'|'+refe[2])) {
					$(this).attr('checked', true);
				}
			});
		}
	});
}

function setFrontTypesContents(obj, id, val) {
	obj.each(function(){
		var refe = $(this).find('input[type="checkbox"]').val().split('|');
		setRuleTypeContent(refe[0], refe[1], (val ? 1 : 0));
		$(this).find('input[type="checkbox"]').attr('checked', val);
	});
}

$(document).ready(function(){
	$('a.open_more').click(function(e){
		e.preventDefault();
		openClose($(this));
	});

	$('a.show_all').click(function(e){
        e.preventDefault();
        if ($(this).hasClass('active') == true) {
            $(this).removeClass('active');
            $(this).parent().parent().find('.show-all').hide();
        } else {
            $(this).addClass('active');
            $(this).parent().parent().find('.show-all').fadeIn(300);
        }
	});
});