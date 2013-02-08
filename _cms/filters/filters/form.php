<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'filte';
$page_code   = 'filte';

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

$id_filter   = '';
$status      = '';
$name_filter = '';
$log         = '';

$filter = new Filters(isset($_GET['id']) ? $_GET['id'] : '');
if ($filter->exists()) {
    $id_filter   = $filter->getAttribute('id_filter');
    $status      = $filter->getAttribute('status')->getAttribute('id_status');
    $name_filter = $filter->getAttribute('name_filter');
    $log         = trim($filter->getAttribute('log'));
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_module" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação do Filtro'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="10"<?php echo ($id_filter != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_filter; ?>" />

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Filtro'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="25" value="<?php echo $name_filter; ?>" />

	<br/><br/>
	<div class="pRelative">
    	<label for="filter_parent_ok"><?php echo $i18n->_('Filtro Associado'); ?></label><br/>
    	<input type="text" class="text required w30" name="filter_parent_ok" id="filter_parent_ok" autocomplete="off" value="<?php echo ($filter->getAttribute('filter_parent') instanceof Filters ? $filter->getAttribute('filter_parent')->getAttribute('name_filter') : ''); ?>" />
    	<div id="listener-filter-parent" class="w31"></div>
    	<input type="hidden" name="filter_parent" value="<?php echo ($filter->getAttribute('filter_parent') instanceof Filters ? $filter->getAttribute('filter_parent')->getAttribute('id_filter') : ''); ?>" />
	</div>

	<br/><br/>
	<label for="status"><?php echo $i18n->_('Status do Filtro'); ?></label>*<br/>
	<?php
	$arr_status = Status::getStatus();
	if ($arr_status && count($arr_status) > 0) {
	    foreach ($arr_status as $key=>$obj_status) {
	        echo '<input id="status_'.$obj_status->getAttribute('id_status').'" class="radio" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($status == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') .' /> <label for="status_'.$obj_status->getAttribute('id_status').'" class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
	    }
	}
	?>

	<hr/>
	
	<p class="aright">
		<?php
		if ($filter->exists() && $RULE['edt']) {
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
	</p>

	<?php
	if ($log != '' && $RULE['log']) {
		echo '<div id="log">' . nl2br($log) . '</div>';
	}
	?>

</form>

<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	//função que lista os nomes
	$('input[name="filter_parent_ok"]').keyup(function(){
		$('#listener-filter-parent').hide();
		$('input[type="hidden"][name="filter_parent"]').val("");
		var valor = $(this).val();
		if (valor.length > 2) {
			var results = '';
			$.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'name=' + valor + '&action=listFiltersByName',
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

	$('#listener-filter-parent a').live('click',function(e){
		e.preventDefault();
		$('input[name="filter_parent_ok"]').val( $(this).text() );
		$('input[type="hidden"][name="filter_parent"]').val( $(this).attr('href') );
		$('#listener-filter-parent').hide();
	});

});
</script>

<?php require 'bot.inc.php'; ?>