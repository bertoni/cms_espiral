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

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/config.inc.php';

add_include_path(PATH_CMS_DOC_ROOT . '/inc/');

if ($_SERVER['SCRIPT_NAME'] != '/_cms/index.php') {
    include 'check_session.inc.php';
}

$FOLDERS_HIDE = array('thumbs');

$IMAGE_CUT_SIZES = array(
    array(
	'text'=>'Recortar imagem',
        'x'=>'0',
        'y'=>'0'
    ),
	array(
	'text'=>'Thumb imagens: 40x40',
        'x'=>'40',
        'y'=>'40'
    ),
    array(
	'text'=>'Destaque rotativo home: 405x414',
        'x'=>'405',
        'y'=>'414'
    )
);

$page_menu = "";
$page_code = "";

$FOLDERS_HIDE = array('thumbs');
?>
