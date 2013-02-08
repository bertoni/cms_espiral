<?php
$field = '';
if (isset($_GET['field'])) {
    $field = $_GET['field'];
}
$type = '';
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}
$msg_ok = '';
if (! empty($_GET['msg_ok'])) {
	$msg_ok = $_GET['msg_ok'];
}
$msg_error = '';
if (! empty($_GET['msg_error'])) {
	$msg_error = $_GET['msg_error'];
}
$current_dir = '';
if (! empty($_GET['current_dir'])) {
	$current_dir = $_GET['current_dir'];
}
$param_file_name = '';
if (! empty($_GET['file_name'])) {
	$param_file_name = $_GET['file_name'];
}

$is_hidden_current_dir = true;
if (! in_array($current_dir, $FOLDERS_HIDE)) {
    $is_hidden_current_dir = false;
}

$files_functions = new FilesFunctions();
$files_functions->setDirRelPath($current_dir);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="pt-BR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="robots" content="none" />
<meta name="googlebot" content="noarchive" />
<title><?= SYSTEM_NAME ?> - Arquivos</title>
<script type="text/javascript" src="lib.js"></script>
<link rel="stylesheet" type="text/css" href="style.css" />

<link rel="stylesheet" href="fileupload/jquery-ui.css" id="theme" />
<link rel="stylesheet" href="fileupload/jquery.fileupload-ui.css" />
<link rel="stylesheet" href="/js/mylibs/jcrop/jquery.Jcrop.min.css" type="text/css" />

</head>
<body>
	<h1>Gerenciamento de arquivos</h1>
	<?php
	if (! empty($msg_ok)) {
		?><div id="msg_ok"><?= $msg_ok ?></div><?php
	}
	if (! empty($msg_error)) {
		?><div id="msg_error"><?= $msg_error ?></div><?php
	}
	?>