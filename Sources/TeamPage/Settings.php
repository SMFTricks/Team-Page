<?php

/**
 * @package Team Page
 * @version 5.4
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace TeamPage;

if (!defined('SMF'))
	die('No direct access...');

class Settings
{
	/**
	 * @var array The custom fields
	 */
	private $_custom_fields = [];

	/**
	 * Settings::hookAreas()
	 *
	 * Adding the admin section
	 * @param array $admin_areas An array with all the admin areas
	 * @return void
	 */
	public function hookAreas(&$admin_areas) : void
	{
		global $txt;

		loadLanguage('TeamPage/');
		
		$admin_areas['config']['areas']['teampage'] = [
			'label' => $txt['TeamPage_button'],
			'function' => __NAMESPACE__ . '\Settings::Index#',
			'icon' => 'server',
			'subsections' => [
				'pages' => [$txt['TeamPage_page_pages']],
				'settings' => [$txt['TeamPage_page_settings']],
			],
		];

		// Permissions
		add_integration_function('integrate_load_permissions', __CLASS__.'::Permissions#', false);
		// Delete membergroup
		add_integration_function('integrate_delete_membergroups', __NAMESPACE__ . '\Groups::Delete#', false);
	}

	/**
	 * Settings::Permissions()
	 *
	 * TeamPage permissions
	 * @param array $permissionGroups An array containing all possible permissions groups.
	 * @param array $permissionList An associative array with all the possible permissions.
	 * @return void
	 */
	public function Permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions) : void
	{
		global $modSettings;

		$permissions = [
			'teampage_canAccess' => false,
		];

		foreach ($permissions as $p => $s)
			$permissionList['membergroup'][$p] = [$s,'general'];

		// Team page disabled?
		if (empty($modSettings['TeamPage_enable']))
			$hiddenPermissions[] = 'teampage_canAccess';
	}

	/**
	 * Settings::helpadmin()
	 *
	 * Loads the language file for the help popups in the permissions page
	 *
	 * @return void
	 */
	public function helpadmin() : void
	{
		loadLanguage('TeamPage/');
	}

	/**
	 * Settings::Index()
	 * 
	 * The list of subactions for the teampage area
	 * 
	 * @return void
	 */
	public function Index() : void
	{
		global $txt, $context;

		loadTemplate('TeamPage-Admin');

		$subactions = [
			'settings' => 'Settings::Main',
			'pages' => 'Pages::List',
			'edit' => 'Pages::Edit',
			'save' => 'Pages::Save',
			'sort' => 'Groups::Save',
			'delete' => 'Pages::Delete',
			'order' => 'Pages::Order',
			'modsave' => 'Moderators::Save',
		];
		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'pages';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['TeamPage_page_settings'],
			'description' => $txt['TeamPage_page_settings_desc'],
			'tabs' => [
				'pages' => ['description' => $txt['TeamPage_page_pages_desc']],
				'settings' => ['description' => $txt['TeamPage_page_settings_desc']],
			],
		];
		call_helper(__NAMESPACE__ . '\\' . $subactions[$sa] . '#');
	}

	/**
	 * Settings:Main()
	 * 
	 * The main settings for the team page
	 * 
	 * @return void
	 */
	public function Main($return_config = false) : void
	{
		global $context, $txt, $sourcedir;

		require_once($sourcedir . '/ManageServer.php');

		// Set all the page stuff
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_settings'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		// Custom Fields
		$this->_custom_fields = Helper::Get(0, 100000, 'field_order', 'custom_fields',
			['col_name', 'field_name'],
			'WHERE active = {int:active}',
			false, '',
			[
				'active' => 1
			]
		);
		foreach ($this->_custom_fields as $custom_field)
			$context['TeamPage_custom_fields'][$custom_field['col_name']] = tokenTxtReplace($custom_field['field_name']);

		$config_vars = [
			['title', 'TeamPage_page_settings'],
			['check', 'TeamPage_enable'],
			['permissions', 'teampage_canAccess', 'subtext' => $txt['permissionhelp_teampage_canAccess']],
			['title', 'TeamPage_page_settings_layout'],
			['check', 'TeamPage_show_badges'],
			['check', 'TeamPage_show_description'],
			['select', 'TeamPage_sort_by', 'subtext' => $txt['TeamPage_sort_by_desc'], [
				$txt['TeamPage_sort_by_id'],
				$txt['TeamPage_sort_by_name']
			]],
			'',
			['check', 'TeamPage_show_custom', 'hidden' => empty($modSettings['titlesEnable'])],
			['check', 'TeamPage_show_avatars'],
			['int', 'TeamPage_avatars_width', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['int', 'TeamPage_avatars_height', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_personal'],
			['check', 'TeamPage_show_login'],
			['check', 'TeamPage_show_registered'],
			['check', 'TeamPage_show_posts'],
			['check', 'TeamPage_show_website'],
			['check', 'TeamPage_show_members_ag'],
			'',
			['select', 'TeamPage_show_custom_fields', $context['TeamPage_custom_fields'], 'multiple' => true],
		];

		// Save!
		Helper::Save($config_vars, $return_config, 'settings');
	}
}