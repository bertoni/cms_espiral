<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'cont';
$page_code   = (isset($_GET['cont']) ? $_GET['cont'] : '');

$typeContent = new TypeContents($page_code);
if (!$typeContent->exists() || $typeContent->getAttribute('status')->getAttribute('id_status') != 'a') {
    header('Location: /_cms/');
    exit;
}

require_once 'actions_rules_content.inc.php';
if (!$RULE['lis']) {
    header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão de ver este conteúdo') . '.');
    exit;
}

$str_filters_in_use = array();

$pagina = 1;
if (isset($_GET['pag'])) {
    $pagina = $_GET['pag'];
}

$search_status = '';
if (isset($_GET['status'])) {
    $search_status = new Status($_GET['status']);
    if (!$search_status->exists()) {
        $search_status = '';
    } else {
        $str_filters_in_use[] = $i18n->_('Status') . ' = ' . $search_status->getAttribute('name_status');
    }
}

$sql_search = '';
$sql_search['sql'] = '';

$search_name = '';
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $search_name        = $_GET['name'];
    $sql_search['sql'] .= ' AND ' . Contents::TABLE . '.title LIKE ? ';
    $sql_search['values'][] = '%' . $search_name . '%';
    $str_filters_in_use[] = $i18n->_('Título') . ' = ' . $search_name;
}

$arr_filters = array();
$arr_config  = ConfigForm::getConfigForms('', '', '', $typeContent);
if ($arr_config && count($arr_config) > 0) {
    foreach ($arr_config as $config_form) {
        if (array_key_exists($config_form->getAttribute('use_with_filter'), $TYPES_COMPARISON)) {
            $value = '';
            if (isset($_GET[$config_form->getAttribute('name')])) {
                $value                  = $_GET[$config_form->getAttribute('name')];
                $comp                   = $config_form->getAttribute('use_with_filter');
                $sql_search['sql']     .= ' AND ' . ExtraContents::TABLE . '.name = ? ';
                $sql_search['values'][] = $config_form->getAttribute('name');
                if (!is_array($value)) {
                    $sql_search['sql']     .= ' AND ' . ExtraContents::TABLE . '.value ' . ($comp == 'equal' ? '=' : 'LIKE') . ' ? ';
                    $sql_search['values'][] = ($comp == 'equal' ? $value : ($comp == 'like' ? $value . '%' : '%' . $value . '%'));

                    $filter_value = new Filters($value);
                    $str_filters_in_use[] = $config_form->getAttribute('label') . ' = ' . $filter_value->getAttribute('name_filter');
                } else {
                    $param = '';
                    $searc = array();
                    foreach ($value as $key=>$val) {
                        $param                 .= ($key ? ' OR ' : '') . '(?)';
                        $sql_search['values'][] = '%' . $val . '%';

                        $filter_value = new Filters($val);
                        $searc[]      = $filter_value->getAttribute('name_filter');
                    }
                    $sql_search['sql'] .= ' AND ' . ExtraContents::TABLE . '.value LIKE ' . $param . ' ';
                    $str_filters_in_use[] = $config_form->getAttribute('label') . ' = ' . implode(' ou ', $searc);
                }
            }
            $arr_filters[] = generationFiltersContent($config_form, $value);
        }
    }
}


$pag         = new Pagination(Contents::getContents('', '', '', $search_status, $typeContent, $sql_search), $pagina, DEFAULT_PER_LIST);
$arr_content = Contents::getContents($pag->getInicio(), $pag->getFim(), 'title ASC', $search_status, $typeContent, $sql_search);

require 'top.inc.php';

if ($RULE['new']) {
    echo '<a class="button float-right" href="form.php?cont=' . $_GET['cont'] . '">' . $i18n->_('Adicionar') . '</a>';
}
?>
<h1><?php echo $typeContent->getAttribute('name_type_content'); ?></h1>

<a href="#" id="control_filters"><?php echo $i18n->_('Filtrar resultados'); ?></a>

<div id="cont_filters">
    <form name="form_search" action="" method="get">
        <input type="hidden" name="cont" value="<?php echo $_GET['cont']; ?>" />
        <span><?php echo $i18n->_('Status do Conteúdo'); ?></span><br/>
        <?php
        $arr_status = Status::getStatus();
        if ($arr_status && count($arr_status) > 0) {
            foreach ($arr_status as $key=>$obj_status) {
                echo '<input class="radio" id="status_'.$obj_status->getAttribute('id_status').'" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($search_status instanceof Status && $search_status->getAttribute('id_status') == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') . ' /> <label for="status_'.$obj_status->getAttribute('id_status').'" class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
            }
        }
        ?>

        <br/><br/>
        <label for="module"><?php echo $i18n->_('Título do Conteúdo'); ?></label><br/>
        <input type="text" class="text required w30" name="name" id="name" value="<?php echo $search_name; ?>" />

        <?php
        if (count($arr_filters) > 0) {
            foreach ($arr_filters as $filters) {
                foreach ($filters as $filter) {
                    echo $filter['html'];
                }
            }
        }
        ?>

        &nbsp;<a class="button search" href="index.php"><img src="/_cms/img/btn_search.png" alt="" /></a>
    </form>
