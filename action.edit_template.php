<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM') || !$this->CheckPermission('Modify Templates')) exit;

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

	if(isset($params['ajax']) && $params['ajax'] === "true") {ob_start();}

	$tpl_name = isset($params['tpl_name']) ? urldecode($params['tpl_name']) : '';
	$old_name = isset($params['oldname']) ? $params['oldname'] : $tpl_name;

	if ((isset($params['submit']) || isset($params['temp'])) && trim($tpl_name) != '') {
		$tpl_overview = $params['tpl_overview'];
		$tpl_detail = $params['tpl_detail'];
		$tpl_content = $tpl_overview . TPL_SEPARATOR . $tpl_detail;

		if($tpl_name != $old_name && $oldname != '') {
			$query = 'SELECT COUNT(template_name) AS cnt FROM '.cms_db_prefix().'module_templates WHERE module_name = ? AND MD5(template_name) = ?';
			$result = $this->db->Execute($query, array('DLM', md5($tpl_name)));
			$row = $result->FetchRow();

			if($row['cnt'] == 0) {
				$query = 'UPDATE '.cms_db_prefix().'module_templates SET template_name = ?, content = ? WHERE module_name = ? AND MD5(template_name) = ?';
				$this->db->Execute($query, array($tpl_name, $tpl_content, 'DLM', md5($old_name)));
			} else {
				$this->errors[] = $this->Lang('error_dublicatename');
				$tpl_name = $old_name;
			}
		} else {
			$this->SetTemplate($tpl_name, $tpl_content);
		}

		if(isset($params['submit']) && count($this->errors) == 0 ) {
			if($return === false) {
				$params = array('tab_message' => 'template_updated', 'active_tab' => 'templates');
				$this->Redirect($id, 'defaultadmin', '', $params);
			} else {
				$params = array('tab_message' => 'template_updated', 'tpl_name' => $return[1]);
				$this->Redirect($id, $return[0], '', $params);
			}
			exit;
		}

		$this->AjaxResponse($this->Lang('template_updated'), false, 0);
	} elseif(trim($tpl_name) != ''){
		$tpl_content  = SplitTemplate($this->GetTemplate($tpl_name));
		$tpl_overview = $tpl_content[0];
		$tpl_detail   = $tpl_content[1];
	} else {
		$tpl_overview = $tpl_detail = '';
	}

	if(isset($params['tpl_import']) && $params['tpl_import'] == true) {
		$tpl_name = substr($tpl_name, 0, strlen($tpl_name) - 4);
	}

	$this->smarty->assign('startform', $this->CreateFormStart($id, 'edit_template', $returnid, 'post', '', false, '', array('active_tab' => 'templates')));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign('submit', $this->CreateFormEnd());

	$this->smarty->assign('name_text', $this->Lang('name'));
	$this->smarty->assign('name_value', html_entity_decode($tpl_name, ENT_QUOTES, 'UTF-8'));

	$this->smarty->assign('overview_text', $this->Lang('overview_tpl'));
	$this->smarty->assign('overview_value', htmlspecialchars($tpl_overview));

	$this->smarty->assign('detail_text', $this->Lang('detail_tpl'));
	$this->smarty->assign('detail_value', htmlspecialchars($tpl_detail));

	$this->smarty->assign('headline', $this->Lang('edit_template'));

	//$this->smarty->assign('ajax', $this->CreateInputHidden($id, 'ajax', 'false'));
	$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'oldname', $tpl_name) . ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	$this->smarty->assign('temp', $this->CreateInputSubmit($id, 'temp', $this->Lang('savetemp')));

	echo $this->DisplayErrors();
	echo $this->ProcessTemplate('admin/common.js.tpl');
	echo $this->ProcessTemplate('admin/ajax.tpl');
	echo $this->ProcessTemplate('admin/edit_template.tpl');
?>