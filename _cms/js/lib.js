function fixTableColors() {
    $('tr').removeClass('even');

    if ($('tr').length > 1) {
        var even = true;
        $('tr').each(function(){
            even = even?false:true;

            if (even) {
                $(this).addClass('even');
            }
        });
    } else {
        $('table').remove();
        $('#main-list').append("<h2>Nenhum registro foi encontrado.</h2>");
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

function fakeCheckbox(obj) {
    var check = $(obj);
    var input = check.find('input');
    if (check.hasClass('on')){
        check.removeClass('on').addClass('off');
        input.attr('checked', false);
    } else {
        check.removeClass('off').addClass('on');
        input.attr('checked', true);
    }
}

function fakeCheckboxListeners() {
    $('.fake_check input').change(function(){
        fakeCheckbox($(this).parent());
    });
    $('.fake_check label').click(function(){
        $(this).parent().click();
        return false;
    });
    $('.fake_check').click(function(){
        $(this).children('input').change();
    });
}
fakeCheckboxListeners()

function fakeRadio(obj) {
    var radio = $(obj);
    $('.fake_radio').removeClass('on').addClass('off');
    radio.removeClass('off').addClass('on');
}

function fakeRadioListeners() {
    $('.fake_radio input').change(function(){
        fakeRadio($(this).parent());
        $(this).attr('checked', true);
    });
    $('.fake_radio label').click(function(){
        $(this).parent().click();
        return false;
    });
    $('.fake_radio').click(function(){
        $(this).find('input').change();
        //$(this).find('input').attr('checked', true);
    });
}
fakeRadioListeners()

function setaMsgGeral(tipo, msg) {
    $('#msg-geral').html('<div class="' + tipo + '"><span class="baloon"></span><p>' + msg + '</p></div><a href="#">delete</a>');
    $('#msg-geral').fadeIn();
}

/*
 * Funcções que limita a entrada de caracteres em um textarea
 * input: [id do campo textarea][limite de caracteres][campo que receberá o restante]
 * output: string
 */
function limitaCampo(txarea, limite, placar) {
	total = limite;
	tam   = document.getElementById(txarea).value.length;
	rest  = total - tam;
	document.getElementById(placar).innerHTML = rest;
	if (tam > total){
		aux = document.getElementById(txarea).value;
		document.getElementById(txarea).value = aux.substring(0,total);
		document.getElementById(placar).innerHTML = 0;
	}
}



function include(file_path){
	var j = document.createElement("script"); /* criando um elemento script: </script><script></script> */
	j.type = "text/javascript"; /* informando o type como text/javacript: <script type="text/javascript"></script>*/
	j.src = file_path; /* Inserindo um src com o valor do parâmetro file_path: <script type="javascript" src="+file_path+"></script>*/
	document.body.appendChild(j); /* Inserindo o seu elemento(no caso o j) como filho(child) do  BODY: <html><body><script type="javascript" src="+file_path+"></script></body></html> */
}


function include_once(file_path) {
	var sc = document.getElementsByTagName("script");
	for (var x in sc)
	if (sc[x].src != null && sc[x].src.indexOf(file_path) != -1) return;
	include(file_path);
}

/*
 * Funçãos que limpa um combobox para que o mesmo possa ter novos valores
 * input: [campo][label]
    * output: null
 */
function resetaCombo(campo, label) {
	$("select[name='"+campo+"']").empty();
	var option = document.createElement('option');
	$( option ).attr( {value : ''} );
	$( option ).append( label );
	$("select[name='"+campo+"']").append( option );
}

$(document).ready(function(){

    $('#control_filters').click(function(e){
		e.preventDefault();
		if ($('#cont_filters').css('display') == 'none') {
			$('#control_filters').html('Esconder filtros');
			$('#control_filters').css('background-position', 'left top');
            $('#cont_filters').slideDown(200);
		} else {
			$('#control_filters').html('Filtrar resultados');
			$('#control_filters').css('background-position', 'left bottom');
            $('#cont_filters').slideUp(200);
		}
	});

    $('#msg-geral a').live('click', function(e){
        e.preventDefault();
        $('#msg-geral').fadeOut();
    });

    $('.submit').click(function(e) {
        e.preventDefault();
        $('form').submit();
    });

    $('form[name="form_search"] a.search').click(function(e){
        e.preventDefault();
        $(this).parent('form').submit();
    });


    if( $('.tinymceFull').length ) {
    	function myCustomFileBrowser(field_name, url, type, win) {
	        // Do custom browser logic
	        //win.document.forms[0].elements[field_name].value = 'my browser value';
	    	alert('teste');
	    }

    	$("head").append("<script language='JavaScript' type='text/javascript' src='/_cms/js/tiny_mce/jquery.tinymce.js'></script>");
	    $(".tinymceFull").tinymce({
			// Location of TinyMCE script
			script_url : '/_cms/js/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			//content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js"
			//file_browser_callback : "tinyBrowser"

		});
    }

    if( $('.tinymceLittle').length ){
    	$("head").append("<script type='text/javascript' src='/_cms/js/tiny_mce/jquery.tinymce.js'></script>");
	    $(".tinymceLittle").tinymce({
			// Location of TinyMCE script
			script_url : '/_cms/js/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,cite,abbr,acronym,del,ins,attribs",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,code,|,styleprops,|,visualchars,nonbreaking,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,


			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js"

		});
    }

    if ( $('.ckeditorFull').length ) {
    	//$('head').append('<script type="text/javascript" src="/_cms/js/ckeditor/ckeditor.js"></script>');
    	//$('head').append('<script type="text/javascript" src="/_cms/js/ckeditor/adapters/jquery.js"></script>');
  		//$('.ckeditorFull').ckeditor();
    	//CKEDITOR.replace("content");
    	$('.ckeditorFull').addClass('ckeditor');
    }



    if ( $('input.file').length ) {
    	$('head').append("<script type='text/javascript' src='/_cms/files/lib.js'></script>");
    	$('input.file').dblclick(function(){
			var id = $(this).attr('id');
            insert(id, 'path');
		});
    	$('.show-file-browser').click(function(){
			var id = $(this).parent().find('input.file').attr('id');
            insert(id, 'path');
		});
    }

    if ( $('.filter').length ) {
	    $(".filter").change(function(){

	        var filter   = $(this);
	        var index    = filter.index();
	        var value    = filter.val();
	        var name     = filter.attr("name");
	        var required = false;

	        $(".filter").each(function(){
	            if ($(this).index() > index) {
	                if (name == $(this).attr("name")) {
                        $(this).remove();
	                }
	            }
	        });


            if ($(filter).val() != "") {
                $(filter).after('<img class="loading-gif" alt="Carregando..." src="/_cms/img/loading.gif" />');
            }

            if (filter.hasClass("required")) {
	            required = true;
	        }

	        $.ajax({
	            type: "POST",
	            url: "/inc/functions_ajax.inc.php",
	            data: "filter="+value+"&action=getChildsByFilter",
	            dataType: "json",
	            success: function(data) {
	                if (data.status == "1") {
	                    var select = document.createElement("select");
	                    $(select).attr("name", name);
	                    $(select).addClass("filter");
	                    $(select).addClass("child");
	                    if (required) {
	                        $(select).addClass("required");
	                    }
	                    filter.after(select);
	                    $.each(data.filters, function(i, item){
	                        var option = document.createElement("option");
	                        $(option).attr("value", item.id);
	                        $(option).append(item.name);
	                        $(select).append(option);
	                    });

                        var left = '-'+($(select).width())+'px'

                        $(select).css('left', left);
                        $(select).animate({left: '10',opacity: '1'}, 300);
	                }
	            },
                complete: function() {
                    $('.loading-gif').remove();
                }
	        });
	    });
    }

});


$(window).load(function(){

	if ( $('.wysihtml5').length ) {
		$('.wysihtml5').attr('rows', '10');
    	$('head').append("<script defer='defer' type='text/javascript' src='/_cms/js/wysihtml5/advanced.js'></script>");
    	$('head').append("<script defer='defer' type='text/javascript' src='/_cms/js/wysihtml5/wysihtml5-0.3.0.min.js'></script>");
    	$('head').append("<link type='text/css' href='/_cms/js/wysihtml5/base.css' media='screen' rel='stylesheet' />");
    	$('head').append("<script defer='defer' type='text/javascript' src='/_cms/files/lib.js'></script>");
    	//$('head').append("<link type='text/css' href='/_cms/js/wysihtml5/editor.css?v=1' media='screen' rel='stylesheet' />");


    	$('.wysihtml5').each(function(i){

    		var toolbar = '<div id="wysihtml5-toolbar" class="toolbar-wsyihtml5" style="display: none;">';
        	toolbar    += 	'<header>';
        	toolbar    += 		'<ul class="commands">';
        	toolbar    += 		'<li data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="createLink" title="Insert a link" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="insertImage" title="Insert an image" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Insert headline 1" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-command-group="foreColor" class="fore-color" title="Color the selected text" class="command">';
        	toolbar    += 		'<ul>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy"></li>';
        	toolbar    += 		'<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue"></li>';
        	toolbar    += 		'</ul>';
        	toolbar    += 		'</li>';
        	toolbar    += 		'<li data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"></li>';
        	toolbar    += 		'<li data-wysihtml5-action="change_view" style="border-right: 0;" title="Show HTML" class="action"></li>';
        	toolbar    += 		'</ul>';
        	toolbar    += 	'</header>';
        	toolbar    += 	'<div data-wysihtml5-dialog="createLink" style="display: none;">';
        	toolbar    += 		'<label>';
        	toolbar    += 			'Link:';
        	toolbar    += 			'<input data-wysihtml5-dialog-field="href" value="http://">';
        	toolbar    += 		'</label>';
        	toolbar    += 		'<a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>';
        	toolbar    += 	'</div>';
        	toolbar    += 	'<div data-wysihtml5-dialog="insertImage" style="display: none;">';
        	toolbar    += 		'<label style="margin-right: 10px;">';
        	toolbar    += 			'Image:';
        	toolbar    += 			'<input id="src'+i+'" data-wysihtml5-dialog-field="src" value="">&nbsp;<a href="#" id="search-image'+i+'"><img style="vertical-align: middle;" src="/_cms/img/icon_picture_large.png" width="20" alt="buscar imagem" /></a>';
        	toolbar    += 		'</label>';
        	toolbar    += 		'&nbsp;<a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>';
        	toolbar    += 	'</div>';
        	toolbar    += '</div>';

    		$(toolbar).insertBefore($(this));
    		var scr = '<script defer="defer">';
    		scr    += 'var editor = new wysihtml5.Editor("' + $(this).attr('id') + '", {';
    		scr    += '	toolbar:      "wysihtml5-toolbar",';
    		scr    += ' stylesheets: ["http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css", "/_cms/js/wysihtml5/editor.css"],';
    		scr    += ' parserRules:  wysihtml5ParserRules,';
    		scr    += ' allowObjectResizing:  true';
    		scr    += '});';
    		scr    += 'editor.on("load", function() {';
    		scr    += 'var composer = editor.composer;';
    		scr    += 'composer.selection.selectNode(editor.composer.element.querySelector("h1"));';
    		scr    += '});';
    		scr    += '</script>';
    		$('body').append(scr);

    		$('#search-image'+i).live('click', function(){
                insert('src'+i, 'path');
    		});

    	});

    }

});


