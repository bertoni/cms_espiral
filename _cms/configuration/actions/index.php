<?php
/**
 * Arquivo de listagem
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

$pagina = 1;
if (isset($_GET['pag'])) {
    $pagina = $_GET['pag'];
}

$pag         = new Pagination(Actions::getActions('', ''), $pagina, DEFAULT_PER_LIST);
$arr_actions = Actions::getActions($pag->getInicio(), $pag->getFim(), 'name_action');

require 'top.inc.php';

if ($RULE['new']) {
    echo '<a class="button float-right" href="form.php">' . $i18n->_('Adicionar') . '</a>';
}
?>
<h1><?php echo $contenModule->getAttribute('name_content_module'); ?></h1>
<hr class="clear"/>

<div class="pagination">
	<?php echo $pag->getHtmlFullPaginacao() ?>
</div>

<div id="main-list">
<?php
if (!$arr_actions || sizeof($arr_actions) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Identificação') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('Filha de') . '</th>
    		<th>' . $i18n->_('Descrição') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_actions as $key=>$action) {
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $action->getAttribute('id_action') . '</td>
	    	<td>' . $action->getAttribute('name_action') . '</td>
	    	<td>' . ($action->getAttribute('action_parent') instanceof Actions ? $action->getAttribute('action_parent')->getAttribute('name_action') : ' - ') . '</td>
	    	<td>' . $action->getAttribute('description') . '</td>
	    	<td><nobr>
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($action->getAttribute('id_action')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $action->getAttribute('id_action') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
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
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir essa ação?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeActionById',
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