<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = 'ty_fi';

$contenModule = new ContentsModule($page_code);
if (!$contenModule->exists() || $contenModule->getAttribute('status')->getAttribute('id_status') != 'a') {
    header('Location: /_cms/');
    exit;
}
require_once 'actions_rules.inc.php';
if (!$RULE['lis']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão de ver este conteúdo') . '.');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: /_cms/');
    exit;
} else {
    $type_content = new TypeContents($_GET['id']);
    if (!$type_content->exists()) {
        header('Location: /_cms/');
        exit;
    }
}

$name_type_content = $type_content->getAttribute('name_type_content');
$id_type_content   = $type_content->getAttribute('id_type_content');


$arr_config = $type_content->getAttribute('config_form');

require 'top.inc.php';
?>

<a class="button float-right" href="../"><?php echo $i18n->_('Voltar para a listagem'); ?></a>
<h1><a href="../"><?php echo $contenModule->getAttribute('name_content_module') ?></a> &raquo; <?php echo $name_type_content ?></h1>
<hr/>
<form action="proc.php" name="form_action" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="id_type_content" id="id_type_content" value="<?php echo $id_type_content ?>" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<?php
	if ($RULE['new']) {
	    echo '<a href="#" class="add_field">' . $i18n->_('Adicionar novo campo') . '</a>';
	}
	?>

	<div id="container">
		<?php
		if ($arr_config && count($arr_config) > 0) {
		    foreach ($arr_config as $key=>$config_form) {
		        $tp_html = $config_form->getAttribute('form')->getAttribute('type_html');
    		    echo '<div class="campo">';

                    echo '<span class="field-name">'.$config_form->getAttribute('label').($config_form->getAttribute('required')?'*':'').'</span>';
                    echo '<a href="#" class="more"><img src="/_cms/img/icon-fields-more.png" alt="' . $i18n->_('Mostrar campos') . '" /></a>';
                    echo '<a href="#" class="less"><img src="/_cms/img/icon-fields-less.png" alt="' . $i18n->_('Ocultar campos') . '" /></a>';

    		        if ($RULE['del']) {
    		            echo '<a href="#" class="remove float-right"><img src="/_cms/img/icon-delete-msg-hover.png" alt="remover campo" /></a>';
    		        }
    		        echo '<a href="#" class="next"><img src="/_cms/img/icon-fields-next.png" alt="' . $i18n->_('Próximo') . '" /></a>';
    		        echo '<a href="#" class="prev"><img src="/_cms/img/icon-fields-prev.png" alt="' . $i18n->_('Anterior') . '" /></a>';

                    echo '<div class="fields">';
                        echo '<label for="form_field">' . $i18n->_('Campo') . '</label>*<br/>';
                        echo '<select class="text required w30 fieldSelect" name="form_field[' . $key . ']">';
                            echo '<option value="">' . $i18n->_('Escolha o Campo') . '</option>';
                            $arr_fields = FormFields::getFormFields('', '', 'name_form_field');
                            if ($arr_fields && count($arr_fields) > 0) {
                                foreach ($arr_fields as $key1=>$field) {
                                    echo '<option value="' . $field->getAttribute('id_form_field') . '|' . $field->getAttribute('type_html') . '"' . ($config_form->getAttribute('form')->getAttribute('id_form_field') == $field->getAttribute('id_form_field') ? ' selected="selected"' : '') . '>' . $field->getAttribute('name_form_field') . '</option>';
                                }
                            }
                        echo '</select>';
                        echo '<br/><br/>';
                        echo '<label for="name">' . $i18n->_('Nome do Campo') . '</label>*<br/>';
                        echo '<input class="text required big w98" type="text" name="name[' . $key . ']" maxlength="50" value="' . $config_form->getAttribute('name') . '" />';
                        echo '<br/><br/>';
                        echo '<label for="label">' . $i18n->_('Rótulo do Campo') . '</label>*<br/>';
                        echo '<input class="text required big w98" type="text" name="label[' . $key . ']" maxlength="35" value="' . $config_form->getAttribute('label') . '" />';
                        echo '<br/><br/>';
                        echo '<label for="max_lenght"' . ($tp_html == 'input[checkbox]' || $tp_html == 'input[radio]' || $tp_html == 'select' ? ' class="hide"' : '') . '>' . $i18n->_('Tamanho máximo') . '</label><br/>';
                        echo '<input class="text big w98' . ($tp_html == 'input[checkbox]' || $tp_html == 'input[radio]' || $tp_html == 'select' ? ' hide' : '') . '" type="text" name="max_lenght[' . $key . ']" maxlength="5" value="' . (!$config_form->getAttribute('max_lenght') ? '' : $config_form->getAttribute('max_lenght')) . '" />';
                        echo '<br/><br/>';
                        echo '<label for="required">' . $i18n->_('Campo obrigatório') . '</label>*<br/>';
                        echo '<input class="radio required" type="radio" name="required[' . $key . ']"' . ($config_form->getAttribute('required') ? ' checked="checked"' : '') . ' value="1" /> <label class="radio_label">' . $i18n->_('Sim') . '</label>';
                        echo '<input class="radio required" type="radio" name="required[' . $key . ']"' . (!$config_form->getAttribute('required') ? ' checked="checked"' : '') . ' value="0" /> <label class="radio_label">' . $i18n->_('Não') . '</label>';
                        echo '<input class="required order" type="hidden" name="order[' . $key . ']" maxlength="2" value="' . $config_form->getAttribute('order_show') . '" />';
                        echo '<br/><br/>';
                        echo '<label for="use_filter">' . $i18n->_('Usado no filtro') . '</label>*<br/>';
                        echo '<select class="text required w30" name="use_filter[' . $key . ']">';
		                    echo '<option value="">' . $i18n->_('Não utilizado') . '</option>';
                            foreach ($TYPES_COMPARISON as $key=>$value) {
                                echo '<option value="' . $key . '"' . ($config_form->getAttribute('use_with_filter') == $key ? ' selected="selected"' : '') . '>' . $value . '</option>';
                            }
                        echo '</select>';
                    echo '</div>';
    		    echo '</div>';
		    }
		}
		?>
    </div>

    <?php
	if ($RULE['new']) {
	   // echo '<a href="#" class="add_field">Adicionar novo campo</a>';
	}
	if ($RULE['edt']) {
	    echo '<p class="aright">';
	    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
		echo '</p>';
	}
	?>
