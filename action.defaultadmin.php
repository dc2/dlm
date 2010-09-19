<?php
	if (!isset($gCms)) exit;

	echo '<div id="dlmpage">';

	$this->theme =& $gCms->variables['admintheme'];

	if (!$this->CheckPermission('Use DLM')) {
		return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	}

	echo $this->StartTabHeaders();

	$active_tab = isset($params['active_tab']) ? $params['active_tab'] : false;

	echo $this->SetTabHeader('general', $this->Lang('title_general'), ('general' == $active_tab) ? true : false);

	if ($this->CheckPermission('Set DLM Prefs'))
		echo $this->SetTabHeader('prefs', $this->Lang('title_prefs'), ('prefs' == $active_tab) ? true : false);

	echo $this->SetTabHeader('statistics', $this->Lang('title_statistics'), ('statistics' == $active_tab) ? true : false);

	if($this->CheckPermission('Modify Templates'))
		echo $this->SetTabHeader('templates', lang('templates'), ('templates' == $active_tab) ? true : false);

	echo $this->EndTabHeaders();
	echo $this->StartTabContent();

	echo $this->StartTab('general', $params);
	require(cms_join_path('admin', 'tabs', 'general.php'));
	echo $this->EndTab();

	if ($this->CheckPermission('Set DLM Prefs')) {
		echo $this->StartTab('prefs', $params);
		require(cms_join_path('admin', 'tabs', 'preferences.php'));
		echo $this->EndTab();
	}

	echo $this->StartTab('statistics', $params);
	require(cms_join_path('admin', 'tabs', 'statistics.php'));
	echo $this->EndTab();

	if($this->CheckPermission('Modify Templates')) {
		echo $this->StartTab('templates', $params);
		require(cms_join_path('admin', 'tabs', 'templates.php'));
		echo $this->EndTab();
	}

	echo $this->EndTabContent();

	echo '</div>';
?>