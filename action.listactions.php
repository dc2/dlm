<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	if(isset($params['ajax']) && $params['ajax'] === "true") {
		@ob_end_clean();@ob_end_clean();ob_start();
	}

	$items = $params['listitems'];
	$error = false;

	if(isset($items)) {
		switch((int)$params['listaction']) {
			case 0:	// delete
				foreach($items as $key => $value) {
					$this->Audit($key, $dbitem['name'], 'DlM: ' . 'Item (batch) deleted');
					$this->SendEvent('ItemDeleted', array('dl_item' => array('id' => $item_id, 'name' => $item_name)));
					if(!$this->tree->DeleteBranch($key))
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

					foreach($dbtree as $dbitem) {
						$or .= ' OR (dl_left > '.$dbitem['dl_left'].' AND dl_right < '.$dbitem['dl_right'].') ';
					}
				}

				$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET active=? WHERE dl_id IN ('.substr($in, 0, strlen($in)-1).') ' . $or;
				$this->db->Execute($query, array($active));

				$tab_message = 'items_activated';
			break;
		}
	}

	if(!isset($params['ajax']) || $params['ajax'] != "true") {
		if($return === false) {
			if(count($this->errors > 0)) {
				$params = array('tab_message' => $tab_message, 'active_tab' => 'general');
			} else {
				$params = array('tab_message' => 'error', 'active_tab' => 'general');
			}
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			if(!$error) {
				$params = array('tab_message' => $tab_message, 'item_id' => $return[1]);
			} else {
				$params = array('tab_message' => 'error', 'item_id' => $return[1]);
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