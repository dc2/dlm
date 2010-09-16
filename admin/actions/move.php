<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$item_id = isset($params['item_id']) ? $params['item_id'] : false;
	if ($item_id === false) $this->Redirect($id, 'defaultadmin', $returnid);

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$direction = (isset($params['direction']) ? $params['direction'] : '');
	if ($direction != 'up' && $direction!='down') {
		$this->Redirect($id, 'defaultadmin', $returnid);
	}

	$node = $params['item_id'];
	$info = $this->tree->GetNodeInfo($node);

	switch($params['direction']) {
		case 'up':
			$query = 'SELECT dl_id FROM '.cms_db_prefix().'module_dlm_items WHERE dl_right = ?';
			$result = $this->db->Execute($query, array($info[0] - 1));	// $info[0] = left value of the element

			$newcat = $result->FetchRow();
			$newnode = $newcat['dl_id'];

			$this->tree->ChangePositionAll($node, $newnode, 'before');
			$this->SendEvent('ItemMoved', array('dl_item' => array('id' => $node)));
		break;
		case 'down':
			$query = 'SELECT dl_id FROM '.cms_db_prefix().'module_dlm_items WHERE dl_left = ?';
			$result = $this->db->Execute($query, array($info[1] + 1));	// $info[1] = right value of the element

			$newcat = $result->FetchRow();
			$newnode = $newcat['dl_id'];

			$this->tree->ChangePositionAll($node, $newnode, 'after');
			$this->SendEvent('ItemMoved', array('dl_item' => array('id' => $node)));
		break;
	}

	$this->AjaxResponse($this->Lang('item_moved'), false, true, $return);
?>