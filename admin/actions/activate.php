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

		if(!isset($params['ajax']) || $params['ajax'] != "true") {
			if($return === false) {
				$params = array();
				$this->Redirect($id, 'defaultadmin', '', $params);
			} else {
				$params = array('item_id' => $return[1]);
				$this->Redirect($id, $return[0], 0, $params);
			}
		} elseif($active == 0) $this->errors[] = ' ';

	} else $this->errors[] = $this->Lang('error_noitem');

	$this->AjaxResponse($this->Lang('items_activated'));
?>