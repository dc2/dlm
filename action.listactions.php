<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	if(isset($params['ajax']) && $params['ajax'] == "true") {
		@ob_end_clean();@ob_end_clean();ob_start();
	}

	$items = $params['listitems'];
	$error = false;

	if(isset($items)) {
		switch((int)$params['listaction']) {
			case 0:	// delete
				foreach($items as $key => $value) {
					$this->Audit($key, '', 'DLM: ' . 'Item (batch) deleted');
					$this->SendEvent('ItemDeleted', array('dl_item' => array('id' => $key)));
					if(!$this->DeleteBranch($key))
						$error = true;
				}

				if($error)
					$this->errors[] = $this->Lang('error_item_delete');
				else
					$tab_message = 'items_deleted';
			break;

			case 1: case 2: // (de)activate
				$active = (int)$params['listaction'] == 1 ? '1' : '0';
				$in = '';
				$or = '';

				foreach($items as $key => $value) {
					$this->SendEvent('ItemActivated', array('dl_item' => array('id' => $key)));

					$in .= (int)$key. ',';
					$dbtree = $this->tree->GetItemsDB((int)$key, array('dl_id', 'dl_left', 'dl_right'));

					foreach($dbtree as $item) {
						$or .= ' OR (dl_left > '.$item['dl_left'].' AND dl_right < '.$item['dl_right'].') ';
					}
				}

				$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET active=? WHERE dl_id IN ('.substr($in, 0, strlen($in)-1).') ' . $or;
				$this->db->Execute($query, array($active));

				$tab_message = 'items_activated';
			break;
		}
	}

	$this->AjaxResponse($this->Lang($tab_message), false, 0, $return);
?>