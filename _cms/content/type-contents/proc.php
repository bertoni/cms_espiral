<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_type_content = (isset($_POST['id']) ? $_POST['id'] : '');
$type_content    = new TypeContents($id_type_content);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (TypeContents::removeTypeContent($type_content)) {
        header('Location: index.php?ok=' . $i18n->_('Tipo de Conteúdo removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Tipo de Conteúdo') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $type_content->setAttribute('name_type_content', trim($_POST['name']));
    $type_content->setAttribute('status', new Status(trim($_POST['status'])));
    $type_content->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $type_content->save();
    $id_redirect = urlencode($type_content->getAttribute('id_action'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>