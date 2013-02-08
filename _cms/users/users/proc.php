<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_user = (isset($_POST['id']) ? $_POST['id'] : '');
$user    = new Users($id_user);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (Users::removeUser($user)) {
        header('Location: index.php?ok=' . $i18n->_('Usuário removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Usuário') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $user->setAttribute('profile', new Profiles($_POST['profile']));
    $user->setAttribute('status', new Status($_POST['status']));
    $user->setAttribute('name', $_POST['name']);
    $user->setAttribute('email', $_POST['email']);
    $user->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $user->save();
    $id_redirect = urlencode($user->getAttribute('id_user'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>