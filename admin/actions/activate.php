<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = isset($params['item_id']) ? (int)$params['item_id'] : false;
	if ($item_id === false)	$this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$item = $this->tree->GetItem($item_id);
	
	$active = false;
	
	if(isset($item)) {
		$active = abs($item['active'] - 1);
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET active=? WHERE dl_id = ? OR(dl_left > ? AND dl_right < ?)';
		$this->db->Execute($query, array($active, $item_id, $item['dl_left'], $item['dl_right']));

		$this->SendEvent('ItemActivated', array('dl_item' => array('id' => $key, 'name' => $item['name'])));
	} else $this->errors[] = $this->Lang('error_noitem');

	$this->AjaxResponse($this->Lang('items_activated'), !(bool)$active, true, $return);
?>