<?php
/**
 * Arquivo de configuração
 *
 * PHP Version 5.3
 *
 * @category Config
 * @package  Configuration
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */


/**
 * Defino o ambiente de desenvolvimento
 */
$ambiente = $_SERVER['SERVER_ADDR'];
if ($ambiente == '192.168.0.8') {
    define('DEV', true);
} else {
    define('DEV', false);
}


/**
 * Defino as Constantes da aplicação
 */
define('SYSTEM_NAME', 'EspiralCMS');
define('PATH_DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('PATH_CMS_DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/_cms');
define('PATH_CMS_URL', '/_cms');
define('SALT', SYSTEM_NAME . '3sp1r4l');
define('DEFAULT_PER_LIST', 15);
define("PATH_UPLOADS", "/uploads/");
define("PATH_UPLOADS_THUMBS", "/uploads/thumbs/");
define("FILES_ROOT_PATH", $_SERVER['DOCUMENT_ROOT'] . PATH_UPLOADS);
define("FILES_ROOT_PATH_THUMBS", $_SERVER['DOCUMENT_ROOT'] . PATH_UPLOADS_THUMBS);


/**
 * Seto se o php deve mostrar os erros
 */
if (DEV) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(null);
    ini_set('display_errors', 'Off');
}


/**
 * defino o charset do php
 */
ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=utf-8');


/**
 * seto a zona default
 */
date_default_timezone_set("Brazil/East");
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");


/**
 * Função que adiciona pastas ao caminho relativo
 *
 * @param string $path {caminho a ser adicionado}
 *
 * @return void
 * @access public
 */
function add_include_path($path)
{
    foreach (func_get_args() AS $path) {
        if (!file_exists($path)
            || (file_exists($path) && filetype($path) !== 'dir')
        ) {
            trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
            continue;
        }

        $paths = explode(PATH_SEPARATOR, get_include_path());

        if (array_search($path, $paths) === false) {
            array_push($paths, $path);
        }

        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}


add_include_path(PATH_DOC_ROOT . '/inc/');
add_include_path(PATH_DOC_ROOT . '/inc/class/');
add_include_path(PATH_DOC_ROOT . '/inc/i18n/');

/**
 * Inicia a sessão
 */
session_start();

require_once 'functions.inc.php';
require_once 'constants.inc.php';

/**
 * Gravo a conexão com o banco de dados da aplicação
 */
$REGISTRY = Registry::getInstance();
if (!$REGISTRY->exists('dbmysql')) {
    $REGISTRY->set('dbmysql', new Connection());
}
if (!$REGISTRY->exists('i18n')) {
    $REGISTRY->set('i18n', new I18n('pt_br'));
}
$i18n = $REGISTRY->get('i18n');

$PAGE_CODE = '';
?>