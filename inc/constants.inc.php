<?php
/**
 * Arquivo de constantes
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
 * Define os tipos de campos html possíveis
 */
$TYPES_HTML = array(
    'input[text]',
    'input[checkbox]',
    'input[radio]',
    'input[hidden]',
    'input[password]',
    'input[file]',
    'select',
    'textarea'
);
global $TYPES_HTML;

$TYPES_COMPARISON = array(
    'like'=>'Like %...',
    'likelike'=>'Like %...%',
    'equal'=>'='
);
global $TYPES_COMPARISON;

$ARR_HOURS = array();
for ($i = 0; $i < 24; $i++) {
	array_push($ARR_HOURS, str_pad($i, 2, "0", STR_PAD_LEFT));
}
global $ARR_HOURS;

$ARR_MINUTES = array();
for ($i = 0; $i < 60; $i+=15) {
	array_push($ARR_MINUTES, str_pad($i, 2, "0", STR_PAD_LEFT));
}
global $ARR_MINUTES;


$POST_MAX_SIZE = ini_get('post_max_size');
$mul = substr($POST_MAX_SIZE, -1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$POST_MAX_SIZE_BYTES = (int) $POST_MAX_SIZE * $mul;


$EXTENSIONS_ALLOWED = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'xls', 'txt', 'ppt', 'flv', 'mp3');
$EXTENSIONS_IMAGES = array($EXTENSIONS_ALLOWED[0], $EXTENSIONS_ALLOWED[1], $EXTENSIONS_ALLOWED[2], $EXTENSIONS_ALLOWED[3]);
$EXTENSIONS_VIDEOS = array($EXTENSIONS_ALLOWED[9]);
$EXTENSIONS_DOCS = array($EXTENSIONS_ALLOWED[4], $EXTENSIONS_ALLOWED[5], $EXTENSIONS_ALLOWED[6], $EXTENSIONS_ALLOWED[7], $EXTENSIONS_ALLOWED[8], $EXTENSIONS_ALLOWED[10]);

?>