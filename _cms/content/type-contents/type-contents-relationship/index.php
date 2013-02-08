<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = 'ty_re';

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

//$JS = array('/js/lib_rules.js');
require 'top.inc.php';
?>

<a class="button float-right" href="../"><?php echo $i18n->_('Voltar para a listagem'); ?></a>
<h1><a href="../"><?php echo $contenModule->getAttribute('name_content_module') ?></a> &raquo; <?php echo $typeContent->getAttribute('name_type_content'); ?></h1>
<hr/>

<div id="man_actions">
<?php
$types = TypeContents::getTypeContents('', '', 'name_type_content');
echo '<div id="list-permissions">';
if ($types) {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Tipo de Conteúdo') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>
    ';
    foreach ($types as $key=>$type) {
        $rule = TypeContents::checkTypeContentRTypeContent($typeContent, $type);
        echo '<tr' . ($key%2==0 ? ' class="even"' : '' ) . '>';
            echo '<td>' . $type->getAttribute('name_type_content') . '</td>';
            echo '<td><a class="button small' . ($rule ? '' : ' unlink') . '" title="' . $type->getAttribute('id_type_content') . '">' . ($rule ? $i18n->_('Remover') : $i18n->_('Permitir')) . '</a></td>';
        echo '</tr>';
    }
}
echo '</div>';
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#list-permissions a.button').click(function(e){
		e.preventDefault();
		var link = $(this)
		if (link.hasClass('unlink')) {
			var rule = true;
		} else {
			var rule = false;
		}
		var parent = "<?php echo $typeContent->getAttribute('id_type_content'); ?>";
		$.ajax({
			type: 'POST',
			url: '/inc/functions_ajax.inc.php',
			data: 'rule=' + rule + '&parent=' + parent + '&child=' + link.attr('title') + '&action=changeTypeContentsRTypeContents',
			dataType: 'json',
			async: true,
			success: function(data) {
				if (data.status == '0') {
					setaMsgGeral('erro', data.msg);
				} else {
					if (rule) {
						link.removeClass('unlink');
						link.html('Remover');
					} else {
						link.addClass('unlink');
						link.html('Permitir');
					}
				}
			}
		});
	});
});
</script>
<?php require 'bot.inc.php'; ?>