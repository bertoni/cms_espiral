<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'users';
$page_code   = 'r_u_c';

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
if (!isset($_GET['id'])) {
    header('Location: /_cms/');
    exit;
} else {
    $user = new Users($_GET['id']);
    if (!$user->exists()) {
        header('Location: /_cms/');
        exit;
    }
}

$arr_profiles = listPermissionsProfiles($USER_LOGGED->getAttribute('profile'));
if (!$arr_profiles[$user->getAttribute('profile')->getAttribute('id_profile')]['edit']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão para editar este usuário') . '.');
    exit;
}

$JS = array('/js/lib_rules.js');
require 'top.inc.php';
?>

<h1><?php echo $contenModule->getAttribute('name_content_module') . ' - ' . $user->getAttribute('name'); ?></h1>
<hr />

<div class="float-right" style="width:300px">
    <p><strong><?php echo $i18n->_('Como funciona o esquema de regras?'); ?></strong></p>
    <?php echo $i18n->_('regras-actions-content_user'); ?>
</div>

<div id="man_actions" class="float-left" style="width:630px">
<?php
function montaHtml(Users $user, TypeContents $tp, Actions $action, $margin, $level = array(), $enabled) {
    $arr_act = Actions::getActions('', '', 'name_action', '', $action);

    $id_action   = $action->getAttribute('id_action');
    $name_action = $action->getAttribute('name_action');
    $id_c_module = $tp->getAttribute('id_type_content');
    
    $span = $endspan = '';
    for ($i=1; $i<count($level); $i++) {
        $span    .= '<span class="d' . $level[$i] . $id_c_module . '" style="display: none;">';
        $endspan .= '</span>';
    }
    $checked = Rules::checkRuleUser($action, $user, '', $tp);
    if ($arr_act) {
        echo '
        <p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_c_module . '"' : '') . '>' . 
        $span . $name_action . 
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="1|' . $id_action . '|' . $id_c_module . '"' . ($checked == '1' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Sim') . ' &nbsp;&nbsp;' . 
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="0|' . $id_action . '|' . $id_c_module . '"' . ($checked == '0' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Não') . ' &nbsp;&nbsp;' .
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="|' . $id_action . '|' . $id_c_module . '"' . ($checked == '' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Herdado') . ' &nbsp;' .
        '<a href="d' . $id_action . $id_c_module . '" class="open_more">' . $i18n->_('mais') . '</a>' . $endspan . '</p>';
        $level[] = $id_action;
        foreach ($arr_act as $action2) {
            $check = TypeContents::checkActionByTypeContent($tp, $action2);
            if ($check) {
                montaHtml($user, $tp, $action2, (40 * (count($level)+1)), $level, $enabled);
            }
        }
    } else {
        echo '
        <p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_c_module . '"' : '') . '>' . 
        $span . $name_action . 
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="1|' . $id_action . '|' . $id_c_module . '"' . ($checked == '1' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Sim') . ' &nbsp;&nbsp;' . 
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="0|' . $id_action . '|' . $id_c_module . '"' . ($checked == '0' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Não') . ' &nbsp;&nbsp;' .
        ' <input type="radio" name="' . $id_action . $id_c_module . '" class="' . $id_action . $id_c_module . '" value="|' . $id_action . '|' . $id_c_module . '"' . ($checked == '' ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $i18n->_('Herdado') . ' ' .
        $endspan . '</p>';
    }
}


$act      = Actions::getActions('0', '1', 'fk_id_action_parent = ""');
$ret_type = TypeContents::getTypeContents('', '', 'name_type_content');
if ($ret_type) {
    foreach ($ret_type as $type_content) {
        echo '<p style="font-size: 1.3em;font-weight: bold; margin-top: 20px;">' . $i18n->_('Conteúdo') . ': ' . $type_content->getAttribute('name_type_content') . '</p>';
        if ($act) {
            foreach ($act as $action) {
                $check = TypeContents::checkActionByTypeContent($type_content, $action);
                if ($check) {
                    montaHtml($user, $type_content, $action, 40, array(), $RULE['edt']);
                }
            }
        }
    }
}
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#man_actions input[type="radio"]').change(function(){
		var cla  = $(this).attr('class');
		var ref  = $(this).val().split('|');
		var user = '<?php echo $user->getAttribute('id_user'); ?>';

		setRuleUser(ref[1], user, ref[0], '', ref[2]);

		if ($(this).parent().find('a.open_more').hasClass('active') == false && ref[0] == 1) {
			openClose($(this).parent().find('a.open_more'));
		}
		if ($(this).parent().find('a.open_more').hasClass('active') == true && ref[0] != 1) {
			openClose($(this).parent().find('a.open_more'));
		}

		setBackUserTy($(this), user);

		setFrontUserTy($('#man_actions .d'+cla), user, ref[0]);
		
	});
});
</script>

<?php require 'bot.inc.php'; ?>