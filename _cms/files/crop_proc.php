<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';
require '../inc/phmagick/phmagick.php';

error_reporting(1);

$current_dir = $_POST['current_dir'];
$file_name = $_POST['file_name'];
$field = $_POST['field'];
$type = $_POST['type'];
$x = (int) $_POST['x'];
$y = (int) $_POST['y'];

$width = (int) $_POST['width'];
$height = (int) $_POST['height'];
$x1 = (int) $_POST['x1'];
$y1 = (int) $_POST['y1'];
$x2 = (int) $_POST['x2'];
$y2 = (int) $_POST['y2'];

$file_functions = new FilesFunctions();


$image_path = FILES_ROOT_PATH . $current_dir . '/' . $file_name;
$ext = $file_functions->getFileExt($file_name);
$new_image_path = str_replace('.' . $ext, '', $image_path) . '_' . $x . 'x' . $y . '.' . $ext;


$cc =& new CropCanvas(true);
#$cc->setDebugging();
$cc->loadImage($image_path);
$cc->cropToDimensions($x1, $y1, $x2, $y2);
if ($cc->saveImage($new_image_path, 70)) {

    if ($x > 0 && $y > 0) {
        //$tb = new Imagethumb($new_image_path);
        //$tb->getThumb($new_image_path, $x, $y);

        $im = new phMagick($new_image_path,$new_image_path);
        $im->resize($x, $y);
        $im->setDestination($new_image_path)->resize($x,$y,true);
    }

    $msg = 'msg_ok=Imagem recortada com sucesso&file_name=' . getFileNameInPath($new_image_path);
} else {
    $msg = 'msg_error=Ocorreu um erro ao recortar a imagem&file_name=' . getFileNameInPath($new_image_path);
}
header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
exit;
?>