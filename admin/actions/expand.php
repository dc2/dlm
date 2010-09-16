<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = isset($params['item_id']) ? $params['item_id'] : false;
	if ($item_id === false) $this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$dbitem = $this->tree->GetItem($item_id);
	$children = ($dbitem['dl_right'] - $dbitem['dl_left'] - 1) / 2;

	if($children > 0) {
		$expand = abs($dbitem['expand'] - 1);
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand=? WHERE dl_id = ?';
		$this->db->Execute($query, array($expand, $item_id));

		if(!isset($params['ajax']) || $params['ajax'] != "true") {
			if($return === false) {
				$params = array();
				$this->Redirect($id, 'defaultadmin', '', $params);
			} else {
				$params = array('item_id' => $return[1]);
				$this->Redirect($id, $return[0], 0, $params);
			}
		} else {
			if($expand == 1) {
				$items = $this->GetTreeAdmin($item_id, $id, (int)$params['indent']);
				$this->smarty->assign('showrows', true);
				$this->smarty->assign_by_ref('items', $items);
			} else {
				$this->errors[] = ' ';
			}
		}
	} else $this->errors[] = $this->Lang('no_children');

	$this->AjaxResponse(count($items).';'.$this->ProcessTemplate('admin/rows.tpl'));
?>