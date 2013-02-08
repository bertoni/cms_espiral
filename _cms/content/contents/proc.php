<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!isset($_POST['check']) || empty($_POST['check'])) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

//echo '<pre>';print_r($_POST);exit;
$id_content      = (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$id_type_content = (isset($_POST['type']) ? $_POST['type'] : '');
$type_content    = new TypeContents($id_type_content);
$content         = new Contents($id_content);

if ($type_content->exists()) {
    
    if (isset($_POST['action']) && $_POST['action'] == 'remove') {
        if (Contents::removeContent($content)) {
            header('Location: index.php?ok=' . $i18n->_('Conteúdo removido com sucesso') . '.');
    	    exit;
        } else {
            header('Location: index.php?erro=' . $i18n->_('Não foi possível remover o Conteúdo') . '.');
    	    exit;
        }
    } else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
        
        if (count($type_content->getAttribute('config_form')) > 0) {
    	    foreach ($type_content->getAttribute('config_form') as $configForm) {
    	        $required   = $configForm->getAttribute('required');
    	        $field_name = $configForm->getAttribute('name');
    	        if (isset($_POST[$field_name])) {
    	            if ($configForm->getAttribute('form')->getAttribute('type_html') == 'input[checkbox]') {
    	                $values_final = '';
    	                foreach ($_POST[$field_name] as $key=>$value) {
    	                    $values_final .= ($key ? '||' : '') . $value;
    	                }
    	                $content->setExtraContent($field_name, $values_final);
    	            } else {
                        $content->setExtraContent($field_name, $_POST[$field_name]);
    	            }
                } else {
                    if ($required) {
                        header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
        	            exit;
                    }
                }
    	    }
        }
        
        $date_publication = explode(' - ', $_POST['date_publication']);
        $date             = explode('/', $date_publication[0]);
        $time             = explode(':', $date_publication[1]);
        $date_publication = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
        
        $date_expiration = explode(' - ', $_POST['date_expiration']);
        $date            = explode('/', $date_expiration[0]);
        $time            = explode(':', $date_expiration[1]);
        $date_expiration = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
        
        $content->setAttribute('type_content', $type_content);
        $content->setAttribute('date_publication', $date_publication);
        $content->setAttribute('date_expiration', $date_expiration);
        $content->setAttribute('title', $_POST['title']);
        $content->setAttribute('status', new Status(trim($_POST['status'])));
        $content->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
        
        $ret = $content->save();
        if ($ret) {
            if ($_POST['action'] == 'save') {
                header('Location: form.php?cont=' . $_POST['type'] . '&id=' . urlencode($content->getAttribute('id_content')) . '&ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
    	        exit;
            }
            if ($_POST['action'] == 'savecreate') {
                header('Location: form.php?cont=' . $_POST['type'] . '&ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
    	        exit;
            }
            if ($_POST['action'] == 'saveback') {
                header('Location: index.php?cont=' . $_POST['type'] . '&ok=' . $i18n->_('Dados criados/alterados com sucesso') . '.');
    	        exit;
            }
        } else {
            header('Location: form.php?cont=' . $_POST['type'] . '&id=' . urlencode($id_content) . '&alert=' . $i18n->_('Não foi possível criar/alterar os dados') . '.');
    	    exit;
        }
    } else {
        header('Location: index.php?cont=' . $_POST['type'] . '&alert=' . $i18n->_('Erro na validação'));
    	exit;
    }
} else {
    header('Location: index.php?cont=' . $_POST['type'] . '&alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>