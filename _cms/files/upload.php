<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$dest_dir = $_POST['dest_dir'];
$field = $_POST['field'];
$type = $_POST['type'];

$file = $_FILES['file'];

if (empty($dest_dir)) {
	$dest_dir2save = FILES_ROOT_PATH;
} else {
	$dest_dir2save = FILES_ROOT_PATH . '' . $dest_dir;
}

$file_name = toCleanFileName(utf8_decode($file['name']));

if (move_uploaded_file($file["tmp_name"], $dest_dir2save . '/' . str_replace('jpeg','jpg',$file_name))) {
	#$log->insertLog(true, getIdUser(), 'files_upload_file', 'Upload do arquivo ' . $file_name);
} else {
	#$log->insertLog(true, getIdUser(), 'files_upload_file', 'Erro no upload do arquivo ' . $file_name);
}

echo '{"name":"'.$file['name'].'","type":"'.$file['type'].'","size":"'.$file['size'].'"}';
?>