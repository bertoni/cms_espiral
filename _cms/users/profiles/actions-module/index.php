<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'users';
$page_code   = 'ru_pe';

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
    $profile = new Profiles($_GET['id']);
    if (!$profile->exists()) {
        header('Location: /_cms/');
        exit;
    }
}
$JS = array('/js/lib_rules.js');
require 'top.inc.php';
?>

<a class="button float-right" href="../"><?php echo $i18n->_('Voltar para a listagem'); ?></a>
<h1><a href="../"><?php echo $contenModule->getAttribute('name_content_module') ?></a> &raquo; <?php echo $profile->getAttribute('profile'); ?></h1>
<hr/>

<div class="float-right" style="width:300px">
    <p><strong><?php echo $i18n->_('Como funciona o esquema de regras?'); ?></strong></p>
    <?php echo $i18n->_('regras-actions-module'); ?>
</div>

<div id="man_actions" class="float-left" style="width:630px">
<?php
function montaHtml(Profiles $profile, ContentsModule $ct, Actions $action, $margin, $level = array(), $enabled) {
    $arr_act = Actions::getActions('', '', 'name_action', '', $action);

    $id_action   = $action->getAttribute('id_action');
    $name_action = $action->getAttribute('name_action');
    $id_c_module = $ct->getAttribute('id_content_module');

    $span = $endspan = '';
    for ($i=1; $i<count($level); $i++) {
        $span    .= '<span class="d' . $level[$i] . $id_c_module . '" style="display: none;">';
        $endspan .= '</span>';
    }
    $checked = Rules::checkRuleProfile($action, $profile, $ct);

    if ($arr_act) {
        echo '<p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_c_module . '"' : '') . '>' . $span . $name_action . ' <input type="checkbox" class="' . $id_action . $id_c_module . '" value="' . $id_action . '|' . $id_c_module . '"' . ($checked ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' /> <a href="d' . $id_action . $id_c_module . '" class="open_more">' . $i18n->_('mais') . '</a>' . $endspan . '</p>';
        $level[] = $id_action;
        foreach ($arr_act as $action2) {
            montaHtml($profile, $ct, $action2, (40 * (count($level)+1)), $level, $enabled);
        }
    } else {
        echo '<p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_c_module . '"' : '') . '>' . $span . $name_action . ' <input type="checkbox" class="' . $id_action . $id_c_module . '" value="' . $id_action . '|' . $id_c_module . '"' . ($checked ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $endspan . '</p>';
    }
}


$act     = Actions::getActions('0', '1', 'fk_id_action_parent = ""');
$ret_mod = Modules::getModules('', '', 'name_module');
if ($ret_mod) {
    foreach ($ret_mod as $module) {
        echo '<div>';
        echo '<p style="font-size:1.3em;font-weight:bold;padding:5px 10px;margin:10px 0 0 0;">' . $i18n->_('Módulo') . ': ' . $module->getAttribute('name_module') . '<a style="position:relative;left:6px;top:1px" href="#" class="show_all">mais</a></p>';
        echo '<div class="show-all" style="display:none">';
        $ret_c_mod = ContentsModule::getContentsModules('', '', 'name_content_module', '', $module);
        if ($ret_c_mod) {
            foreach ($ret_c_mod as $content_module) {
                echo '<p style="padding-left: 40px; margin:0;font-weight: bold;">' . $content_module->getAttribute('name_content_module') . '</p>';
                if ($act) {
                    foreach ($act as $action) {
                        montaHtml($profile, $content_module, $action, 60, array(), $RULE['edt']);
                    }
                }
            }
        }
        echo '</div>';
        echo '</div>';
    }
}
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#man_actions input[type="checkbox"]').change(function(){
		var val  = $(this).is(':checked');
		var cla  = $(this).attr('class');
		var ref  = $(this).val().split('|');
		var prof = '<?php echo $profile->getAttribute('id_profile'); ?>';

		setRuleProfile(ref[0], prof, (val ? 1 : 0), ref[1], '');

		if (val && $(this).parent().find('a.open_more').hasClass('active') == false) {
			openClose($(this).parent().find('a.open_more'));
		} else if ($(this).parent().find('a.open_more').hasClass('active') != false) {
			openClose($(this).parent().find('a.open_more'));
		}

		setBackProfileMo($(this), prof);

		setFrontProfileMo($('#man_actions .d'+cla), prof, val);
	});
});
</script>
<?php require 'bot.inc.php'; ?>