</div>

<hr/>

<div id="multiple-selection-options">
    <?php echo $RULE['del']?'<a class="button small" id="remove-multiple" href="">' . $i18n->_('Remover selecionados') . '</a>':''; ?>
    <?php echo $RULE['det']?'<a class="button small" id="enable-multiple" href="">' . $i18n->_('Definir status como ativo') . '</a>':''; ?>
</div>

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
if (!$arr_content || sizeof($arr_content) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    echo '
    <table>
    	<tr>
            <th width="1%">&nbsp;</th>
    		<th>' . $i18n->_('Título') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th>' . $i18n->_('Data Criação') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_content as $key=>$content) {
	    $date = $content->getAttribute('date_creation');
	    echo '
	    <tr class="' . ($key%2==0 ? ' even' : '' ) . '">
            <td class="toggle-selection"><input type="checkbox" name="select[]" class="select-checkbox" /></td>
	    	<td class="toggle-selection">' . $content->getAttribute('title') . '</td>
	    	<td class="toggle-selection status-container">' . $content->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td class="toggle-selection">' . (!empty($date) ? strftime('%d/%m/%Y - %H:%M', $date) : ' - ') . '</td>
	    	<td><nobr>
	    		' . ($RULE['rel'] ? '<a class="button small" href="relationship/?id=' . urlencode($content->getAttribute('id_content')) . '">' . $i18n->_('Relacionar') . '</a>' : '') . '
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?cont=' . $_GET['cont'] . '&id=' . urlencode($content->getAttribute('id_content')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $content->getAttribute('id_content') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
            </nobr></td>
	    </tr>
	    ';
	}
	echo '
	</table>';
}
?>
<hr/>
</div>

<div class="pagination bottom">
	<?php echo $pag->getHtmlPaginas(); ?>
</div>

<script type="text/javascript">
$(document).ready(function(){

    $('.toggle-selection').click(function(){
        if($(this).parent().hasClass('selected')) {
            $(this).parent().removeClass('selected');
            $(this).parent().find('.select-checkbox').attr('checked', false);
        } else {
            $(this).parent().addClass('selected');
            $(this).parent().find('.select-checkbox').attr('checked', true);
        }

        if ($('tr.selected').length > 0) {
            $('#multiple-selection-options').fadeIn(200);
        } else {
            $('#multiple-selection-options').fadeOut(200);
        }
    });

    $('#remove-multiple').click(function(e){
        e.preventDefault();
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir os conteúdos selecionados?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $('tr.selected').each(function() {
                var id = $(this).find('a.remove').attr('href');
                var parent = $(this);

                $.ajax({
                    type: 'POST',
                    url: '/inc/functions_ajax.inc.php',
                    data: 'id=' + id + '&action=removeContentById',
                    dataType: 'json',
                    async: true,
                    success: function(data) {
                        if (data.status == '1') {
                            parent.remove();
                        }
                    }
                });
            });
            setaMsgGeral('ok', <?php echo $i18n->_('Conteúdos selecionados removidos com sucesso'); ?>);
        }
    });

    $('#enable-multiple').click(function(e){
        e.preventDefault();

        $('tr.selected').each(function() {
            var id = $(this).find('a.remove').attr('href');
            var parent = $(this);

            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=enableContentById',
                dataType: 'json',
                async: true,
                success: function(data) {
                    if (data.status == '1') {
                        parent.find('.status-container').empty();
                        parent.find('.status-container').append('ativo(a)');
                    }
                }
            });
        });

        setaMsgGeral('ok', <?php echo $i18n->_('Status alterado com sucesso nos conteúdos selecionados'); ?>);
    });

	$('a.remove').click(function(e){
		e.preventDefault();
		var id = $(this).attr('href');
		var parent = $(this).parent().parent().parent();

        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse conteúdo?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeContentById',
                dataType: 'json',
                async: true,
                success: function(data) {
                    var status = (data.status == '0' ? 'erro' : 'ok');
                    setaMsgGeral(status, data.msg);
                    if (data.status == '1') {
                        parent.remove();
                        fixTableColors();
                    }
                }
            });

        }
	});
});
</script>

<?php require 'bot.inc.php'; ?>