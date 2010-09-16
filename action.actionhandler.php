<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	if(isset($params['ajax']) && $params['ajax'] === "true") {
		@ob_end_clean();@ob_end_clean();ob_start();
	}

	switch($params['_action']) {
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
		case 'contractall':
			$item = false;
			$level = 0;
			$expand = ($params['_action'] == 'contractall') ? 0 : 1;

			if(isset($params['item'])) {
				$item = (int)$params['item'];
				$info = $this->tree->GetNodeInfo($item);
				$left = $info[0];
				$right = $info[1];
				$level = $info[2];
			}

			if($item === false) {
				$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand = ?';
				$this->db->Execute($query, array($expand));
			} else {
				$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand = ? WHERE dl_id =  ? OR (dl_left > ? AND dl_right < ?)';
				$this->db->Execute($query, array($expand, $item, $left, $right));
			}

			if(!$params['ajax']) {
				$this->Redirect($id, 'defaultadmin', '');
			} else {
				$this->theme =& $gCms->variables['admintheme'];
				$this->smarty->assign('showrows', true);
				$this->smarty->assign('items', $this->GetTreeAdmin((int)$item, $id, $level));
				echo $this->ProcessTemplate('admin/rows.tpl');
				exit;
			}
		break;

		case 'delete':
			include('admin/actions/delete.php');
		break;

		case 'delete_template':
			include('admin/actions/delete_template.php');
		break;

		case 'set_default_template':
			include('admin/actions/set_default_template.php');
		break;

		default:
			$this->Redirect($id, 'defaultadmin');
		break;
	}
?>