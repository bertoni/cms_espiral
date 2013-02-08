<?php
/**
 * Arquivo que trata o login de usuários
 *
 * PHP Version 5.3
 *
 * @category Page
 * @package  CMS
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

// Importa as configurações
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

// Configura o captcha Acessivel pergunta;resposta:outra resposta
$arr_captcha = $i18n->_('arr_captcha');

// Efetua o Logout
if ($_GET) {
    if (isset($_GET['logout'])) {
        session_destroy();
        clearSessionCookies();
        header('location: ?ok=' . $i18n->_('Você saiu do sistema'));
        exit;
    }
}

// Verifica se já existe uma sessão ou cookies
if (isset($_SESSION['login_cms']) && $_SESSION['login_cms']) {
    header('location: ' . PATH_CMS_URL . '/dashboard');
    exit;
} else if (isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
    $ret = Users::loginCms($_COOKIE['user'], $_COOKIE['pass']);
    if ($ret) {
        header('location: ' . PATH_CMS_URL . '/dashboard');
        exit;
    }
}

if ($_POST) {
    // Verifica se é necessário o captcha acessivel
    if (isset($_POST['captcha_acessivel'])) {
        if ($_SESSION['indice']) {
            $indice                 = $_SESSION['indice'];
            $arr_temp               = explode(';', $arr_captcha[$indice]);
            $arr_respostas_corretas = explode(':', $arr_temp[1]);
            $resposta_enviada       = strtolower($_POST['captcha_acessivel']);

            if (!in_array($resposta_enviada, $arr_respostas_corretas)) {
                header('location: /_cms/?alert=' . $i18n->_('Captcha Incorreto'));
                exit;
            }
        } else {
            header('location: /_cms/?alert=' . $i18n->_('Captcha Incorreto'));
            exit;
        }
    }

    // Se o usuário não estiver na tela de recuperar senha
    if (!isset($_POST['recuperar'])) {
        $ret = Users::loginCms($_POST['usuario'], $_POST['senha']);
        if ($ret) {

            // Se a checkbox "Lembre-me" for selecionada nós criamos cookies
            if (isset($_POST['remember'])) {
                if ($_POST['remember'] == 'on') {
                    setSessionCookies($_POST['usuario'], $_POST['senha']);
                }
            }

            // Tudo deu certo, redirecionamos o usuário autenticado.
            header('location: '.PATH_CMS_URL.'/dashboard');
            exit;
        } else {
            if (!isset($_SESSION['counter_fail'])) {
                $_SESSION['counter_fail'] = 1;
            } else {
                $_SESSION['counter_fail'] = $_SESSION['counter_fail'] + 1;
                if ($_SESSION['counter_fail'] > 3) {
                    $_GET['captcha'] = $i18n->_('Você falhou na tentativa de login algumas vezes') .
                    				   '.<br/>' . $i18n->_('Se você esqueceu de sua senha clique em') .
                    				   ' <a href="?recuperar-senha">"' . $i18n->_('Qual é a minha senha?') . '"</a>';
                }
            }
            $_GET['alert'] = $_SESSION['msg_cms'];
        }

        // Usuário esqueceu a senha
    } else if ($_POST['recuperar'] != '') {
        $user = Users::checkEmail($_POST['recuperar']);
        if ($user instanceof Users) {
            $nova_senha = makeSafePassword();

            $ret        = $user->changePass($nova_senha);
            if ($ret) {
                $destinatario = $user->getAttribute('email');
                $assunto      = $i18n->_('Envio de senha - CMS');
                $corpo        = '<p>' . $i18n->_('Sua senha para acessar o sistema é') . ': <strong>'
                                .$nova_senha.'</strong></p>';

                if (sendMail($destinatario, $assunto, $corpo)) {
                    $_GET['ok'] = $i18n->_('A nova senha foi enviada para seu e-mail');
                }
            } else {
                $_GET['alert'] = $i18n->_('Não foi possível alterar sua senha. Contate o Administrador.');
            }
        } else {
            $_GET['alert'] = $i18n->_('Este e-mail não existe.');
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="none" />
        <meta name="googlebot" content="noarchive" />
        <title><?php echo SYSTEM_NAME ?></title>
        <link rel="stylesheet" type="text/css" href="css/login.css" media="screen" />
        <script
            src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"
            type="text/javascript">
        </script>
    </head>
    <body>
        <div class="wrapper">
            <div class="content">
                <img src="img/system_logo.jpg" alt="<?php echo SYSTEM_NAME ?>" width="290" />
<?php
// Alertas
if (isset($_GET['alert'])) {
    echo '<div class="alert erro">'
                .$_GET['alert'].
            '<span>x</span></div>';
}

if (isset($_GET['captcha'])) {
    echo '<div class="alert">'
                .$_GET['captcha'].
            '<span>x</span></div>';
}

if (isset($_GET['ok'])) {
    echo '<div class="alert ok">'
                .$_GET['ok'].
            '<span>x</span></div>';
}

if (isset($_GET['recuperar-senha'])) {
?>
    <div class="alert"><?php echo $i18n->_('Informe seu e-mail de cadastro ou nome de usuário para receber a sua senha por e-mail'); ?>.<span>x</span></div>

    <div class="box w30">
        <form action="" method="post">
            <label for="recuperar"><?php echo $i18n->_('E-mail'); ?></label><br/>
            <input type="text" name="recuperar" id="recuperar" maxlength="30" />

            <div class="recuperar-container">
                <input class="button submit" type="submit" value="<?php echo $i18n->_('Gerar nova senha'); ?>" />
            </div>
            <div class="clear"></div>
        </form>
        <p class="info"><a href="/_cms"><?php echo $i18n->_('Fazer Login'); ?></a></p>
    </div><!-- .box -->
<?php
} else {
?>
    <div class="box w30">
        <form action="" method="post">
            <label for="usuario"><?php echo $i18n->_('E-mail'); ?></label><br/>
            <input type="text" name="usuario" id="usuario" maxlength="30" />

            <br/><br/>
            <label for="senha"><?php echo $i18n->_('Senha'); ?></label><br/>
            <input type="password" name="senha" id="senha" maxlength="32" />

            <?php
            if (isset($_SESSION['counter_fail'])) {
                if ($_SESSION['counter_fail'] > 3) {
        
                    $indice   = rand(0, (count($arr_captcha)-1));
                    $arr_temp = explode(";", $arr_captcha[$indice]);
                    $pergunta = $arr_temp[0];
        
                    $_SESSION['indice'] = $indice;
        
                    echo '<br/><br/>
                        <label for="captcha_acessivel">'.$pergunta.'</label><br/>
                        <input type="text" name="captcha_acessivel"'.
                        'id="captcha_acessivel" maxlength="20" />';
                }
            }
            ?>
            <br/><br/>
            <div style="float:left">
                <input style="position:relative;top:4px;" type="checkbox" name="remember" id="remember" />
                <label style="position:relative;top:6px;font-size:0.9em;" for="remember"><?php echo $i18n->_('Lembrar-me'); ?></label>
            </div>

            <div style="float:right">
                <input class="button submit" type="submit" value="<?php echo $i18n->_('Acessar o sistema'); ?>" />
            </div>
            <div class="clear"></div>
        </form>
        <p class="info"><a href="/_cms/?recuperar-senha"><?php echo $i18n->_('Qual é a minha senha?'); ?></a></p>
    </div><!-- .box -->
<?php
}
?>
            </div><!-- .content -->
        </div><!-- .wrapper -->
        <script>
        $(document).ready(function(){
        
        <?php if (isset($_GET['alert'])) { ?>
        
            esquerda = false;
            cycle_count = 0;
            tremor = setInterval(function(){
        
                if ($('.box').css('left') == 'auto') {
                    var left = 0;
                } else {
                    var left = parseInt($('.box').css('left'));
                }
        
        
                if (left <= -20) { esquerda = false; }
                if (left >= 20)  { esquerda = true;  }
                if (left == 0)   { cycle_count++;    }
        
                if (esquerda) {
                    $('.box').css('left', (left-10)+'px');
                } else {
                    $('.box').css('left', (left+10)+'px');
                }
        
                if (cycle_count == 7) {
                    clearInterval(tremor);
                    $('.box').css('left',0);
                }
            }, 5);
        <?php
        }
        ?>
            $('.alert span').click(function(){
                $(this).parent().fadeOut(200);
            })
        });
        </script>
    </body>
</html>