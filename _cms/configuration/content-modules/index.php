<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'conf';
$page_code   = 'tmod';

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
        $str_filters_in_use[] =  $i18n->_('Status') . ' = ' . $search_status->getAttribute('name_status');
    }
}

$search_modules = '';
if (isset($_GET['module'])) {
    $search_modules = new Modules($_GET['module']);
    if (!$search_modules->exists()) {
        $search_modules = '';
    } else {
        $str_filters_in_use[] = $i18n->_('Módulo') . ' = ' . $search_modules->getAttribute('name_module');
    }
}

$pag            = new Pagination(ContentsModule::getContentsModules('', '', '', $search_status, $search_modules), $pagina, DEFAULT_PER_LIST);
$arr_ct_modules = ContentsModule::getContentsModules($pag->getInicio(), $pag->getFim(), 'name_content_module', $search_status, $search_modules);

require 'top.inc.php';

if ($RULE['new']) {
    echo '<a class="button float-right" href="form.php">' . $i18n->_('Adicionar') . '</a>';
}
?>
<h1><?php echo $contenModule->getAttribute('name_content_module'); ?></h1>

<a href="#" id="control_filters"><?php echo $i18n->_('Filtrar resultados'); ?></a>

<div id="cont_filters">
    <form name="form_search" action="" method="get">

        <label for="status"><?php echo $i18n->_('Status do Conteúdo de Módulo'); ?></label><br/>
        <?php
        $arr_status = Status::getStatus();
        if ($arr_status && count($arr_status) > 0) {
            foreach ($arr_status as $key=>$obj_status) {
                echo '<input class="radio" type="radio" id="status_'.$obj_status->getAttribute('id_status').'" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($search_status instanceof Status && $search_status->getAttribute('id_status') == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') . ' /> <label for="status_'.$obj_status->getAttribute('id_status').'" class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
            }
        }
        ?>

        <br/><br/>
        <label for="module"><?php echo $i18n->_('Módulo'); ?></label><br/>
        <select class="text required" name="module" id="module">
            <option value=""><?php echo $i18n->_('Escolha o módulo'); ?></option>
            <?php
            $arr_modules = Modules::getModules('', '', 'name_module');
            if ($arr_modules && count($arr_modules) > 0) {
                foreach ($arr_modules as $key=>$obj_module) {
                    echo '<option value="' . $obj_module->getAttribute('id_module') . '"' . ($search_modules instanceof Modules && $search_modules->getAttribute('id_module') == $obj_module->getAttribute('id_module') ? ' selected="selected"' : '') . '>' . $obj_module->getAttribute('name_module') . '</option>';
                }
            }
            ?>
        </select>

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
if (!$arr_ct_modules || sizeof($arr_ct_modules) == 0) {
	echo "<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>";
} else {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Identificação') . '</th>
    		<th>' . $i18n->_('Módulo') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th>' . $i18n->_('Visível') . '</th>
    		<th>' . $i18n->_('Url') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_ct_modules as $key=>$ct_module) {
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $ct_module->getAttribute('id_content_module') . '</td>
	    	<td>' . $ct_module->getAttribute('module')->getAttribute('name_module') . '</td>
	    	<td>' . $ct_module->getAttribute('name_content_module') . '</td>
	    	<td>' . $ct_module->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td>' . ($ct_module->getAttribute('visible') ? 'Sim' : 'Não') . '</td>
	    	<td>' . $ct_module->getAttribute('url') . '</td>
	    	<td><nobr>
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($ct_module->getAttribute('id_content_module')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $ct_module->getAttribute('id_content_module') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
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
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse Conteúdo Módulo?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeContentModuleById',
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