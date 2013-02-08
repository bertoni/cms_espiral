<?php
/**
 * Processamento do formulário
 *
 * PHP Version 5.3
 *
 * @category Page
 * @package  CMS
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

if (!(isset($_POST['check']) && empty($_POST['check']) == false)) {
	header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}

$id_action = (isset($_POST['id']) ? $_POST['id'] : '');
$action    = new Actions($id_action);

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    if (Actions::removeAction($action)) {
        header('Location: index.php?ok=' . $i18n->_('Ação removida com sucesso') . '.');
	    exit;
    } else {
        header('Location: index.php?erro=' . $i18n->_('Não foi possível remover a Ação') . '.');
	    exit;
    }
} else if ($_POST['action'] == 'save' || $_POST['action'] == 'saveback' || $_POST['action'] == 'savecreate') {
    $action_parent = new Actions(trim($_POST['action_parent']));
    if ($action_parent->exists()) {
        $action->setAttribute('action_parent', $action_parent);
    }
    $action->setAttribute('name_action', $_POST['name']);
    $action->setAttribute('description', $_POST['description']);
    $action->setAttribute('log', formatLog($USER_LOGGED, $_POST['history_log']));
    $ret         = $action->save();
    $id_redirect = urlencode($action->getAttribute('id_action'));
    require_once 'actions_proc.inc.php';
} else {
    header('Location: index.php?alert=' . $i18n->_('Erro na validação'));
	exit;
}
?>