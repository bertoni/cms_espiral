<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'users';
$page_code   = 'prof';

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

$pag          = new Pagination(Profiles::getProfiles('', ''), $pagina, DEFAULT_PER_LIST);
$arr_profiles = Profiles::getProfiles($pag->getInicio(), $pag->getFim(), 'profile');

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
$rule_mod = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('ru_pe'));
if ($rule_mod == '') {
    $rule_mod = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('ru_pe'));
}
$rule_cont = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('r_p_c'));
if ($rule_cont == '') {
    $rule_cont = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('r_p_c'));
}
if (!$arr_profiles || sizeof($arr_profiles) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Identificação') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('Perfil Superior') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_profiles as $key=>$profile) {
	    echo '
	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
	    	<td>' . $profile->getAttribute('id_profile') . '</td>
	    	<td>' . $profile->getAttribute('profile') . '</td>
	    	<td>' . ($profile->getAttribute('profile_parent') instanceof Profiles ? $profile->getAttribute('profile_parent')->getAttribute('profile') : '') . '</td>
	    	<td>' . $profile->getAttribute('status')->getAttribute('name_status') . '</td>
	    	<td><nobr>
                ' . ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($profile->getAttribute('id_profile')) . '">' . $i18n->_('Editar') . '</a>' : '') . '
                ' . ($rule_mod ? '<a class="button small" href="actions-module/?id=' . urlencode($profile->getAttribute('id_profile')) . '">' . $i18n->_('Permissões Módulos') . '</a>' : '') . '
                ' . ($rule_cont ? '<a class="button small" href="actions-contents/?id=' . urlencode($profile->getAttribute('id_profile')) . '">' . $i18n->_('Permissões Conteúdos') . '</a>' : '') . '
                ' . ($RULE['del'] ? '<a class="button small remove" href="' . $profile->getAttribute('id_profile') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '') . '
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
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse Perfil?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeProfileById',
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