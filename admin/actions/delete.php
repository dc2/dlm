<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = (isset($params['item_id']) ? $params['item_id'] : false);

	if ($item_id == false) $this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$item = $this->tree->GetItem($item_id);

	if(isset($item)) {
		if($this->DeleteBranch($item_id)) {
			$tab_message = 'item_deleted';
			$this->Audit($item_id, $item['name'], 'DLM: ' . ($item['type'] == 0 ? 'Category' : 'Download').' deleted');
		} else {
			$this->errors[] = $this->Lang('error_item_delete');
		}
	} else $this->errors[] = $this->Lang('error_noitem');

	$this->AjaxResponse($this->Lang($tab_message), false, 0, $return);
?>