var form           = $('form');
var check          = $('#check');
var action         = $('#action');
var messages       = $('#messages');
var log_txt        = '';
var fields_changed = [];

$('form p.aright a.button').click(function(){
	if ($(this).hasClass('save')) {
		action.val('save');
		form.submit();
		return false;
	} else if ($(this).hasClass('saveback')) {
		action.val('saveback');
		form.submit();
		return false;
	} else if ($(this).hasClass('savecreate')) {
		action.val('savecreate');
		form.submit();
		return false;
	} else if ($(this).hasClass('close')) {
		if (fields_changed.length) {
			confirmRedirect('Os seguintes campos foram alterados:', 'Você tem certeza que deseja fechar o formulário sem salvar?', 'index.php');
		} else {
			history.go(-1);
		}
		return false;
	} else if ($(this).hasClass('new')) {
		if (fields_changed.length) {
			confirmRedirect('Os seguintes campos foram alterados:', 'Você tem certeza que deseja criar um novo registro sem salvar o atual?', 'form.php');
		} else {
			window.location='form.php';
		}
		return false;
	} else if ($(this).hasClass('remove')) {
		action.val('remove');
		form.submit();
		return false;
	}
});

form.find('input, select, textarea').change(function(){
	id = $(this).attr('id');
	nome = $('label[for='+id+']').html();
	if (nome == null) {
		nome = $(this).attr('name');
	}
	if (!checkInArray(fields_changed,id)) {
		fields_changed[fields_changed.length] = id;
		log_txt = log_txt + '> O campo "' + nome + '" foi alterado;\n';
	}
});

form.submit(function() {
	var msg;
            msg = "";
	form.children('.required').each(function(){
		if ($(this).val() == "" || ($(this).val() == 0 && !$(this).hasClass('allowZero'))) {
			$(this).css({'border':'1px solid red'});
			id = $(this).attr('id');
			nome = $('label[for='+id+']').html();
			msg = msg + "O campo \""+nome+"\" não foi preenchido corretamente.<br/>";
		}
	});

	//CHECK PASSWORD
	var password = $('#password').val();
	var password_conf = $("#password_conf").val();
	if (password != password_conf) {
		nome = $('label[for="password"]').html();
		msg = msg + "O campo \""+nome+"\" não está igual a sua confirmação.<br/>";
	}

    //VALIDAÇÃO SOMENTE PARA A PAGINA DE ENQUETES
    if ($('.content').hasClass('enquetes')) {
        $total_opcoes = $('.opcoes').length;
        if($total_opcoes < 2){
            alert('A enquete deve possuir no mínimo 2 opções.');
            return false;
        }
    }

    //VALIDAÇÃO PARA EMAIL
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    var emailaddressVal = $(".email").val();

    $('.email').each(function(){
        var emailaddressVal = $(this).val();

        if(emailaddressVal != '' && (!emailReg.test(emailaddressVal))) {
			$(this).css({'border':'1px solid red'});
			id = $(this).attr('id');
			nome = $('label[for='+id+']').html();
            msg = msg + "O campo \""+nome+"\" não foi preenchido corretamente.<br/>";
        }
    });

    //VALIDAÇÃO PARA NUMEROS
    var numberReg = /[0-9]/;

    $(".mask-pint").each(function(){
        var numberVal = $(this).val();

        if(numberVal != '' && (!numberReg.test(numberVal))) {
			$(this).css({'border':'1px solid red'});
			id = $(this).attr('id');
			nome = $('label[for='+id+']').html();
            msg = msg + "O campo \""+nome+"\" não foi preenchido corretamente.<br/>";
        }
    });

	if (msg != "") {
		alert(msg);
		return false;
	} else {
		check.val('1');
		$("#history_log").val(log_txt);
		return true;
	}
});

$('#estado').change(function(){
    var estado = $(this);
    var cidade;
    if(cidade = $("#cidade")) {
        var estado_nome = estado.find("option[value="+ estado.val() +"]").html();
        $("label[for="+ cidade.attr('id') +"]").html("Carregando...");
        $.ajax({
            type: "GET",
            url: "/inc/ajax_cidade_estado.php",
            data: "estado=" + estado.val(),
            success: function(data) {
                $("label[for="+ cidade.attr('id') +"]").html("Cidades ("+ estado_nome +")");
                empty_option = "<option value=\"\" selected>&nbsp;</option>";
                cidade.html(empty_option);
                cidade.append(data);
                cidade.focusout();
            }
        });
    } else {
    	alert('Ocorreu um erro ao tentar carregar as cidades. Avise o administrador do sistema.');
    }
});
