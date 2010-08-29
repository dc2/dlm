<?php	
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Set DlM Prefs')) exit;
	
	if(isset($params['prefsubmit'])) {
		$obfuscate	= (int) $params['obfuscate'];
		$referer	= (int) $params['referer'];
		$js_effects	= (int) $params['js_effects'];
		$_returnid	= (int) $params['_returnid'];
		$allowed_referer = trim($params['allowed_referer']);
			
		$this->SetPreference('obfuscate', $obfuscate);
		$this->SetPreference('referer', $referer);
		$this->SetPreference('allowed_referer', $allowed_referer);
		$this->SetPreference('js_effects', $js_effects);
		$this->SetPreference('returnid', $_returnid);
		
		$blacklist = trim($params['blacklist']);
		$whitelist = trim($params['whitelist']);
		
		$blacklist = substr($blacklist, -1, 1) == ';' ? substr($blacklist, 0, strlen($blacklist) - 1) : $blacklist;
		$whitelist = substr($whitelist, -1, 1) == ';' ? substr($whitelist, 0, strlen($whitelist) - 1) : $whitelist;
		
		$extregexp = '/^([0-9A-Za-z];?){1}(;?[0-9A-Za-z];?)*$/';
		
		if(preg_match($extregexp, $blacklist) == 1 || trim($blacklist) == '') {
			$this->SetPreference('blacklist', $blacklist);
		} else {
			$this->errors[] = 'Blacklist'.$this->Lang('error_fileextformat');
		}
		if(preg_match($extregexp, $whitelist) == 1 || trim($whitelist) == '') {
			$this->SetPreference('whitelist', $whitelist);
		} else {
			$this->errors[] = 'Whitelist'.$this->Lang('error_fileextformat');
		}
	} else {
		$obfuscate	= (int) $this->GetPreference('obfuscate', 1);
		$referer	= (int) $this->GetPreference('referer', 1);
		$js_effects	= (int) $this->GetPreference('js_effects', 0);
		$_returnid	= (int) $this->GetPreference('returnid', 0);
		$allowed_referer =	$this->GetPreference('allowed_referer', '');
		
		$blacklist = $this->GetPreference('blacklist', '');
		$whitelist = $this->GetPreference('whitelist', '');
	}
	
	$this->smarty->assign('extensions_text', $this->Lang('extensions'));
	$this->smarty->assign('extensions_desc', $this->Lang('extensions_desc'));
	
	$this->smarty->assign('downloads_text', $this->Lang('downloads'));
	
	$this->smarty->assign('misc_text', $this->Lang('misc'));
	$this->smarty->assign('_returnid', $this->CreateInputTextWithLabel($id, '_returnid', $_returnid, 20, false, '', $this->Lang('returnid_desc').'<br />'));
	
	$this->smarty->assign('obfuscate', $this->CreateLabelForInput($id, 'obfuscate', $this->Lang('obfuscate_desc').'<br />') . 
									   $this->CreateInputDropdown($id, 'obfuscate', explode(';', $this->Lang('obfuscate_list')), $obfuscate));
									   
	$this->smarty->assign('referer', $this->CreateLabelForInput($id, 'referer', $this->Lang('referer_desc').'<br />') . 
									 $this->CreateInputDropdown($id, 'referer', explode(';', $this->Lang('referer_list')), $referer));
									 
	$this->smarty->assign('allowed_referer', $this->CreateInputTextWithLabel($id, 'allowed_referer', $allowed_referer, 100, false, '', $this->Lang('allowed_referer').'<br />'));								   
	
	$this->smarty->assign('js_effects', $this->CreateLabelForInput($id, 'js_effects', $this->Lang('js_effects_text').'<br />') . 
										$this->CreateInputDropdown($id, 'js_effects', explode(';', $this->Lang('js_effetcs_list')), $js_effects));
	
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'defaultadmin', $returnid, 'post', '', false, '', array('active_tab' => 'prefs')));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	
	$this->smarty->assign('blacklist', $this->CreateInputTextWithLabel($id, 'blacklist', $blacklist, 100, false, '', $this->Lang('blacklist_desc').'<br />'));
	$this->smarty->assign('whitelist', $this->CreateInputTextWithLabel($id, 'whitelist', $whitelist, 100, false, '', $this->Lang('whitelist_desc').'<br />'));
	
	$this->smarty->assign('prefsubmit', $this->CreateInputSubmit($id, 'prefsubmit', $this->Lang('submit')));
	
	echo $this->DisplayErrors();
	echo $this->ProcessTemplate('admin/preferences.tpl');
?>