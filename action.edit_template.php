<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

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

	$tpl_name = urldecode($params['tpl_name']);

	// update / save edit
	if (isset($params['submit']) || isset($params['temp'])) {
		$tpl_content = $params['tpl_content'];

		$this->SetTemplate($tpl_name, $tpl_content);
		#CMSModule::SetTemplate()
		#$this->Audit($item_id, $item_name, 'DlM: Template edited');
		#$this->SendEvent('TemplateEdited', array('dl_item' => array('id' => $item_id, 'name' => $item_name)));

		if(isset($params['submit'])) {
			if($return === false) {
				$params = array('tab_message' => 'template_updated', 'active_tab' => 'templates');
				$this->Redirect($id, 'defaultadmin', '', $params);
			} else {
				$params = array('tab_message' => 'template_updated', 'tpl_name' => $return[1]);
				$this->Redirect($id, $return[0], '', $params);
			}
			exit;
		}

		if(isset($params['ajax']) && $params['ajax'] === "true") {
			$content = ob_get_contents();ob_end_clean();
			if(count($this->errors) == 0) {
				echo "1,";
				echo $this->Lang('template_updated');
			} else {
				echo "0,";
				echo $this->DisplayErrors(true);
			}
			exit;
		}
	}

	$tpl_content = $this->GetTemplate($tpl_name);

	$this->smarty->assign('startform', $this->CreateFormStart($id, 'edit_template', $returnid, 'post', '', false, '', array('active_tab' => 'templates')));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign('submit', $this->CreateFormEnd());

	$this->smarty->assign('name_text', $this->Lang('name'));
	$this->smarty->assign('name_value', html_entity_decode($tpl_name, ENT_QUOTES, 'UTF-8'));

	$this->smarty->assign('content_text', $this->Lang('content'));
	$this->smarty->assign('content_value', htmlspecialchars($tpl_content));

	$this->smarty->assign('headline', $this->Lang('edit_template'));

	$this->smarty->assign('ajax', $this->CreateInputHidden($id, 'ajax', 'false'));
	$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'oldname', $tpl_name) . ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	$this->smarty->assign('temp', $this->CreateInputSubmit($id, 'temp', $this->Lang('savetemp')));

	echo $this->DisplayErrors();
	echo $this->ProcessTemplate('admin/common.js.tpl');
	echo $this->ProcessTemplate('admin/ajax.tpl');
	echo $this->ProcessTemplate('admin/edit_template.tpl');

?>
