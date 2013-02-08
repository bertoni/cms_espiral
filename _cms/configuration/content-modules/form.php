<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'conf';
$page_code   = 'tmod';

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

$id_content_module   = '';
$module_parent       = '';
$status              = '';
$name_content_module = '';
$url                 = '';
$log                 = '';
$visible             = '';

$content_module = new ContentsModule(isset($_GET['id']) ? $_GET['id'] : '');
if ($content_module->exists()) {
    $id_content_module   = $content_module->getAttribute('id_content_module');
    $module_parent       = $content_module->getAttribute('module')->getAttribute('id_module');
    $status              = $content_module->getAttribute('status')->getAttribute('id_status');
    $name_content_module = $content_module->getAttribute('name_content_module');
    $url                 = $content_module->getAttribute('url');
    $log                 = trim($content_module->getAttribute('log'));
    $visible             = $content_module->getAttribute('visible');
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_module" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação do Conteúdo de Módulo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="5"<?php echo ($id_content_module != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_content_module; ?>" />

	<br/><br/>
	<label for="module"><?php echo $i18n->_('Módulo'); ?></label><br/>
	<select class="text required w30"name="module" id="module">
		<?php
		$arr_modules = Modules::getModules('', '', 'name_module');
		if ($arr_modules && count($arr_modules) > 0) {
    		foreach ($arr_modules as $key=>$obj_module) {
    			echo '<option value="' . $obj_module->getAttribute('id_module') . '"' . ($module_parent == $obj_module->getAttribute('id_module') ? ' selected="selected"' : '') . '>' . $obj_module->getAttribute('name_module') . '</option>';
    		}
		}
		?>
	</select>

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Conteúdo de Módulo'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="50" value="<?php echo $name_content_module; ?>" />

	<br/><br/>
	<label for="url"><?php echo $i18n->_('Caminho Pasta'); ?></label><br/>
	<input class="text required big w100" type="text" name="url" id="url" maxlength="150" value="<?php echo $url; ?>" />

	<br/><br/>
	<span><?php echo $i18n->_('Visibilidade do Conteúdo de Módulo'); ?></span>*<br/>
	<input class="radio" id="visible_on"  type="radio" name="visible" value="1"<?php echo($visible === 1 ? ' checked="checked"' : ''); ?> /> <label for="visible_on" class="radio_label"><?php echo $i18n->_('Vísivel no Menu'); ?></label>
	<input class="radio" id="visible_off" type="radio" name="visible" value="0"<?php echo($visible === 0 ? ' checked="checked"' : ''); ?> /> <label for="visible_off" class="radio_label"><?php echo $i18n->_('Invísivel no Menu'); ?></label>

	<br/><br/>
	<span><?php echo $i18n->_('Status do Conteúdo de Módulo'); ?></span>*<br/>
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
		if ($content_module->exists() && $RULE['edt']) {
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
