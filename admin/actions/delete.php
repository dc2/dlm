<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

	$item_id = (isset($params['item_id']) ? $params['item_id'] : false);

	if ($item_id == false) $this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$dbitem = $this->tree->GetItem($item_id);

	if(isset($dbitem)) {
		if($this->tree->DeleteBranch($item_id)) {
			$tab_message = 'item_deleted';
			$this->Audit($item_id, $dbitem['name'], 'DlM: ' . ($dbitem['type'] == 0 ? 'Category' : 'Download').' deleted');
		} else {
			$this->errors[] = $this->Lang('error_item_delete');
		}
	} else $this->errors[] = $this->Lang('error_noitem');

	if(!isset($params['ajax']) || $params['ajax'] != "true") {
		if($return === false) {
			if(count($this->errors > 0)) {
				$params = array('tab_message' => $tab_message, 'active_tab' => 'general');
			} else {
				$params = array('tab_message' => 'error_delete', 'active_tab' => 'general');
			}
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			if(count($this->errors) == 0) {
				$params = array('tab_message' => $tab_message, 'item_id' => $return[1]);
			} else {
				$params = array('tab_message' => 'error_delete', 'item_id' => $return[1]);
			}
			$this->Redirect($id, $return[0], '', $params);
		}
	}

	if(isset($params['ajax']) && $params['ajax'] === "true") {
		ob_end_clean();
		if(count($this->errors) == 0) {
			echo "1,";
			echo $this->Lang($tab_message);
		} else {
			echo "0,";
			echo $this->DisplayErrors(true);
		}
		exit;
	}
?>
