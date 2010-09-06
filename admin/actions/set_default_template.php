<?php
	if(!isset($gCms)) exit;
	if(!$this->CheckPermission('Manage Downloads') || !$this->CheckPermission('Modify Templates')) exit;

	$tpl_name = trim(urldecode($params['tpl_name']));
	$tpl_name = !empty($tpl_name) ? $tpl_name : false;
	if($tpl_name === false) $this->Redirect($id, 'defaultadmin', $returnid);

	$tpl = $this->GetTemplate($tpl_name);

	if(trim($tpl)!='') {
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET description = ? WHERE dl_id = ?';
		$result = $this->db->Execute($query, array($tpl_name, 0));
	} else
		$this->errors[] = $this->Lang('error_notplcontent');

	/*
	if(!isset($params['ajax']) || $params['ajax'] != "true") {
		if(count($this->errors > 0)) {
			$params = array('active_tab' => 'templates');
		} else {
			$params = array('tab_message' => 'error_notplcontent', 'active_tab' => 'templates');
		}
		$this->Redirect($id, 'defaultadmin', '', $params);
	}


	if(isset($params['ajax']) && $params['ajax'] === "true") {
		ob_end_clean();
		if(count($this->errors) == 0) {
			echo "1,";
		} else {
			echo "0,";
			echo $this->DisplayErrors(true);
		}
		exit;
	}
	*/

?>