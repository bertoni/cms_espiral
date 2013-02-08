<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = 't_con';

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

$id_type_content   = '';
$status            = '';
$name_type_content = '';
$log               = '';

$type_content = new TypeContents(isset($_GET['id']) ? $_GET['id'] : '');
if ($type_content->exists()) {
    $id_type_content   = $type_content->getAttribute('id_type_content');
    $status            = $type_content->getAttribute('status')->getAttribute('id_status');
    $name_type_content = $type_content->getAttribute('name_type_content');
    $log               = trim($type_content->getAttribute('log'));
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_module" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação do Tipo de Conteúdo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="10"<?php echo ($id_type_content != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_type_content; ?>" />

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Tipo de Conteúdo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="50" value="<?php echo $name_type_content; ?>" />

	<br/><br/>
	<span><?php echo $i18n->_('Status do Tipo de Conteúdo'); ?></span>*<br/>
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
		if ($type_content->exists() && $RULE['edt']) {
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

<?php require 'bot.inc.php'; ?>
