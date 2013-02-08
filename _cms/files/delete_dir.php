<?php
require '../inc/check_session.inc.php';

$current_dir = $_GET['current_dir'];
$field = $_GET['field'];
$type = $_GET['type'];
$path = $_GET["path"];

$dest_dir = FILES_ROOT_PATH . '/' . $path;

if (file_exists($dest_dir)) {
	if (rmdir($dest_dir)) {
		#$log->insertLog(true, getIdUser(), 'files_del_dir', 'Exclus�o do diret�rio ' . $path);
		$msg = 'msg_ok=Diretório excluído com sucesso. (' . $path . ')';
		header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
		exit;
	} else {
		#$log->insertLog(true, getIdUser(), 'files_del_dir', 'Erro na exclus�o do diret�rio ' . $path);
		$msg = 'msg_error=Erro durante a exclus�o do diret�rio. Verifique se o diret�rio est� vazio. (' . $path . ')';
		header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
		exit;
	}
} else {
	#$log->insertLog(true, getIdUser(), 'files_del_dir', 'Erro na exclus�o. N�o existe o diret�rio ' . $path);
	$msg = 'Diret�rio n�o encontrado. (' . $path . ')';
	header("Location:files.php?current_dir=$current_dir&field=$field&type=$type&$msg");
	exit;
}

?>
