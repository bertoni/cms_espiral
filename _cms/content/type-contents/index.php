<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = 't_con';

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

$pagina = 1;
if (isset($_GET['pag'])) {
    $pagina = $_GET['pag'];
}

$str_filters_in_use = array();
$search_status = '';
if (isset($_GET['status'])) {
    $search_status = new Status($_GET['status']);
    if (!$search_status->exists()) {
        $search_status = '';
    } else {
        $str_filters_in_use[] = $i18n->_('Status') . ' = ' . $search_status->getAttribute('name_status');
    }
}

$pag              = new Pagination(TypeContents::getTypeContents('', '', '', $search_status), $pagina, DEFAULT_PER_LIST);
$arr_type_content = TypeContents::getTypeContents($pag->getInicio(), $pag->getFim(), 'name_type_content', $search_status);

require 'top.inc.php';

if ($RULE['new']) {
    echo '<a class="button float-right" href="form.php">' . $i18n->_('Adicionar') . '</a>';
}
?>
<h1><?php echo $contenModule->getAttribute('name_content_module'); ?></h1>
<a href="#" id="control_filters"><?php echo $i18n->_('Filtrar resultados'); ?></a>

<div id="cont_filters">
    <form name="form_search" action="" method="get">
        <span><?php echo $i18n->_('Status do Tipo de Conteúdo'); ?></span><br/>
        <?php
        $arr_status = Status::getStatus();
        if ($arr_status && count($arr_status) > 0) {
            foreach ($arr_status as $key=>$obj_status) {
                echo '<input class="radio" type="radio" name="status" id="status_'.$obj_status->getAttribute('id_status').'" value="' . $obj_status->getAttribute('id_status') . '"' . ($search_status instanceof Status && $search_status->getAttribute('id_status') == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') . ' /> <label for="status_'.$obj_status->getAttribute('id_status').'" class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
            }
        }
        ?>

        &nbsp;<a class="button search" href="index.php"><img src="/_cms/img/btn_search.png" alt="" /></a>
    </form>
</div>

<hr class="clear"/>

<div class="pagination">
	<?php echo $pag->getHtmlFullPaginacao() ?>

    <?php
    if (count($str_filters_in_use) > 0) {
        echo '<br/><p><strong>' . $i18n->_('Filtrando por') . ':</strong> ' . implode(', ', $str_filters_in_use) . '</p>';
    }
    ?>
</div>

<div id="main-list">
<?php
$rule_rules = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('ru_ty'));
if ($rule_rules == '') {
    $rule_rules = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('ru_ty'));
}
$rule_field = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('ty_fi'));
if ($rule_field == '') {
    $rule_field = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('ty_fi'));
}
$rule_relati = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('ty_re'));
if ($rule_relati == '') {
    $rule_relati = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('ty_re'));
}
if (!$arr_type_content || sizeof($arr_type_content) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Identificação') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_type_content as $key=>$type_content) {
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $type_content->getAttribute('id_type_content') . '</td>
	    	<td>' . $type_content->getAttribute('name_type_content') . '</td>
	    	<td>' . $type_content->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td><nobr>
                ' . ($rule_relati ? '<a class="button small" href="type-contents-relationship/?id=' . urlencode($type_content->getAttribute('id_type_content')) . '">' . $i18n->_('Definir Relacionamentos') . '</a>' : '') . '
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($type_content->getAttribute('id_type_content')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($rule_rules  ? '<a class="button small" href="type-contents-actions/?id=' . urlencode($type_content->getAttribute('id_type_content')) . '">' . $i18n->_('Definir Ações') . '</a>' : '') . '
                ' . ($rule_field  ? '<a class="button small" href="type-contents-form-fields/?id=' . urlencode($type_content->getAttribute('id_type_content')) . '">' . $i18n->_('Definir Campos') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $type_content->getAttribute('id_type_content') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
            </nobr></td>
	    </tr>
	    ';
	}
	echo '
	</table>';
}
?>
</div>
<hr/>


<div class="pagination bottom">
	<?php echo $pag->getHtmlPaginas(); ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('a.remove').click(function(e){
		e.preventDefault();
		var id = $(this).attr('href');
		var parent = $(this).parent().parent().parent();
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse Tipo de Conteúdo?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeTypeContentById',
                dataType: 'json',
                async: true,
                success: function(data) {
                    var status = (data.status == '0' ? 'erro' : 'ok');
                    setaMsgGeral(status, data.msg);
                    if (data.status == '1') {
                        parent.remove();
                    }
                }
            });
        }
	});
});
</script>

<?php require 'bot.inc.php'; ?>