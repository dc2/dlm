<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

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


	if(isset($params['ajax']) && $params['ajax'] === "true") {
		ob_start();
	}

	if($dbitem != false) {
		if($dbitem['type'] == 0) {
			// set all the params for update
			$item_name = $dbitem['name'];
			$item_desc = $dbitem['description'];
			$item_parent = $dbitem['parent'];

			$item_name = (isset($params['item_name']) ? $params['item_name'] : $item_name);
			$item_desc = (isset($params['item_desc']) ? $params['item_desc'] : $item_desc);
			$item_parent = (isset($params['item_parent']) ? $params['item_parent'] : $item_parent);

			// update / save edit
			if (isset($params['submit']) || isset($params['temp'])) {
				if ($item_name != '') {
					if(($oldparent = $dbitem['parent']) != $item_parent) {
						$this->tree->MoveNode($item_id, $item_parent, $oldparent);
					}

					$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET name=?, description=?, parent=?, type=? WHERE dl_id = ?';
					$this->db->Execute($query, array($item_name, $item_desc, $item_parent, 0, $item_id));

					$this->Audit($item_id, $item_name, 'DlM: Category edited');
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

			echo $this->DisplayErrors();

			$this->smarty->assign('add_category', $this->CreateLink($id, 'add_category', $returnid, DisplayImage('category_new.png', $this->Lang('category'), $this->Lang('category')), array("return" => "edit_category,$item_id"), '', false, false, '') .' '. $this->CreateLink($id, 'add_category', $returnid, $this->Lang('add_category'), array("return" => "edit_category,$item_id"), '', false, false, 'class="pageoptions"'));
			$this->smarty->assign('add_download', $this->CreateLink($id, 'add_download', $returnid, DisplayImage('download_new.png', $this->Lang('download'), $this->Lang('download')), array("return" => "edit_category,$item_id"), '', false, false, '') .' '. $this->CreateLink($id, 'add_download', $returnid, $this->Lang('add_download'), array("return" => "edit_category,$item_id"), '', false, false, 'class="pageoptions"'));

			$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));

			// form
			$this->smarty->assign('headline', $this->Lang('edit_category'));
			$this->smarty->assign('path_text', $this->Lang('path_text'));
			$this->smarty->assign('path', $this->GetPath($item_id, $id, $returnid, 1, false, "edit_category,$item_id"));

			$this->smarty->assign('name_text', $this->Lang('name'));
			$this->smarty->assign('name_value', html_entity_decode($item_name, ENT_QUOTES, 'UTF-8'));

			$this->smarty->assign('parent_text', $this->Lang('parent_category'));
			$this->smarty->assign('parent_input', $this->CreateInputDropdown($id, 'item_parent', $this->GetTreeInput(0, $item_id), $item_parent));

			$this->smarty->assign('desc_text', $this->Lang('desc'));
			$this->smarty->assign('desc_value', $item_desc);

			$this->smarty->assign('ajax', $this->CreateInputHidden($id, 'ajax', 'false'));

			$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
			$this->smarty->assign('temp', $this->CreateInputSubmit($id, 'temp', 	$this->Lang('savetemp')));
			$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

			//$this->smarty->assign('view', $this->CreateLink('m03794', 'default', 247, $this->theme->DisplayImage('icons/system/view.gif', $this->Lang('view'),'','','systemicon'), array('item' => $item_id), '', false, true, '', false));

			// now retrieve the tree of children
			$items = $this->GetTreeAdmin($item_id, $id, $dbitem['dl_level'], &$dbtree, "edit_category,$item_id");

			$this->smarty->assign('rootlevel', $dbitem['dl_level']);

			if(($itemcount = count($items)) > 0) {
				$this->smarty->assign_by_ref('items', $items);
				$this->smarty->assign('itemcount', $itemcount);
				$this->smarty->assign('showlist');

				$this->smarty->assign('node_children', $this->Lang('node_children'));
				$this->smarty->assign('th_name', $this->Lang('th_name'));
				#$this->smarty->assign('th_template', $this->Lang('template'));
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

			$this->smarty->assign('toggle', $this->Lang('toggle'));

			$this->smarty->assign('formstart', $this->CreateFormStart($id, 'edit_category', $returnid));
			$this->smarty->assign('formend', $this->CreateFormEnd());

			$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'item_id', $item_id) . ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

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