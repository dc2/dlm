<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads') || !$this->CheckPermission('Modify Templates')) exit;
	
	print_r($params);
?>