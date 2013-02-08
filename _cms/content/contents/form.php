<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = $_GET['cont'];

$typeContent = new TypeContents($page_code);
if (!$typeContent->exists() || $typeContent->getAttribute('status')->getAttribute('id_status') != 'a') {
    header('Location: /_cms/');
    exit;
}

require_once 'actions_rules_content.inc.php';
if (!$RULE['lis']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão de ver este conteúdo') . '.');
    exit;
}

$id_content       = '';
$title            = '';
$status           = '';
$date_publication = strftime('%d/%m/%Y - %H:%M', mktime());
$date_expiration  = strftime('%d/%m/%Y - %H:%M', mktime(23, 59, 59, 12, 31, 2035));
$log              = '';

$content = new Contents(isset($_GET['id']) ? (int)$_GET['id'] : '');
if ($content->exists()) {
    $id_content       = $content->getAttribute('id_content');
    $title            = $content->getAttribute('title');
    $status           = $content->getAttribute('status')->getAttribute('id_status');
    $date_publication = strftime('%d/%m/%Y - %H:%M', $content->getAttribute('date_publication'));
    $date_expiration  = strftime('%d/%m/%Y - %H:%M', $content->getAttribute('date_expiration'));
    $log              = trim($content->getAttribute('log'));
}

$CSS = array('/css/datepicker.css');
$JS  = array('/js/libs/jquery_ui.js', '/js/libs/datepicker.js', '/js/ckeditor/ckeditor.js');
require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $typeContent->getAttribute('name_type_content'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" method="post">

	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="id" id="id" value="<?php echo $id_content; ?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $typeContent->getAttribute('id_type_content'); ?>" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<br/><br/>
	<label for="title"><?php echo $i18n->_('Título'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="title" id="title" value="<?php echo $title; ?>" />

	<?php
	if (count($typeContent->getAttribute('config_form')) > 0) {
	    foreach ($typeContent->getAttribute('config_form') as $configForm) {
            $extra = ExtraContents::getExtraContents($content, $configForm->getAttribute('name'));
            $value = ($extra ? $extra[0]->getAttribute('value') : '');
	        echo FieldHtmlGenerator($configForm, $value);
	    }
	}
	?>

	<br/><br/>
	<label for="status"><?php echo $i18n->_('Status do Conteúdo'); ?></label>*<br/>
	<?php
	$arr_status = Status::getStatus();
	if ($arr_status && count($arr_status) > 0) {
	    foreach ($arr_status as $key=>$obj_status) {
	        echo '<input class="radio required" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($status == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') .' /><label class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
	    }
	}
	?>

	<br/><br/>
	<label for="date_publication"><?php echo $i18n->_('Data de Publicação'); ?></label><br/>
	<input class="text dateTime required big w30" type="text" name="date_publication" id="date_publication" value="<?php echo $date_publication; ?>" />

	<br/><br/>
	<label for="date_expiration"><?php echo $i18n->_('Data de Expiração'); ?></label><br/>
	<input class="text dateTime required big w30" type="text" name="date_expiration" id="date_expiration" value="<?php echo $date_expiration; ?>" />

	<hr/>
	
	<p class="aright">
		<?php
		if ($content->exists() && $RULE['edt']) {
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
    $.timepicker.setDefaults($.timepicker.regional['pt_BR']);
    $('.dateTime').datetimepicker({
    	separator: ' - ',
    	dateFormat: 'dd/mm/yy'
    });
});
</script>
<?php require 'bot.inc.php'; ?>