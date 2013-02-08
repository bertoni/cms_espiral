<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'conf';
$page_code   = 'field';

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

$id_form_filed   = '';
$filter          = '';
$max_lenght      = '';
$name_form_field = '';
$type_html       = '';
$function_js     = '';
$value_default   = '';
$class_css       = '';

$form_field = new FormFields(isset($_GET['id']) ? $_GET['id'] : '');
if ($form_field->exists()) {
    $id_form_filed   = $form_field->getAttribute('id_form_field');
    $filter          = ($form_field->getAttribute('filter') instanceof Filters ? $form_field->getAttribute('filter') : '');
    $max_lenght      = $form_field->getAttribute('max_lenght');
    $name_form_field = $form_field->getAttribute('name_form_field');
    $type_html       = $form_field->getAttribute('type_html');
    $function_js     = $form_field->getAttribute('function_js');
    $value_default   = $form_field->getAttribute('value_default');
    $class_css       = $form_field->getAttribute('class_css');
}
require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_action" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="max_lenght" id="max_lenght" value="<?php echo $max_lenght; ?>" />
	<input type="hidden" name="history_log" id="history_log" value="" />
	
	<label for="id"><?php echo $i18n->_('Identificação do Campo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="5"<?php echo ($id_form_filed != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_form_filed; ?>" />
	
	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Campo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="30" value="<?php echo $name_form_field; ?>" />
	
	<br/><br/>
	<label for="type_html"><?php echo $i18n->_('Tipo HTML'); ?></label>*<br/>
	<select class="text required w30" name="type_html" id="type_html">
		<option value=""><?php echo $i18n->_('Escolha o tipo de html'); ?></option>
		<?php
		if ($TYPES_HTML && count($TYPES_HTML) > 0) {
    		foreach ($TYPES_HTML as $key=>$type) {
    			echo '<option value="' . $type . '"' . ($type_html == $type ? ' selected="selected"' : '') . '>' . $type . '</option>';
    		}
		}
		?>
	</select>
	
	<div id="container_filter">
		<br/><br/>
    	<label for="type_value"><?php echo $i18n->_('Tipo de valores usados'); ?></label>*<br/>
    	<label><input class="radio" type="radio" name="type_value" value="fi"<?php echo($filter instanceof Filters ? ' checked="checked"' : ''); ?> /> <?php echo $i18n->_('Valores de um filtro'); ?></label>
    	<label><input class="radio" type="radio" name="type_value" value="va"<?php echo(!empty($value_default) ? ' checked="checked"' : ''); ?> /> <?php echo $i18n->_('Definir valores manualmente'); ?></label>
		<div class="types" id="fi"<?php echo($filter instanceof Filters ? '' : ' style="display:none;"'); ?>>
        	<br/><br/>
        	<div class="pRelative">
            	<label for="filter_ok"><?php echo $i18n->_('Filtro Associado'); ?></label>*<br/>
            	<input type="text" class="text w100" name="filter_ok" id="filter_ok" autocomplete="off" value="<?php echo ($filter instanceof Filters ? $filter->getAttribute('name_filter') : ''); ?>" />
            	<div id="listener-filter-parent" class="w100"></div>
            	<input type="hidden" name="filter" id="filter" value="<?php echo ($filter instanceof Filters ? $filter->getAttribute('id_filter') : ''); ?>" />
        	</div>
    	</div>
    	
    	<div class="types" id="va"<?php echo(!empty($value_default) ? '' : ' style="display:none;"'); ?>>
        	<br/><br/>
        	<label for="value_default"><?php echo $i18n->_('Valores Padrão'); ?></label>* ( <?php echo $i18n->_('valor,nome separados por |'); ?> )<br/>
        	<input class="text big w100" type="text" name="value_default" id="value_default" value="<?php echo $value_default; ?>" />
    	</div>
	</div>
	
	<br/><br/>
	<label for="functions"><?php echo $i18n->_('Função externa'); ?></label><br/>
	<input class="text big w100" type="text" name="functions" id="functions" value="<?php echo $function_js; ?>" />
	
	<br/><br/>
	<label for="class_css"><?php echo $i18n->_('Classe de CSS'); ?></label> (<?php echo $i18n->_('separe-as por |'); ?>)<br/>
	<input class="text big w100" type="text" name="class_css" id="class_css" value="<?php echo $class_css; ?>" />

	<hr/>
	
	<p class="aright">
		<?php
		if ($form_field->exists() && $RULE['edt']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		if ($RULE['new']) {
    		    echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
    		}
		} else if ($RULE['new']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
		}
		echo '<a class="button close">' . $i18n->_('Voltar') . '</a> ';
		if ($RULE['new']) {
		    echo '<a class="button new">' . $i18n->_('Criar Novo') . '</a> ';
		}
		if ($RULE['del']) {
		    echo '<a class="button remove">' . $i18n->_('Remover') . '</a>';
		}
		?>
	
</form>

<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>
<script type="text/javascript">

function setTypeHtml(val) {
	switch (val) {
	case 'input[checkbox]':
	case    'input[radio]':
	case          'select':
		$('#container_filter').fadeIn();
		$('#container_filter select, #container_filter input').addClass('required');
		break;
	default:
		$('#container_filter').fadeOut();
    	$('#container_filter select, #container_filter input').removeClass('required');
    	break;
	}
}

$(document).ready(function(){
	$('#container_filter').hide();
	var act = '<?php echo $type_html; ?>';
	setTypeHtml(act);

	$('#type_html').change(function(){
		setTypeHtml($(this).val());
	});

	$('input[type="radio"][name="type_value"]').change(function(){
		$('#container_filter div.types').fadeOut();
		$('#'+$(this).val()).fadeIn();
	});

	//função que lista os nomes
	$('input[name="filter_ok"]').keyup(function(){
		$('#listener-filter-parent').hide();
		$('input[type="hidden"][name="filter"]').val("");
		var valor = $(this).val();
		if (valor.length > 3) {
			var results = '';
			$.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'name=' + valor + '&parent=' + true + '&action=listFiltersByName',
                dataType: 'json',
                async: true,
                success: function(data) {
					if(data.status == '1'){
						$.each(data.filters, function(i, obj){
							var alter='';
							if (i%2==0) {alter = ' class="alt"';} else {alter = '';}
							results += '<a href="' + obj.id + '"' + alter + '>' + obj.name + '</a>';
						});
						$('#listener-filter-parent').append(results);
						$('#listener-filter-parent').show();
					}
                }
            });
			if (results == '') { $('#listener-filter-parent').html(''); }
		}
	});

	$('input[name="filter_ok"]').change(function(){
		if ($(this).val() == "") {
			$('input[type="hidden"][name="filter"]').val("");
		}
	});

	$('#listener-filter-parent a').live('click',function(e){
		e.preventDefault();
		$('input[name="filter_ok"]').val( $(this).text() );
		$('input[type="hidden"][name="filter"]').val( $(this).attr('href') );
		$('#listener-filter-parent').hide();
	});
});
</script>

<?php require 'bot.inc.php'; ?>