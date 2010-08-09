<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;
	
	$return = !empty($params['return']) ? explode(',', $params['return']) : false;
	$item_parent = (int) $return[1];
	
	if (isset($params['cancel'])) {
		if($return === false) {
			$params = array();
			$this->Redirect($id, 'defaultadmin', '', $params);
		} else {
			$params = array('tab_message' => 'category_added', 'item_id' => $return[1]);
			$this->Redirect($id, $return[0], '', $params);
		}
	}
	
	$item_name   = (isset($params['item_name']) ? $params['item_name'] : '');
	$item_desc   = (isset($params['item_desc']) ? $params['item_desc'] : '');
	$item_parent = (isset($params['item_parent']) ? $params['item_parent'] : $item_parent);
	
	if (isset($params['submit'])) {
		if ($item_name != "") {	
			$node = $this->tree->InsertNode($item_parent, array('name' => $item_name, 'description' => $item_desc, 'type' => 0));
			if($node !== false) {
				$this->Audit($node, $item_name, 'DlM: Category added');
				$this->SendEvent('CategoryAdded', array('dl_item' => array('id' => $node, 'name' => $item_name)));
				
				if($return === false) {
					$params = array('tab_message' => 'category_added', 'active_tab' => 'general');
					$this->Redirect($id, 'defaultadmin', '', $params);
				} else {
					$params = array('tab_message' => 'category_added', 'item_id' => $return[1]);
					$this->Redirect($id, $return[0], '', $params);
				}
			} else {
				$this->errors[] = $this->Lang('error_dbinsert');	
			}

		} else {
			$this->errors[] = $this->Lang('error_nocategoryname');	
		}
	}
	
	$this->smarty->assign('headline', $this->Lang('add_category'));
	$this->smarty->assign('path_text', $this->Lang('path_text'));
	$this->smarty->assign('path', $this->GetPath($item_parent, $id, $returnid, 1, $this->Lang('add_category'), isset($params['return']) ? $params['return'] : ''));
	
	$this->smarty->assign('name_text', $this->Lang('name'));
	$this->smarty->assign('name_value', $item_name);
	
	$this->smarty->assign('parent_text', $this->Lang('parent_category'));
	$this->smarty->assign('parent_input', $this->CreateInputDropdown($id, 'item_parent', $this->GetTreeInput(0), $item_parent));
	
	$this->smarty->assign('desc_text', $this->Lang('desc'));
	$this->smarty->assign('desc_value', $item_desc);
	
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	
	echo $this->DisplayErrors();
	
	$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));
	
	echo $this->CreateFormStart($id, 'add_category', $returnid);
	
	echo $this->ProcessTemplate('admin/common.js.tpl');
	echo $this->ProcessTemplate('admin/ajax.tpl');
	echo $this->ProcessTemplate('admin/edit_category.tpl');
	
	if($return !== false) echo $this->CreateInputHidden($id, 'return', implode(',', $return));
	echo $this->CreateFormEnd();
?>