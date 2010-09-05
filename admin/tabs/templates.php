<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Modify Templates')) exit;

	$this->smarty->assign('th_actions', $this->Lang('th_actions'));
	$this->smarty->assign('th_templates', lang('templates'));

	$this->smarty->assign('areyousure_tpl', $this->Lang('areyousure_tpl'));

	$this->smarty->assign('templates', $this->ListTemplates($id, $returnid));

	$this->smarty->assign('add_template', $this->CreateLink($id, 'edit_template', $returnid, $this->theme->DisplayImage('icons/system/newobject.gif', $this->Lang('add_template'),'','','systemicon').$this->Lang('add_template')));

	echo $this->DisplayErrors();
	echo $this->ProcessTemplate('admin/templates.tpl');
?>