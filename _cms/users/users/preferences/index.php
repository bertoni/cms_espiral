<?php
$change_pass=0;
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$module_code = 'users-preferences';
$page_code   = 'users-preferences';


// Se o formulário foi enviado
if (isset($_POST['check'])) {


    // Verificamos se o nome de usuário e e-mail são válidos
    if ($_POST['nome'] != '' && $_POST['email'] != '') {

        // Aplicamos as preferências
        clearPreferencesCookies();
        setPreferencesCookies($_POST['tema']);

        $tema = $_POST['tema'];

        if ($_POST['senha_atual'] == ''
            && $_POST['senha_txt'] == ''
            && $_POST['senha_conf'] == ''
        ) {
            // Se não foi informada uma nova senha
            $USER_LOGGED->setAttribute('name',$_POST['nome']);
            $USER_LOGGED->setAttribute('email',$_POST['email']);
            $USER_LOGGED->save();

            $_SESSION['user_cms'] = serialize($USER_LOGGED);

            $_GET['ok'] = $i18n->_('Dados alterados, senha não alterada');
        } else {
           // Se foi informada
           $hash = sha1(SALT . trim($_POST['senha_atual']));

           if ( $hash == $USER_LOGGED->getAttribute('pass')) {
               // Se a senha atual correta foi informada

               if ($_POST['senha_txt'] == $_POST['senha_conf']
                   && $_POST['senha_txt'] != ''
                   && $_POST['senha_conf'] != ''
               ) {
                   // Se a senha for igual a confirmação e ambas não forem vazias
                   $USER_LOGGED->setAttribute('name',$_POST['nome']);
                   $USER_LOGGED->setAttribute('email',$_POST['email']);
                   $USER_LOGGED->setAttribute('change_pass',0);
                   $USER_LOGGED->save();
                   $USER_LOGGED->changePass($_POST['senha_txt']);

                   $_SESSION['user_cms'] = serialize($USER_LOGGED);

                   $_GET['ok'] = $i18n->_('Dados alterados, senha alterada');
               } else {
                   $_GET['erro'] = $i18n->_('A nova senha digitada não é igual a confirmação');
               }
           } else {
               $_GET['erro'] = $i18n->_('A senha atual informada está errada');
           }
        }
    } else {
        $_GET['erro'] = $i18n->_('Não foi possível gravar a alteração, dados inválidos');
    }
}

require 'top.inc.php';
?>


<h1 style="margin:30px 0 60px 20px;" class="float-left"><?php echo $i18n->_('Preferências do Usuário'); ?></h1>

<div class="clear"></div>

<form action="" name="form_senha" method="POST">
	<input type="hidden" name="check" id="check" value="1" />

	<div class="float-left w30" style="margin-left: 100px;">

        <input type="hidden" name="check" value="1" />

        <label for="nome"><?php echo $i18n->_('Nome'); ?></label>*<br/>
        <input class="text required big" type="text" name="nome" id="nome" value="<?php echo $USER_LOGGED->getAttribute('name')?>" style="width: 97%;" />

        <br/><br/><br/>
        <h2><?php echo $i18n->_('Trocar a senha'); ?></h2>

		<label for="senha_atual"><?php echo $i18n->_('Senha Atual'); ?></label>*<br/>
		<input class="text required big" type="password" name="senha_atual" id="senha_atual" value="" style="width: 97%;" />

        <br/><br/>
        <label for="senha_txt"><?php echo $i18n->_('Nova Senha'); ?></label>*<br/>
		<input class="text required big" type="password" name="senha_txt" id="senha_txt" value="" style="width: 97%;" />

		<br/><br/>
		<label for="senha_conf"><?php echo $i18n->_('Confirmação de nova senha'); ?></label>*<br/>
		<input class="text required big" type="password" name="senha_conf" id="senha_conf" value="" style="width: 97%;" />

		<br/><br/>
		<p class="aright">
			<a class="button submit" href=""><?php echo $i18n->_('Salvar'); ?></a>
		</p>
	</div>

	<div class="float-left w40" style="margin-left: 60px;">
            <label for="email"><?php echo $i18n->_('E-mail'); ?></label>*<br/>
            <input class="text required big" type="text" name="email" id="email" value="<?php echo $USER_LOGGED->getAttribute('email')?>" style="width: 97%;" />

            <br/><br/><br/>

            <h2><?php echo $i18n->_('Preferências de exibição'); ?></h2>
            <label for="tema"><?php echo $i18n->_('Tema'); ?></label>
            <select style="width:200px" name="tema" id="tema">
                <?php
                $arr_temas = array();
                $handler = opendir($_SERVER['DOCUMENT_ROOT'].'/_cms/css/temas/');
                while ($css = readdir($handler)) {
                    if ($css != "." && $css != "..") {
                        $arr_temas[$css] = ucfirst(str_replace('.css','',$css));
                    }
                }
                closedir($handler);

                foreach ($arr_temas as $arquivo_css=>$nome_tema) {
                    echo '<option '.($tema==$arquivo_css?' selected="selected"':'').' value="'.$arquivo_css.'">'.$nome_tema.'</option>';
                }

                ?>
            </select>
            <br/><br/><br/>

			<h2><?php echo $i18n->_('Informações sobre a senha'); ?></h2>
			<p><?php echo $i18n->_('explicacao-senha'); ?></p>

            <h2><?php echo $i18n->_('Sugestão de senha'); ?>: <strong><?php echo makeSafePassword() ?></strong></h2>

	</div>

	<div class="clear"></div>
</form>

<?php require 'bot.inc.php'; ?>