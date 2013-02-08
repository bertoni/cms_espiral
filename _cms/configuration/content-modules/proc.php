<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_content_module = (isset($_POST['id']) ? $_POST['id'] : '');
$content_module    = new ContentsModule($id_content_module);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (ContentsModule::removeContentModule($content_module)) {
        header('Location: index.php?ok=' . $i18n->_('Conteúdo de Módulo removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Conteúdo de Módulo') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $content_module->setAttribute('name_content_module', trim($_POST['name']));
    $content_module->setAttribute('module', new Modules(trim($_POST['module'])));
    $content_module->setAttribute('url', trim($_POST['url']));
    $content_module->setAttribute('visible', trim($_POST['visible']));
    $content_module->setAttribute('status', new Status(trim($_POST['status'])));
    $content_module->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $content_module->save();
    $id_redirect = urlencode($content_module->getAttribute('id_content_module'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>