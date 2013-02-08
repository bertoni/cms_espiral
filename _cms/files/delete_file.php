<?php
require $_SERVER['DOCUMENT_ROOT'] . '/_cms/inc/config_cms.inc.php';

$current_dir = $_GET['current_dir'];
$field = $_GET['field'];
$type = $_GET['type'];
$file = $_GET["file"];

$file_path = FILES_ROOT_PATH . '/' . $current_dir . '/' . $file;
$file_thumb_path = FILES_ROOT_PATH_THUMBS . '/' . $file;

if (file_exists($file_path)) {
	if (unlink($file_path)) {
		if (file_exists($file_thumb_path)) {
		    unlink($file_thumb_path);
		}
		//$log->insertLog(true, getIdUser(), 'files_del_file', 'Exclus�o do arquivo ' . $current_dir . '/' . $file);
		$msg = 'msg_ok=Arquivo excluído com sucesso. (' . $file . ')';
		header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
		exit;	
	} else {
		//$log->insertLog(true, getIdUser(), 'files_del_file', 'Erro na exclus�o do arquivo ' . $current_dir . '/' . $file);
		$msg = 'msg_error=Erro durante a exclusão do arquivo. (' . $file . ')';
		header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
		exit;
	}
} else {
	//$log->insertLog(true, getIdUser(), 'files_del_file', 'Erro na exclus�o. N�o existe o arquivo ' . $current_dir . '/' . $file);
	$msg = 'Arquivo não encontrado. (' . $file . ')';
	header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
	exit;
}
?>
