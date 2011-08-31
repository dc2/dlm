<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = isset($params['item_id']) ? $params['item_id'] : false;
	if ($item_id === false) $this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$item = $this->tree->GetItem($item_id);
	$children = ($item['dl_right'] - $item['dl_left'] - 1) / 2;

	if($children > 0) {
		$expand = abs($item['expand'] - 1);
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET expand=? WHERE dl_id = ?';
		$this->db->Execute($query, array($expand, $item_id));

		if($expand == 1) {
			$this->smarty->assign('showrows', true);
			$this->smarty->assign('items', $this->GetTreeAdmin($item_id, $id, (int)$params['indent']));
		} else {
			$this->errors[] = ' ';
		}
	} else $this->errors[] = $this->Lang('no_children');

	$this->AjaxResponse(count($items).';'.$this->ProcessTemplate('admin/rows.tpl'), false, true, $return);
?>