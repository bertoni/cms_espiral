<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$page_code   = 'users';
$module_code = 'users';

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

$pag       = new Pagination(Users::getUsers('', ''), $pagina, DEFAULT_PER_LIST);
$arr_users = Users::getUsers($pag->getInicio(), $pag->getFim(), 'name');

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
$rule_mod = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('ru_us'));
if ($rule_mod == '') {
    $rule_mod = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('ru_us'));
}
$rule_cont = Rules::checkRuleUser(new Actions('lis'), $USER_LOGGED, new ContentsModule('r_u_c'));
if ($rule_cont == '') {
    $rule_cont = Rules::checkRuleProfile(new Actions('lis'), $USER_LOGGED->getAttribute('profile'), new ContentsModule('r_u_c'));
}
if (!$arr_users || sizeof($arr_users) == 0) {
	echo '<h2>' . $i18n->_('Nenhum registro foi encontrado') . '.</h2>';
} else {
    $arr_profiles = listPermissionsProfiles($USER_LOGGED->getAttribute('profile'));
    echo '
    <table>
    	<tr>
    		<th>' . $i18n->_('Perfil') . '</th>
    		<th>' . $i18n->_('Nome') . '</th>
    		<th>' . $i18n->_('E-mail') . '</th>
    		<th>' . $i18n->_('Status') . '</th>
    		<th>' . $i18n->_('Logins') . '</th>
    		<th width="1%">' . $i18n->_('Ações') . '</th>
    	</tr>';
	foreach ($arr_users as $key=>$user) {
	    if ($arr_profiles[$user->getAttribute('profile')->getAttribute('id_profile')]['show']) {
    	    echo '
    	    <tr' . ($key%2==0 ? ' class="even"' : '' ) . '>
    	    	<td>' . $user->getAttribute('profile')->getAttribute('profile') . '</td>
    	    	<td>' . $user->getAttribute('name') . '</td>
    	    	<td>' . $user->getAttribute('email') . '</td>
    	    	<td>' . $user->getAttribute('status')->getAttribute('name_status') . '</td>
    	    	<td>' . $user->getAttribute('num_logins') . '</td>
    	    	<td><nobr>';
    	        $edit = 1;
    	        if ($user->getAttribute('id_user') != $USER_LOGGED->getAttribute('id_user')) {
    	            if (!$arr_profiles[$user->getAttribute('profile')->getAttribute('id_profile')]['edit']) {
    	                $edit = 0;
    	            }
    	        }
    	    	if ($edit) {
                    echo ($RULE['det'] ? '<a class="button small" href="form.php?id=' . urlencode($user->getAttribute('id_user')) . '">' . $i18n->_('Editar') . '</a> ' : '');
                    if ($user->getAttribute('id_user') != $USER_LOGGED->getAttribute('id_user')) {
                        echo ($rule_mod ? '<a class="button small" href="actions-module/?id=' . urlencode($user->getAttribute('id_user')) . '">' . $i18n->_('Permissões Módulos') . '</a> ' : '');
                        echo ($rule_cont ? '<a class="button small" href="actions-contents/?id=' . urlencode($user->getAttribute('id_user')) . '">' . $i18n->_('Permissões Conteúdos') . '</a> ' : '');
                        echo ($RULE['del'] ? '<a class="button small remove" href="' . $user->getAttribute('id_user') . '" title="' . $i18n->_('Remover') . '"><img src="/_cms/img/icon.delete.png" alt="' . $i18n->_('Remover') . '" /></a>' : '');
                    }
                }
            echo'</nobr></td>
    	    </tr>
    	    ';
	    }
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
        var ret = confirm("<?php echo $i18n->_('Tem certeza que deseja excluir esse Usuário?'); ?>\n<?php echo $i18n->_('Isso não pode ser desfeito'); ?>!");

        if (ret) {
            $.ajax({
                type: 'POST',
                url: '/inc/functions_ajax.inc.php',
                data: 'id=' + id + '&action=removeUserById',
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
