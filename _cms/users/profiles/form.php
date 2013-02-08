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

$id_profile     = '';
$status         = '';
$name_profile   = '';
$profile_parent = '';

$profile = new Profiles(isset($_GET['id']) ? $_GET['id'] : '');
if ($profile->exists()) {
    $id_profile     = $profile->getAttribute('id_profile');
    $status         = $profile->getAttribute('status')->getAttribute('id_status');
    $name_profile   = $profile->getAttribute('profile');
    $profile_parent = ($profile->getAttribute('profile_parent') instanceof Profiles ? $profile->getAttribute('profile_parent')->getAttribute('id_profile') : '');
}

require 'top.inc.php';
?>

<h1><a href="index.php"><?php echo $contenModule->getAttribute('name_content_module'); ?></a> &raquo; <?php echo (isset($_GET['id']) && $_GET['id'] != '' ? $i18n->_('Editar') : $i18n->_('Adicionar')); ?></h1>

<form action="proc.php" name="form_module" method="post">
	<input type="hidden" name="check" id="check" value="0" />
	<input type="hidden" name="action" id="action" value="0" />
	<input type="hidden" name="history_log" id="history_log" value="" />

	<label for="id"><?php echo $i18n->_('Identificação do Perfil'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="id" id="id" maxlength="3"<?php echo ($id_profile != '' ? ' readonly="readonly"' : ''); ?> value="<?php echo $id_profile; ?>" />

	<br/><br/>
	<label for="name"><?php echo $i18n->_('Nome Perfil'); ?></label>*<br/>
	<input class="text required big w100" type="text" name="name" id="name" maxlength="20" value="<?php echo $name_profile; ?>" />

	<br/><br/>
	<label for="profile"><?php echo $i18n->_('Perfil Superior'); ?></label><br/>
	<select class="text w30" name="profile" id="profile">
		<option value=""><?php echo $i18n->_('Escolha o perfil'); ?></option>
		<?php
		$arr_profiles = Profiles::getProfiles('', '', 'profile');
		if ($arr_profiles && count($arr_profiles) > 0) {
    		foreach ($arr_profiles as $key=>$obj_profile) {
    		    if ($id_profile != $obj_profile->getAttribute('id_profile')) {
    		        echo '<option value="' . $obj_profile->getAttribute('id_profile') . '"' . ($profile_parent == $obj_profile->getAttribute('id_profile') ? ' selected="selected"' : '') . '>' . $obj_profile->getAttribute('profile') . '</option>';
    		    }
    		}
		}
		?>
	</select>

	<br/><br/>
	<label for="status"><?php echo $i18n->_('Status do Perfil'); ?></label>*<br/>
	<?php
	$arr_status = Status::getStatus();
	if ($arr_status && count($arr_status) > 0) {
	    foreach ($arr_status as $key=>$obj_status) {
	        echo '<input class="radio" type="radio" name="status" value="' . $obj_status->getAttribute('id_status') . '"' . ($status == $obj_status->getAttribute('id_status') ? ' checked="checked"' : '') .' /> <label class="radio_label">' . $obj_status->getAttribute('name_status') . '</label>';
	    }
	}
	?>

	<hr/>
	
	<p class="aright">
		<?php
		if ($profile->exists() && $RULE['edt']) {
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

</form>

<script type="text/javascript" src="<?php echo PATH_CMS_URL; ?>/js/validate.js"></script>

<?php require 'bot.inc.php'; ?>