<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$dir_name = toCleanName($_POST['dir_name']);

$dest_dir = $_POST['dest_dir'];
$field = $_POST['field'];
$type = $_POST['type'];

if(empty($dir_name)){
	$msg = 'msg_error=Error. Directory not created.';
	header("Location:files.php?current_dir=$dest_dir&field=$field&type=$type&$msg");
}else{	
	if (empty($dest_dir)) {
		$dest_dir2create = FILES_ROOT_PATH;
	} else {
		### $dest_dir2create = FILES_ROOT_PATH . '/' . $dest_dir;
		$dest_dir2create = FILES_ROOT_PATH . $dest_dir;	
	}
	
	$abs_dir2create = $dest_dir2create . '/' . $dir_name;
	
	if (mkdir($abs_dir2create, 777)) {
		$msg = 'msg_ok=Directory successfully created.';
		shell_exec("chmod 775 ".$abs_dir2create);
		#$log->insertLog(true, getIdUser(), 'files_create_dir', 'Cria��o do diret�rio ' . $dir_name);
	} else {
		$msg = 'msg_error=Error. Directory not created. (' . $abs_dir2create . ')';
		#$log->insertLog(true, getIdUser(), 'files_create_dir', 'Erro na cria��o do diret�rio ' . $dir_name);
	}
	header("Location:files.php?current_dir=$dest_dir&field=$field&type=$type&$msg");
}
?>
