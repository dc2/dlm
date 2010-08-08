<?php
	if (!isset($gCms)) exit;	
	if (!$this->CheckPermission('Manage Downloads')) exit;
	
	if(isset($params['ajax']) && $params['ajax'] === "true") {
		@ob_end_clean();@ob_end_clean();ob_start();
	}
	
	$item_id = isset($params['item_id']) ? $params['item_id'] : false;
	if ($item_id === false) {
		$this->Redirect($id, 'defaultadmin', $returnid);
	}
	
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
	
	if(!isset($params['ajax']) || $params['ajax'] != "true") {
		if($return === false) {
			$params = array('tab_message' => 'item_moved', 'active_tab' => 'general');
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			$params = array('tab_message' => 'item_moved', 'item_id' => $return[1]);
			$this->Redirect($id, $return[0], 0, $params);
		}	
	}	
		
	if(isset($params['ajax']) && $params['ajax'] === "true") {
		ob_end_clean();
		if(count($this->errors) == 0) {
			echo "1,";
			echo $this->Lang('item_moved');
		} else {
			echo "0,";
			echo $this->DisplayErrors(true);
		}
		exit;
	}
	
	echo $this->DisplayErrors();
?>