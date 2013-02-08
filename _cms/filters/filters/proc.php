<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_filter = (isset($_POST['id']) ? $_POST['id'] : '');
$filter    = new Filters($id_filter);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (Filters::removeFilter($filter)) {
        header('Location: index.php?ok=' . $i18n->_('Filtro removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Filtro') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $filter_parent = new Filters($_POST['filter_parent']);
    if (!$filter_parent->exists()) {
        $filter_parent = '';
    }
    
    $filter->setAttribute('name_filter', $_POST['name']);
    $filter->setAttribute('status', new Status($_POST['status']));
    $filter->setAttribute('filter_parent', $filter_parent);
    $filter->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $filter->save();
    $id_redirect = urlencode($filter->getAttribute('id_filter'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>