</form>

<script type="text/javascript">

function addElement(index) {
	var html = '';
	html += '<div class="campo new">';
	html += '<span class="field-name"><?php echo $i18n->_('Novo campo'); ?></span>';
    <?php
    if ($RULE['del']) {
        echo 'html += \'<a href="#" class="remove float-right"><img src="/_cms/img/icon-delete-msg-hover.png" alt="' . $i18n->_('remover campo') . '" /></a>\';';
    }
    ?>
    html += '<a href="#" class="next"><img src="/_cms/img/icon-fields-next.png" alt="<?php echo $i18n->_('Próximo'); ?>" /></a>';
    html += '<a href="#" class="prev"><img src="/_cms/img/icon-fields-prev.png" alt="<?php echo $i18n->_('Anterior'); ?>" /></a>';

    html += '<div class="fields">';
    html += '<label for="form_field"><?php echo $i18n->_('Campo'); ?></label>*<br/>';
    html += '<select class="text required w30 fieldSelect" name="form_field[' + index + ']">';
    html += '<option value=""><?php echo $i18n->_('Escolha o Campo'); ?></option>';
		<?php
		$arr_fields = FormFields::getFormFields('', '', 'name_form_field');
		if ($arr_fields && count($arr_fields) > 0) {
    		foreach ($arr_fields as $key=>$field) {
    			echo 'html += \'<option value="' . $field->getAttribute('id_form_field') . '|' . $field->getAttribute('type_html') . '">' . $field->getAttribute('name_form_field') . '</option>\';';
    		}
		}
		?>
	html += '</select>';

	html += '<br/><br/>';
	html += '<label for="name"><?php echo $i18n->_('Nome do Campo'); ?></label>*<br/>';
	html += '<input class="text required big w98" type="text" name="name[' + index + ']" maxlength="50" value="" />';

	html += '<br/><br/>';
	html += '<label for="label"><?php echo $i18n->_('Rótulo do Campo'); ?></label>*<br/>';
	html += '<input class="text required big w98" type="text" name="label[' + index + ']" maxlength="35" value="" />';

	html += '<br/><br/>';
	html += '<label for="max_lenght"><?php echo $i18n->_('Tamanho máximo'); ?></label><br/>';
	html += '<input class="text big w98" type="text" name="max_lenght[' + index + ']" maxlength="5" value="" />';

	html += '<br/><br/>';
	html += '<label for="required"><?php echo $i18n->_('Campo obrigatório'); ?></label>*<br/>'
	html += '<input class="radio required" type="radio" name="required[' + index + ']" value="1" /> <label class="radio_label"><?php echo $i18n->_('Sim'); ?></label>';
	html += '<input class="radio required" type="radio" name="required[' + index + ']" value="0" /> <label class="radio_label"><?php echo $i18n->_('Não'); ?></label>';

	html += '<input class="required order" type="hidden" name="order[' + index + ']" maxlength="2" value="" />';

	html += '<br/><br/>';
	html += '<label for="use_filter"><?php echo $i18n->_('Usado no filtro'); ?></label>*<br/>';
	html += '<select class="text required w30" name="use_filter[' + index + ']">';
	html += '<option value=""><?php echo $i18n->_('Não utilizado'); ?></option>';
	<?php
    foreach ($TYPES_COMPARISON as $key=>$value) {
        echo 'html += \'<option value="' . $key . '">' . $value . '</option>\';';
    }
    ?>
    html += '</select>';

	html += '</div>';
	html += '</div>';
	return html;
}

