<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = 'ru_ty';

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
    $typeContent = new TypeContents($_GET['id']);
    if (!$typeContent->exists()) {
        header('Location: /_cms/');
        exit;
    }
}
$JS = array('/js/lib_rules.js');
require 'top.inc.php';
?>

<a class="button float-right" href="../"><?php echo $i18n->_('Voltar para a listagem'); ?></a>
<h1><a href="../"><?php echo $contenModule->getAttribute('name_content_module') ?></a> &raquo; <?php echo $typeContent->getAttribute('name_type_content'); ?></h1>
<hr/>

<div id="man_actions">
<?php
function montaHtml(TypeContents $typeContent, Actions $action, $margin, $level = array(), $enabled) {
    $arr_act = Actions::getActions('', '', 'name_action', '', $action);

    $id_action    = $action->getAttribute('id_action');
    $name_action  = $action->getAttribute('name_action');
    $id_t_content = $typeContent->getAttribute('id_type_content');

    $span = $endspan = '';
    for ($i=1; $i<count($level); $i++) {
        $span    .= '<span class="d' . $level[$i] . $id_t_content . '" style="display: none;">';
        $endspan .= '</span>';
    }
    $checked = TypeContents::checkActionByTypeContent($typeContent, $action);

    if ($arr_act) {
        echo '<p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_t_content . '"' : '') . '>' . $span . $name_action . ' <input type="checkbox" class="' . $id_action . $id_t_content . '" value="' . $id_action . '|' . $id_t_content . '"' . ($checked ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' /> <a href="d' . $id_action . $id_t_content . '" class="open_more">' . $i18n->_('mais') . '</a>' . $endspan . '</p>';
        $level[] = $id_action;
        foreach ($arr_act as $action2) {
            montaHtml($typeContent, $action2, (40 * (count($level)+1)), $level, $enabled);
        }
    } else {
        echo '<p style="padding-left: ' . $margin . 'px;' . (count($level) ? 'display: none;' : '') . '"' . (count($level) ? ' class="d' . $level[0] . $id_t_content . '"' : '') . '>' . $span . $name_action . ' <input type="checkbox" class="' . $id_action . $id_t_content . '" value="' . $id_action . '|' . $id_t_content . '"' . ($checked ? ' checked="checked"' : '') . ($enabled ? '' : ' disabled="disabled"') . ' />' . $endspan . '</p>';
    }
}


$act     = Actions::getActions('0', '1', 'fk_id_action_parent = ""');
echo '<div style="float:left;width:315px;">';
echo '<p style="font-weight: bold;">' . $typeContent->getAttribute('name_type_content') . '</p>';
if ($act) {
    foreach ($act as $action) {
        montaHtml($typeContent, $action, 20, array(), $RULE['edt']);
    }
}
echo '</div>';
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#man_actions input[type="checkbox"]').change(function(){
		var val  = $(this).is(':checked');
		var cla  = $(this).attr('class');
		var ref  = $(this).val().split('|');
		var type = '<?php echo $typeContent->getAttribute('id_type_content'); ?>';

		setRuleTypeContent(ref[0], ref[1], (val ? 1 : 0));

		if (val && $(this).parent().find('a.open_more').hasClass('active') == false) {
			openClose($(this).parent().find('a.open_more'));
		} else if (!val && $(this).parent().find('a.open_more').hasClass('active') != false) {
			openClose($(this).parent().find('a.open_more'));
		}

		setBackTypesContents($(this), type);

		setFrontTypesContents($('#man_actions .d'+cla), type, val);
	});
});
</script>
<?php require 'bot.inc.php'; ?>