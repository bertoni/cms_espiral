<?php
/**
 * Formulário de edição
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

$module_code = 'conf';
$page_code   = 'acti';

$contenModule = new ContentsModule($page_code);
if (!$contenModule->exists() || $contenModule->getAttribute('status')->getAttribute('id_status') != 'a') {
    header('Location: /_cms/');
    exit;
}
require_once 'actions_rules.inc.php';
if (!$RULE['lis']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão de ver este conteúdo') . '.');
    exit;
}

$id_action     = '';
$action_parent = '';
$name_action   = '';
$description   = '';
$log           = '';

$action = new Actions(isset($_GET['id']) ? $_GET['id'] : '');
if ($action->exists()) {
    $id_action     = $action->getAttribute('id_action');
    $action_parent = $action->getAttribute('action_parent');
    $name_action   = $action->getAttribute('name_action');
    $description   = $action->getAttribute('description');
    $log           = trim($action->getAttribute('log'));
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_action" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação da Ação'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="3"<?php echo ($id_action != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_action; ?>" />

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Ação'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="25" value="<?php echo $name_action; ?>" />

	<br/><br/>
	<label for="action_parent"><?php echo $i18n->_('Filha de'); ?></label><br/>
	<select class="text w30"name="action_parent" id="action_parent">
		<option value=""><?php echo $i18n->_('Escolha a Ação'); ?></option>
		<?php
		$arr_actions = Actions::getActions('', '');
		if ($arr_actions && count($arr_actions) > 0) {
    		foreach ($arr_actions as $key=>$obj_action) {
    		    if ($obj_action->getAttribute('id_action') != $id_action) {
    			    echo '<option value=" ' . $obj_action->getAttribute('id_action') . '"' . ($action_parent instanceof Actions && $action_parent->getAttribute('id_action') == $obj_action->getAttribute('id_action') ? ' selected="selected"' : '') . '>' . $obj_action->getAttribute('name_action') . '</option>';
    		    }
    		}
		}
		?>
	</select>

	<br/><br/>
	<label for="description"><?php echo $i18n->_('Descrição'); ?></label><br/>
	<textarea class="text w100 required" name="description" id="description" rows="5"><?php echo $description; ?></textarea>

	<hr/>

	<p class="aright">
		<?php
		if ($action->exists() && $RULE['edt']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		if ($RULE['new']) {
    		    echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
    		}
		} else if ($RULE['new']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
		}
		echo '<a class="button close">' . $i18n->_('Voltar') . '</a> ';
		if ($RULE['new']) {
		    echo '<a class="button new">' . $i18n->_('Criar Novo') . '</a> ';
		}
		if ($RULE['del']) {
		    echo '<a class="button remove">' . $i18n->_('Remover') . '</a>';
		}
		?>
	</p>

	<?php
	if ($log != '' && $RULE['log']) {
		echo '<div id="log">' . nl2br($log) . '</div>';
	}
	?>

</form>

<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>

<?php require 'bot.inc.php'; ?>