<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_type_content = (isset($_POST['id_type_content']) ? $_POST['id_type_content'] : '');
$type_content    = new TypeContents($id_type_content);

if (isset($_POST['action']) && $type_content->exists()) {

    //echo '<pre>';print_r($_POST);
    ConfigForm::removeConfigForm($type_content);
    $type_content->cleanConfigForms();

    $ret = array();
    foreach ($_POST['form_field'] as $key=>$post) {
        $form = explode('|', $_POST['form_field'][$key]);
        $form = new FormFields($form[0]);
        if ($form->exists()) {
            $config_form = new ConfigForm($form, $_POST['name'][$key], $_POST['label'][$key], $_POST['max_lenght'][$key], $_POST['required'][$key], $_POST['order'][$key], $_POST['use_filter'][$key]);
            if ($config_form->exists()) {
                $ret[] = $config_form->save($type_content);
            }
        }
    }

    if (!array_search(0, $ret)) {
        if ($_POST['action'] == 'save') {
            header('Location: index.php?id=' . urlencode($type_content->getAttribute('id_type_content')) . '&ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
	        exit;
        }
        if ($_POST['action'] == 'saveback') {
            header('Location: ../?ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
            exit;
        }
    } else {
        header('Location: index.php?alert=' . $i18n->_('Não foi possível alterar a definição de campo') . '.');
	    exit;
    }
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>