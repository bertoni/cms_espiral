<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_module = (isset($_POST['id']) ? $_POST['id'] : '');
$module    = new Modules($id_module);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (Modules::removeModule($module)) {
        header('Location: index.php?ok=' . $i18n->_('Módulo removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Módulo') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $module->setAttribute('name_module', trim($_POST['name']));
    $module->setAttribute('url', trim($_POST['url']));
    $module->setAttribute('status', new Status(trim($_POST['status'])));
    $module->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $module->save();
    $id_redirect = urlencode($module->getAttribute('id_module'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>