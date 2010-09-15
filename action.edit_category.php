<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$this->theme =& $gCms->variables['admintheme'];

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	if (isset($params['cancel'])) {
		if($return === false) {
			$params = array();
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			$params = array('tab_message' => 'category_edited', 'item_id' => $return[1]);
			$this->Redirect($id, $return[0], '', $params);
		}
	}

	$item_id = (isset($params['item_id']) ? $params['item_id'] : '');

	if ($item_id == '' || $item_id === '0') {
		$this->Redirect($id, 'defaultadmin', $returnid);
	}

	$dbtree = $this->tree->GetItemsDB($item_id);
	$dbitem = reset($dbtree);


	if(isset($params['ajax']) && $params['ajax'] === "true") {ob_start();}

	if($dbitem != false) {
		if($dbitem['type'] == 0) {
			$item_name = $dbitem['name'];
			$item_desc = $dbitem['description'];
			$item_parent = $dbitem['parent'];

			$item_name = (isset($params['item_name']) ? $params['item_name'] : $item_name);
			$item_desc = (isset($params['item_desc']) ? $params['item_desc'] : $item_desc);
			$item_parent = (isset($params['item_parent']) ? $params['item_parent'] : $item_parent);

			if (isset($params['submit']) || isset($params['temp'])) {
				if ($item_name != '') {
					if(($oldparent = $dbitem['parent']) != $item_parent) {
						$this->MoveNode($item_id, $item_parent, $oldparent);
					}

					$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET name=?, description=?, parent=?, type=? WHERE dl_id = ?';
					$this->db->Execute($query, array($item_name, $item_desc, $item_parent, 0, $item_id));

					$this->Audit($item_id, $item_name, 'DLM: Category edited');
					$this->SendEvent('CategoryEdited', array('dl_item' => array('id' => $item_id, 'name' => $item_name)));
				} else {
					$this->errors[] = $this->Lang('error_nocategoryname');
				}

				if(isset($params['submit'])) {
					if($return === false) {
						$params = array('tab_message' => 'category_updated', 'active_tab' => 'general');
						$this->Redirect($id, 'defaultadmin', '', $params);
					} else {
						$params = array('tab_message' => 'category_updated', 'item_id' => $return[1]);
						$this->Redirect($id, $return[0], '', $params);
					}
				} else {
					echo $this->ShowMessage($this->Lang('category_updated'));
				}
			}

			if(isset($params['ajax']) && $params['ajax'] === "true") {
				$content = ob_get_contents();ob_end_clean();
				if(count($this->errors) == 0) {
					echo "1,";
					echo $this->Lang('category_updated');
				} else {
					echo "0,";
					echo $this->DisplayErrors(true);
				}
				exit;
			}



			// form
			$this->smarty->assign('headline', $this->Lang('edit_category'));
			$this->smarty->assign('path_text', $this->Lang('path_text'));
			$this->smarty->assign('path', $this->GetPath($item_id, $id, $returnid, 1, false, "edit_category,$item_id"));

			$this->smarty->assign('name_text', $this->Lang('name'));
			$this->smarty->assign('name_value', htmlspecialchars($item_name));

			$this->smarty->assign('parent_text', $this->Lang('parent_category'));
			$this->smarty->assign('parent_input', $this->CreateInputDropdown($id, 'item_parent', $this->GetTreeInput(0, $item_id), $item_parent));

			$this->smarty->assign('desc_text', $this->Lang('desc'));
			$this->smarty->assign('desc_value', $item_desc);

			$this->smarty->assign('ajax', $this->CreateInputHidden($id, 'ajax', 'false'));

			$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
			$this->smarty->assign('temp', $this->CreateInputSubmit($id, 'temp', 	$this->Lang('savetemp')));
			$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

			//$this->smarty->assign('view', $this->CreateLink('m03794', 'default', 247, $this->theme->DisplayImage('icons/system/view.gif', $this->Lang('view'),'','','systemicon'), array('item' => $item_id), '', false, true, '', false));

			// retrieve the tree of children
			$items = $this->GetTreeAdmin($item_id, $id, $dbitem['dl_level'], $dbtree, "edit_category,$item_id");

			$this->smarty->assign('rootlevel', $dbitem['dl_level']);

			if(($itemcount = count($items)) > 0) {
				$this->smarty->assign_by_ref('items', $items);
				$this->smarty->assign('itemcount', $itemcount);
				$this->smarty->assign('showlist');

				$this->smarty->assign('node_children', $this->Lang('node_children'));
				$this->smarty->assign('th_name', $this->Lang('th_name'));
				$this->smarty->assign('th_type', $this->Lang('th_type'));
				$this->smarty->assign('th_id', $this->Lang('th_id'));
				$this->smarty->assign('th_active', $this->Lang('th_active'));
				$this->smarty->assign('th_reorder', $this->Lang('th_reorder'));
				$this->smarty->assign('th_actions', $this->Lang('th_actions'));

				$this->smarty->assign('areyousure_item', $this->Lang('areyousure_item'));
				$this->smarty->assign('areyousure_items', $this->Lang('areyousure_items'));

				$this->smarty->assign('unselect_children', $this->Lang('unselect_children'));
				$this->smarty->assign('all', $this->Lang('all'));
				$this->smarty->assign('selected', $this->Lang('selected'));
				$this->smarty->assign('reverse_selection', $this->Lang('reverse_selection'));

				$this->smarty->assign('delete', $this->Lang('delete'));
				$this->smarty->assign('move', $this->Lang('move'));
				$this->smarty->assign('activate', $this->Lang('activate'));
				$this->smarty->assign('deactivate', $this->Lang('deactivate'));
				$this->smarty->assign('suborder', $this->Lang('suborder'));

				$this->ProcessTemplate('admin/rows.tpl');
				$this->ProcessTemplate('admin/itemlist.tpl');
			}

			$this->smarty->assign('expandall', $this->CreateHandlerLink($id, 'expandall', $returnid, DisplayImage('expandall.png', $this->Lang('expandall'), $this->Lang('expandall')).$this->Lang('expandall'), array('item' => $item_id), '', false, false, 'id="expandall"'));
			$this->smarty->assign('contractall', $this->CreateHandlerLink($id, 'contractall', $returnid, DisplayImage('contractall.png', $this->Lang('contractall'), $this->Lang('contractall')).$this->Lang('contractall'), array('item' => $item_id), '', false, false, 'id="contractall"'));

			$this->smarty->assign('add_category', $this->CreateLink($id, 'add_category', $returnid, DisplayImage('category_new.png', $this->Lang('category'), $this->Lang('category')).' '.$this->Lang('add_category'), array("return" => "edit_category,$item_id"), '', false, false, ''));
			$this->smarty->assign('add_download', $this->CreateLink($id, 'add_download', $returnid, DisplayImage('download_new.png', $this->Lang('download'), $this->Lang('download')).' '.$this->Lang('add_download'), array("return" => "edit_category,$item_id"), '', false, false, ''));

			$this->smarty->assign('toggle', $this->Lang('toggle'));

			$this->smarty->assign('startform', $this->CreateFormStart($id, 'edit_category', $returnid));
			$this->smarty->assign('endform', $this->CreateFormEnd());

			$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'item_id', $item_id) . ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

			$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));

			echo $this->DisplayErrors();


			echo $this->ProcessTemplate('admin/common.js.tpl');
			echo $this->ProcessTemplate('admin/ajax.tpl');
			echo $this->ProcessTemplate('admin/edit_category.tpl');

			if($return !== false) echo $this->CreateInputHidden($id, 'return', implode(',', $return));

		} else {
			$params = array('active_tab' => 'general', 'tab_message' => 'error_nocategory');
			$this->Redirect($id, 'defaultadmin', '', $params);
		}
	} else {
		$this->Redirect($id, 'defaultadmin', $returnid);
	}
?>