<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_profile = (isset($_POST['id']) ? $_POST['id'] : '');
$profile    = new Profiles($id_profile);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (Profiles::removeProfile($profile)) {
        header('Location: index.php?ok=' . $i18n->_('Perfil removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Perfil') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    
    $parent = new Profiles($_POST['profile']);
    if (!$parent->exists()) {
        $parent = '';
    }
    
    $profile->setAttribute('profile_parent', $parent);
    $profile->setAttribute('profile', $_POST['name']);
    $profile->setAttribute('status', new Status($_POST['status']));
    $ret         = $profile->save();
    $id_redirect = urlencode($profile->getAttribute('id_profile'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>