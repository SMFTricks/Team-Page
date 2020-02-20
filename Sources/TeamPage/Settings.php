<?php

/**
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
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
		global $scripturl, $txt, $modSettings;

		loadLanguage('TeamPage');
		
		$admin_areas['config']['areas']['teampage'] = [
			'label' => $txt['TeamPage_button'],
			'function' => __NAMESPACE__ . '\Settings::Index',
			'icon' => 'server',
			'subsections' => [
				'settings' => [$txt['TeamPage_page_settings']],
				'pages' => [$txt['TeamPage_page_pages']],
			],
		];

		// Permissions
		add_integration_function('integrate_load_permissions', 'self::Permissions', false);
	}

	/**
	 * Settings::Permissions()
	 *
	 * TeamPage permissions
	 * @return void
	 */
	public static function Permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
	{
		global $txt;

		$permissions = [
			'teampage_canAccess' => false,
		];

		$permissionGroups['membergroup'] = ['teampage'];
		foreach ($permissions as $p => $s) {
			$permissionList['membergroup'][$p] = [$s,'teampage','teampage'];
			$hiddenPermissions[] = $p;
		}
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
			'delete' => 'Pages::Delete',
		];
		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'settings';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['TeamPage_page_settings'],
			'description' => $txt['TeamPage_page_settings_desc'],
			'tabs' => [
				'settings' => ['description' => $txt['TeamPage_page_settings_desc']],
				'pages' => ['description' => $txt['TeamPage_page_pages_desc']],
			],
		];
		call_helper(__NAMESPACE__ . '\\' . $subactions[$sa]);
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
			['check', 'TeamPage_show_badges'],
			['check', 'TeamPage_show_avatars', 'subtext' => $txt['TeamPage_show_avatars_desc']],
			['check', 'TeamPage_show_desc'],
			['check', 'TeamPage_additional_groups'],
			['title', 'TeamPage_moderators'],
			['check', 'TeamPage_enable_modpage'],
			['text', 'TeamPage_modpage_description', 'subtext' => $txt['TeamPage_modpage_description_desc']],
			['title', 'TeamPage_permissions'],
			['permissions', 'teampage_canAccess', 'subtext' => $txt['permissionhelp_teampage_canAccess']],
		];

		// Save!
		Helper::Save($config_vars, $return_config, 'settings');
	}
}