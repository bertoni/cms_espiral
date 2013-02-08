<?php
if (!isset($id_redirect)) {
    $id_redirect = '';
}
if ($ret) {
    if ($_POST['action'] == 'save') {
        header('Location: form.php?id=' . $id_redirect . '&ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
        exit;
    }
    if ($_POST['action'] == 'savecreate') {
        header('Location: form.php?ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
        exit;
    }
    if ($_POST['action'] == 'saveback') {
        header('Location: index.php?ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
        exit;
    }
} else {
    header('Location: form.php?alert=' . $i18n->_('Não foi possível criar/alterar os dados') . '.');
    exit;
}
?>