function ordenedFields() {
	$('.campo').each(function(){
		$(this).find('.order').val($(this).index());
	});
}

$(document).ready(function(){

    $('.more').click(function(e){
        e.preventDefault();
        $(this).hide();
        $(this).parent().find('.less').show();
        $(this).parent().find('.fields').slideDown(300);
    });
    $('.less').click(function(e){
        e.preventDefault();
        $(this).hide();
        $(this).parent().find('.more').show();
        $(this).parent().find('.fields').slideUp(300);
    });

	$('.prev').live('click', function(e){
		e.preventDefault();
		var current = $(this).parent();
		var order   = current.index();

		if (order) {
            $(this).parent().fadeOut(300, function(){
                $('#container div.campo:eq(' + (order-1) + ')').before(current);
                ordenedFields();
            });
            $(this).parent().fadeIn(300);
		}
	});

	$('.next').live('click', function(e){
		e.preventDefault();
		var current = $(this).parent();
		var order   = current.index();
		if ($('#container div.campo:eq(' + (order+1) + ')').length) {
            $(this).parent().fadeOut(300, function(){
                $('#container div.campo:eq(' + (order+1) + ')').after(current);
                ordenedFields();
            });
            $(this).parent().fadeIn(300);
		}
	});

	$('.add_field').click(function(e){
		e.preventDefault();
		var index = $('#container').find('.campo').size();

		var htm = addElement(index);
		$('#container').append(htm);
		if (index > 0) {
			$('.add_field').removeClass('hide');
		}
		ordenedFields();
	});

	$('.remove').live('click', function(e){
		e.preventDefault();
        $(this).parent().fadeOut(400, function(){
            $(this).remove();
        });
	});

	$('.fieldSelect').live('change', function(){
		var val   = $(this).val().split('|');
		var index = $('.fieldSelect').index(this);
		if (val[1] != 'input[checkbox]' && val[1] != 'input[radio]' && val[1] != 'select') {
			$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').removeClass('hide');
			$(this).parent().find('label[for="max_lenght"]').removeClass('hide');
    		//if (val[2] != '') {
    		//	$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').val(val[2]);
    		//	$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').addClass('required');
    		//	$(this).parent().find('label[for="max_lenght"]').html('Tamanho máximo*');
    		//} else {
    		//	$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').val('');
    		//	$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').removeClass('required');
    		//	$(this).parent().find('label[for="max_lenght"]').html('Tamanho máximo');
    		//}
		} else {
			$(this).parent().find('input[type="text"][name="max_lenght['+index+']"]').addClass('hide');
			$(this).parent().find('label[for="max_lenght"]').addClass('hide');
		}
	});

});
</script>
<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>
<?php require 'bot.inc.php'; ?>