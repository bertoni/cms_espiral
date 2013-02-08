<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = '';
$page_code   = 'approvals';

require 'top.inc.php';
?>
<h1><?php echo $i18n->_('Aprovações'); ?></h1>
<hr class="clear"/>

<div id="multiple-selection-options">
    <a class="button small" id="enable-multiple" href=""><?php echo $i18n->_('Aprovar conteúdos selecionados'); ?></a>
</div>

<?php
$exist  = 0;
$arr_ty = TypeContents::getTypeContents('', '', '', new Status('a'));
if ($arr_ty && count($arr_ty) > 0) {
    foreach ($arr_ty as $type_cont) {
        $show = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, '', $type_cont);
        if ($show == '') {
            $show = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), '', $type_cont);
        }
        if ($show) {
            $arr_cont = Contents::getContents('', '', 'date_creation DESC', new Status('w'), $type_cont);
            if ($arr_cont && count($arr_cont) > 0) {
                echo '<h2><a href="' . PATH_CMS_URL . '/content/contents/?cont=' . $type_cont->getAttribute('id_type_content') . '">' . $type_cont->getAttribute('name_type_content') . '</a></h2>';
                echo '
                <table>
                	<tr>
                        <th width="1%">&nbsp;</th>
                		<th>' . $i18n->_('Nome') . '</th>
                		<th>' . $i18n->_('Status') . '</th>
                		<th width="1%">' . $i18n->_('Ações') . '</th>
                	</tr>';
                foreach ($arr_cont as $key=>$content) {
                    echo '
            	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
            	    	<td class="toggle-selection"><input type="checkbox" name="select[]" class="select-checkbox" /></td>
                        <td class="toggle-selection">' . $content->getAttribute('title') . '</td>
            	    	<td class="toggle-selection status-container">' . $content->getAttribute('status')->getAttribute('name_status') . '</td>
            	    	<td><nobr>
                            <a class="button small edit" title="' . $content->getAttribute('id_content') . '" href="' . PATH_CMS_URL . '/content/contents/form.php?cont=' . $type_cont->getAttribute('id_type_content') . '&id=' . urlencode($content->getAttribute('id_content')) . '">' . $i18n->_('Editar') . '</a>
                        </nobr></td>
            	    </tr>
            	    ';
                }
                echo '</table><hr />';
                $exist++;
            }
        }
    }
}
if (!$exist) {
    echo '<p>' . $i18n->_('Não existem conteúdos em aprovação') . '.</p>';
}
?>

<script>
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

    $('#enable-multiple').click(function(e){
        e.preventDefault();

        $('tr.selected').each(function() {
            var id = $(this).find('a.edit').attr('title');
            var parent = $(this);

            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=enableContentById',
                dataType: 'json',
                async: true,
                success: function(data) {
                    var status = (data.status == '0' ? 'erro' : 'ok');
                    if (data.status == '1') {
                        parent.find('.status-container').empty();
                        parent.find('.status-container').append('ativo(a)');
                    }
                }
            });
        });

        setaMsgGeral('ok', <?php echo $i18n->_('Status alterado com sucesso nos conteúdos selecionados'); ?>);
    });

});
</script>

<?php require 'bot.inc.php'; ?>