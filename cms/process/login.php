<?php
	include $_SERVER['DOCUMENT_ROOT'].'config/init.php';
	debugger($_POST);
	if ($_POST){

	}else{
		redirect('../login','error','Unauthorized access..');
	}

?>