<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_form_field = (isset($_POST['id']) ? $_POST['id'] : '');
$form_field    = new FormFields($id_form_field);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (FormFields::removeFormFiled($form_field)) {
        header('Location: index.php?ok=' . $i18n->_('Campo removido com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Campo') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $filter = new Filters(trim($_POST['filter']));
    if (!$filter->exists() || !empty($_POST['value_default'])) {
        $filter = '';
    }
    
    $form_field->setAttribute('filter', $filter);
    $form_field->setAttribute('name_form_field', $_POST['name']);
    $form_field->setAttribute('type_html', $_POST['type_html']);
    $form_field->setAttribute('function_js', $_POST['functions']);
    $form_field->setAttribute('value_default', $_POST['value_default']);
    $form_field->setAttribute('class_css', $_POST['class_css']);
    $ret         = $form_field->save();
    $id_redirect = urlencode($form_field->getAttribute('id_form_field'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>