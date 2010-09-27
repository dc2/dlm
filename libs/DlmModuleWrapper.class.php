<?php
// this file contains the basic CMSModule-extension with all the overwritten methods
// that content the basic information about the module

class DlmModuleWrapper extends CMSModule {
	function GetName() {
		return 'DLM';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.8';
	}

	function GetHelp() {
		return $this->Lang('help');
	}

	function GetAuthor() {
		return 'dc2';
	}

	function GetAuthorEmail() {
		return 'dc2@worldofanno.de';
	}

	function GetChangeLog() {
		return $this->Lang('changelog');
	}

	function AdminStyle() {
		return GetAdminStyle();
	}

	function IsPluginModule() {
		return true;
	}

	function HasAdmin() {
		return true;
	}

	function GetAdminSection() {
		return 'content';
	}

	function GetAdminDescription() {
		return $this->Lang('moddescription');
	}

	function VisibleToAdminUser() {
		return $this->CheckPermission('Use DLM');
	}

	function GetDependencies() {
		return array();
	}

	function MinimumCMSVersion() {
		return '1.6.7';
	}

	function GetEventDescription($eventname)
	{
		return $this->lang('evd-'.$eventname);
	}

	function GetEventHelp($eventname)
	{
		return $this->lang('evd-'.$eventname);
	}

	function InstallPostMessage() {
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage() {
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage() {
		return $this->Lang('really_uninstall');
	}
}
?>