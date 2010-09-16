<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM') || !$this->CheckPermission('Modify Templates')) exit;

	$this->DeleteTemplate(urldecode($params['tpl_name']));

	if(!isset($params['ajax']) || $params['ajax'] != "true") {
		if($return === false) {
			if(count($this->errors > 0)) {
				$params = array('tab_message' => 'template_deleted', 'active_tab' => 'templates');
			} else {
				$params = array('tab_message' => 'error_delete', 'active_tab' => 'templates');
			}
			$this->Redirect($id, 'defaultadmin', '', $params);
		}
	}

	$this->AjaxResponse('');
?>