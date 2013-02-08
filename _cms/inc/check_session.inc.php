<?php
/**
 * Verifica se o usuário está realmente logado
 *
 * PHP Version 5.3
 *
 * @category Page
 * @package  CMS
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */


if (isset($_SESSION['login_cms']) && $_SESSION['login_cms']) {
    // Prepara o objeto reservado $user
    $USER_LOGGED = unserialize($_SESSION['user_cms']);
} else if (isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
    $ret = Users::loginCms($_COOKIE['user'], $_COOKIE['pass']);
    if ($ret) {
        $USER_LOGGED = unserialize($_SESSION['user_cms']);
    } else {
        header('Location: /_cms/?alert=' . $i18n->_('Você não tem permissão para ver essa área') . '.');
        exit;
    }
} else {
    header('Location: /_cms/?alert=' . $i18n->_('Você não tem permissão para ver essa área') . '.');
    exit;
}

if (!isset($change_pass)) {
    $change_pass = $USER_LOGGED->getAttribute('change_pass');
}

if ($change_pass == 1 && $_SERVER['SCRIPT_NAME'] != '/_cms/users/users/preferences/index.php') {
    header('Location: /_cms/users/users/preferences/?info=' . $i18n->_('Antes de usar o sistema é necessário escolher uma nova senha'));
    exit;
}

?>