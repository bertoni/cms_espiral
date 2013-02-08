<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'conf';
$page_code   = 'mod';

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

$id_module   = '';
$status      = '';
$name_module = '';
$url         = '';
$log         = '';

$module = new Modules(isset($_GET['id']) ? $_GET['id'] : '');
if ($module->exists()) {
    $id_module   = $module->getAttribute('id_module');
    $status      = $module->getAttribute('status')->getAttribute('id_status');
    $name_module = $module->getAttribute('name_module');
    $url         = $module->getAttribute('url');
    $log         = trim($module->getAttribute('log'));
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_module" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação do Módulo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="5"<?php echo ($id_module != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_module; ?>" />

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome do Módulo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="50" value="<?php echo $name_module; ?>" />

	<br/><br/>
	<label for="url"><?php echo $i18n->_('Caminho Pasta'); ?></label><br/>
	<input class="text required big w100" type="text" name="url" id="url" maxlength="150" value="<?php echo $url; ?>" />

	<br/><br/>
	<label for="status"><?php echo $i18n->_('Status do Módulo'); ?></label>*<br/>
	<?php
	$arr_status = Status::getStatus();
	if ($arr_status && count($arr_status) > 0) {
	    foreach ($arr_status as $key=>$obj_status) {
	        echo '<input class="radio" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($status == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') .' /> <label class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
	    }
	}
	?>

	<hr/>
	
	<p class="aright">
		<?php
		if ($module->exists() && $RULE['edt']) {
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
