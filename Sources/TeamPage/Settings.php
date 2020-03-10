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

		loadLanguage('TeamPage/');
		
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
			'sort' => 'Groups::Save',
			'delete' => 'Pages::Delete',
			'order' => 'Pages::Order',
			'modsave' => 'Moderators::Save',
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
			['check', 'TeamPage_additional_groups', 'subtext' => $txt['TeamPage_additional_groups_desc']],

			['title', 'TeamPage_page_settings_layout'],
			['check', 'TeamPage_show_badges'],
			['check', 'TeamPage_show_description'],
			['check', 'TeamPage_show_custom'],
			['check', 'TeamPage_show_avatars', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_personal', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_login', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_registered', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_posts', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['check', 'TeamPage_show_website', 'subtext' => $txt['TeamPage_addinfo_desc']],
			['title', 'TeamPage_permissions'],
			['permissions', 'teampage_canAccess', 'subtext' => $txt['permissionhelp_teampage_canAccess']],
		];

		// Save!
		Helper::Save($config_vars, $return_config, 'settings');
	}
}