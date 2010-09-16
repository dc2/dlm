<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = isset($params['item_id']) ? $params['item_id'] : false;
	if ($item_id === false)	$this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$dbitem = $this->tree->GetItem($item_id);

	if(isset($dbitem)) {
		$active = abs($dbitem['active'] - 1);
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET active=? WHERE dl_id = ? OR(dl_left > ? AND dl_right < ?)';
		$this->db->Execute($query, array($active, $item_id, $dbitem['dl_left'], $dbitem['dl_right']));

		$this->SendEvent('ItemActivated', array('dl_item' => array('id' => $key, 'name' => $dbitem['name'])));
	} else $this->errors[] = $this->Lang('error_noitem');

	$this->AjaxResponse($this->Lang('items_activated'), false, true, $return);
?>