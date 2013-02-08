<?php
/**
 * Página inicial do CMS
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

$module_code = '';
$page_code   = 'dashboard';

require 'top.inc.php';
?>

<div class="box float-left w30">
	<!-- SYSTEM PHOTO - MAX WIDTH 260PX -->
	<img src="<?php echo PATH_CMS_URL; ?>/img/system_logo.jpg" alt="" />

	<br/><br/><h2><?= SYSTEM_NAME ?></h2>

	<p><?php echo $i18n->_('Produzido e mantido por'); ?>: <a href="http://espir.al" target="_blank">Espiral Interativa</a></p>
    <p><?php echo $i18n->_('Encontrou algum erro ou tem alguma sugestão, mande um e-mail para'); ?>: <a href="mailto:ti@espiralinterativa.com">ti@espiralinterativa.com</a></p>
	<br/>
</div><!-- .box.float-left.w30 -->

<div class="float-right w65">
	<h1><?php echo $i18n->_('Bem vindo ao CMS'); ?>, <?php echo $USER_LOGGED->getAttribute('name'); ?>.</h1>
	<?php
	if ($USER_LOGGED->getAttribute('num_logins') > 0) {
	    $logins = $USER_LOGGED->getAttribute('date_last_login');
	    echo '<p>' . $i18n->_('Seu último acesso foi em') . ' ' . strftime('%d/%m/%Y - %H:%M:%S', $logins[0]) . '.</p>';
	}
	?>


	<div class="box">
        <a href="/_cms/users/" class="icon">
            <img src="<?=PATH_CMS_URL.'/img/icons/users.png'?>" alt=""/><br/>
            <p>CMS<br/><?php echo $i18n->_('Gerenciar usuários'); ?></p>
        </a>

        <a href="/_cms/content/" class="icon">
            <img src="<?=PATH_CMS_URL.'/img/icons/logs.png'?>" alt=""/><br/>
            <p>CMS<br/><?php echo $i18n->_('Ir para Conteúdos'); ?></p>
        </a>

        <a href="/_cms/files/files.php" class="icon">
            <img src="<?=PATH_CMS_URL.'/img/icons/files.png'?>" alt=""/><br/>
            <p>CMS<br/><?php echo $i18n->_('Gerenciar Arquivos'); ?></p>
        </a>

        <a style="margin:0" href="/_cms/?logout" class="icon">
            <img src="<?=PATH_CMS_URL.'/img/icons/logout.png'?>" alt=""/><br/>
            <p>CMS<br/><?php echo $i18n->_('Sair do sistema'); ?></p>
        </a>

        <div class="clear"></div>
	</div><!-- .box -->
</div><!-- .float-right w65" -->

<?php require 'bot.inc.php'; ?>
