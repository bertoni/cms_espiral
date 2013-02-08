<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'users';
$page_code   = 'users';

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

$id_user     = '';
$status      = '';
$profile     = '';
$change_pass = '';
$logins      = '';
$last_logins = '';
$date_entry  = '';
$name        = '';
$email       = '';
$log         = '';

$user = new Users(isset($_GET['id']) ? $_GET['id'] : '');
if ($user->exists()) {
    
    $arr_profiles = listPermissionsProfiles($USER_LOGGED->getAttribute('profile'));

    if ($user->getAttribute('id_user') != $USER_LOGGED->getAttribute('id_user') && !$arr_profiles[$user->getAttribute('profile')->getAttribute('id_profile')]['edit']) {
        header('Location: /_cms/?info=' . $i18n->_('Você não tem permissão para editar este usuário') . '.');
        exit;
    }
    
    $id_user     = $user->getAttribute('id_user');
    $status      = $user->getAttribute('status')->getAttribute('id_status');
    $profile     = $user->getAttribute('profile')->getAttribute('id_profile');
    $change_pass = $user->getAttribute('change_pass');
    $logins      = $user->getAttribute('num_logins');
    $last_logins = $user->getAttribute('date_last_login');
    $date_entry  = $user->getAttribute('date_entry');
    $name        = $user->getAttribute('name');
    $email       = $user->getAttribute('email');
    $log         = $user->getAttribute('log');
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_user" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="id" id="id" value="<?php echo $id_user; ?>" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="name"><?php echo $i18n->_('Nome Usuário'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="100" value="<?php echo $name; ?>" />

	<br/><br/>
	<label for="profile"><?php echo $i18n->_('Perfil'); ?></label>*<br/>
	<select class="text required w30" name="profile" id="profile">
		<option value=""><?php echo$i18n->_('Escolha o perfil'); ?></option>
		<?php
		$arr_profiles = Profiles::getProfiles('', '', 'profile');
		if ($arr_profiles && count($arr_profiles) > 0) {
    		foreach ($arr_profiles as $key=>$obj_profile) {
    		    $show = 1;
    		    if ($USER_LOGGED->getAttribute('profile')->getAttribute('id_profile') != 'dev' && $obj_profile->getAttribute('id_profile') == 'dev') {
    		        $show = 0;
    		    }
    		    if ($show) {
    			    echo '<option value="' . $obj_profile->getAttribute('id_profile') . '"' . ($profile == $obj_profile->getAttribute('id_profile') ? ' selected="selected"' : '') . '>' . $obj_profile->getAttribute('profile') . '</option>';
    		    }
    		}
		}
		?>
	</select>

	<br/><br/>
	<label for="email"><?php echo $i18n->_('E-mail do Usuário'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="email" id="email" maxlength="200" value="<?php echo $email; ?>" />

	<br/><br/>
	<label for="status"><?php echo $i18n->_('Status do Usuário'); ?></label>*<br/>
	<?php
	$arr_status = Status::getStatus();
	if ($arr_status && count($arr_status) > 0) {
	    foreach ($arr_status as $key=>$obj_status) {
	        echo '<input class="radio" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($status == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') .' /> <label class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
	    }
	}
	?>

	<?php
	if (!empty($id_user)) {
	?>
	<br/><br/>
	<label for="date_entry"><?php echo $i18n->_('Data de entrada'); ?></label><br/>
	<input class="text big w100" type="text" name="date_entry" id="date_entry" readonly="readonly" value="<?php echo strftime('%d/%m/%Y - %H:%M:%S', $date_entry); ?>" />

	<br/><br/>
	<label for="date_login"><?php echo $i18n->_('Data do último login'); ?></label><br/>
	<input class="text big w100" type="text" name="date_login" id="date_login" readonly="readonly" value="<?php echo (count($last_logins) > 0 ? strftime('%d/%m/%Y - %H:%M:%S', $last_logins[0]) : ''); ?>" />

	<br/><br/>
	<label for="logins"><?php echo $i18n->_('Número de logins'); ?></label><br/>
	<input class="text big w100" type="text" name="logins" id="logins" readonly="readonly" value="<?php echo $logins; ?>" />

	<br/><br/>
	<label for="change_pass"><?php echo $i18n->_('Alterou a senha?'); ?></label><br/>
	<input class="radio" type="radio" disabled="disabled" name="change_pass"<?php echo ($change_pass ? ' checked="checked"' : ''); ?> /> <label class="radio_label"><?php echo $i18n->_('Sim'); ?></label>
	<input class="radio" type="radio" disabled="disabled" name="change_pass"<?php echo (!$change_pass ? ' checked="checked"' : ''); ?> /> <label class="radio_label"><?php echo $i18n->_('Não'); ?></label>
	<?php
	}
	?>

	<hr/>
	
	<p class="aright">
		<?php
		if ($user->exists() && $RULE['edt']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		if ($RULE['new']) {
    		    echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
    		}
		} else if ($RULE['new']) {
		    echo '<a class="button save">' . $i18n->_('Salvar') . '</a> ';
    		echo '<a class="button saveback">' . $i18n->_('Salvar e Voltar') . '</a> ';
    		echo '<a class="button savecreate">' . $i18n->_('Salvar e Criar Novo') . '</a> ';
		}
		echo '<a class="button close">' . $i18n->_('Voltar') . '</a> ';
		if ($RULE['new']) {
		    echo '<a class="button new">' . $i18n->_('Criar Novo') . '</a> ';
		}
		if ($RULE['del']) {
		    echo '<a class="button remove">' . $i18n->_('Remover') . '</a>';
		}
		?>
	</p>

	<?php
	if ($log != '' && $RULE['log']) {
		echo '<div id="log">' . nl2br($log) . '</div>';
	}
	?>

</form>

<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>

<?php require 'bot.inc.php'; ?>