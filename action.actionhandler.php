<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;
	
	if(isset($params['ajax']) && $params['ajax'] === "true") {
		@ob_end_clean();@ob_end_clean();ob_start();
	}
	
	switch($params['haction']) {
		case 'activate': 
			include('admin/actions/activate.php');
		break;
		
		case 'move':
			include('admin/actions/move.php');
		break;
		
		case 'expand':
			$this->theme =& $gCms->variables['admintheme'];
			include('admin/actions/expand.php');
		break;
		
		case 'expandall':
			$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand=1';
			$this->db->Execute($query);
			$this->Redirect($id, 'defaultadmin', '');
		break;
		
		case 'contractall':
			$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand=0';
			$this->db->Execute($query);
			$this->Redirect($id, 'defaultadmin', '');
		break;
		
		case 'delete':
			include('admin/actions/delete.php');
		break;
	}
?>