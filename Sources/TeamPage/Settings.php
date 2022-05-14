<?php

/**
 * @package Team Page
 * @version 5.2
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace TeamPage;

if (!defined('SMF'))
	die('No direct access...');

class Settings
{
	/**
	 * Settings::hookAreas()
	 *
	 * Adding the admin section
	 * @param array $admin_areas An array with all the admin areas
	 * @return
	 */
	public static function hookAreas(&$admin_areas)
	{
		global $txt;

		loadLanguage('TeamPage/');
		
		$admin_areas['config']['areas']['teampage'] = [
			'label' => $txt['TeamPage_button'],
			'function' => __NAMESPACE__ . '\Settings::Index',
			'icon' => 'server',
			'subsections' => [
				'pages' => [$txt['TeamPage_page_pages']],
				'settings' => [$txt['TeamPage_page_settings']],
			],
		];

		// Permissions
		add_integration_function('integrate_load_permissions', __CLASS__.'::Permissions', false);
		// Delete membergroup
		add_integration_function('integrate_delete_membergroups', __NAMESPACE__ . '\Groups::Delete', false);
	}

	/**
	 * Settings::Permissions()
	 *
	 * TeamPage permissions
	 * @param array $permissionGroups An array containing all possible permissions groups.
	 * @param array $permissionList An associative array with all the possible permissions.
	 * @return void
	 */
	public static function Permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions)
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
	 */
	public static function helpadmin()
	{
		loadLanguage('TeamPage/');
	}

	public static function Index()
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

	public static function Main($return_config = false)
	{
		global $context, $txt, $sourcedir;

		require_once($sourcedir . '/ManageServer.php');

		// Set all the page stuff
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_settings'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = [
			['title', 'TeamPage_page_settings'],
			['check', 'TeamPage_enable'],
			['permissions', 'teampage_canAccess', 'subtext' => $txt['permissionhelp_teampage_canAccess']],
			['title', 'TeamPage_page_settings_layout'],
			['check', 'TeamPage_show_badges'],
			['check', 'TeamPage_show_description'],
			'',
			['check', 'TeamPage_show_custom'],
			['check', 'TeamPage_show_avatars', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['int', 'TeamPage_avatars_width'],
			['int', 'TeamPage_avatars_height'],
			['check', 'TeamPage_show_personal', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_login', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_registered', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_posts', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_website', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_members_ag'],
		];

		// Save!
		Helper::Save($config_vars, $return_config, 'settings');
	}
}