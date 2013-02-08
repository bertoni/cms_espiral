<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'filte';
$page_code   = 'filte';

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

$search_name = '';
if (isset($_GET['name'])) {
    $search_name = $_GET['name'];
    $str_filters_in_use[] = $i18n->_('Nome do Filtro') . ' = ' . $_GET['name'];
}

$search_parent = null;
if (isset($_GET['parent_ok'])) {
    $search_parent = new Filters($_GET['parent_ok']);
    if (!$search_parent->exists()) {
        $search_parent = null;
    } else {
        $str_filters_in_use[] = $i18n->_('Filtro Associado') . ' = ' . $search_parent->getAttribute('name_filter');
    }
}

$pag         = new Pagination(Filters::getFilters('', '', '', $search_status, $search_name, $search_parent), $pagina, DEFAULT_PER_LIST);
$arr_filters = Filters::getFilters($pag->getInicio(), $pag->getFim(), 'name_filter', $search_status, $search_name, $search_parent);

require 'top.inc.php';

if ($RULE['new']) {
    echo '<a class="button float-right" href="form.php">' . $i18n->_('Adicionar') . '</a>';
}
?>
<h1><?php echo $contenModule->getAttribute('name_content_module'); ?></h1>
<a href="#" id="control_filters"><?php echo $i18n->_('Filtrar resultados'); ?></a>

<div id="cont_filters">
    <form name="form_search" action="" method="get">

        <label for="status"><?php echo $i18n->_('Status do Filtro'); ?></label><br/>
        <?php
        $arr_status = Status::getStatus();
        if ($arr_status && count($arr_status) > 0) {
            foreach ($arr_status as $key=>$obj_status) {
                echo '<input id="status_'.$obj_status->getAttribute('id_status').'" class="radio" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($search_status instanceof Status && $search_status->getAttribute('id_status') == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') . ' /><label for="status_'.$obj_status->getAttribute('id_status').'" class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
            }
        }
        ?>

        <br/><br/>
        <div class="pRelative">
            <label for="parent"><?php echo $i18n->_('Filtro Associado'); ?></label><br/>
            <input type="text" class="text required w30" name="parent" id="parent" autocomplete="off" value="<?php echo ($search_parent instanceof Filters ? $search_parent->getAttribute('name_filter') : ''); ?>" />
            <div id="listener-filter-parent" class="w31"></div>
            <input type="hidden" name="parent_ok" value="<?php echo ($search_parent instanceof Filters ? $search_parent->getAttribute('id_filter') : ''); ?>" />
        </div>

        <br/><br/>
        <label for="module"><?php echo $i18n->_('Nome do Filtro'); ?></label><br/>
        <input type="text" class="text required w30" name="name" id="name" value="<?php echo $search_name; ?>" />

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
if (!$arr_filters || sizeof($arr_filters) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Identificação') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('Filtros Associados') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_filters as $key=>$filter) {
	    $child = Filters::getFilters('', '', '', '', '', $filter);
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $filter->getAttribute('id_filter') . '</td>
	    	<td>' . $filter->getAttribute('name_filter') . '</td>
	    	<td><a href="/_cms/filters/filters/?parent_ok=' . $filter->getAttribute('id_filter') . '">' . ($child ? count($child) : 0) . '</a></td>
	    	<td>' . $filter->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td><nobr>
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($filter->getAttribute('id_filter')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $filter->getAttribute('id_filter') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
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

	//função que lista os nomes
	$('input[name="parent"]').keyup(function(){
		$('#listener-filter-parent').hide();
		$('input[type="hidden"][name="parent_ok"]').val("");
		var valor = $(this).val();
		if (valor.length > 2) {
			var results = '';
			$.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'name=' + valor + '&action=listFiltersByName',
                dataType: 'json',
                async: true,
                success: function(data) {
					if(data.status == '1'){
						$.each(data.filters, function(i, obj){
							var alter='';
							if (i%2==0) {alter = ' class="alt"';} else {alter = '';}
							results += '<a href="' + obj.id + '"' + alter + '>' + obj.name + '</a>';
						});
						$('#listener-filter-parent').append(results);
						$('#listener-filter-parent').show();
					}
                }
            });
			if (results == '') { $('#listener-filter-parent').html(''); }
		}
	});

	$('#listener-filter-parent a').live('click',function(e){
		e.preventDefault();
		$('input[name="parent"]').val( $(this).text() );
		$('input[type="hidden"][name="parent_ok"]').val( $(this).attr('href') );
		$('#listener-filter-parent').hide();
	});

	$('a.remove').click(function(e){
		e.preventDefault();
		var id = $(this).attr('href');
		var parent = $(this).parent().parent().parent();
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse Filtro?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeFilterById',
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