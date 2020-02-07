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
	public function hookAreas(&$admin_areas)
	{
		global $scripturl, $txt, $modSettings;

		loadLanguage('TeamPage');
		
		$admin_areas['config']['areas']['teampage'] = [
			'label' => $txt['TeamPage_button'],
			'function' => __NAMESPACE__ . '\Settings::Index#',
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
	 * @param array $permissionGroups An array containing all possible permissions groups.
	 * @param array $permissionList An associative array with all the possible permissions.
	 * @return void
	 */
	public static function Permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
	{
		global $txt;

		$shop_permissions = [
			'teampage_canAccess' => false,
		];

		$permissionGroups['membergroup'] = ['teampage'];
		foreach ($shop_permissions as $p => $s) {
			$permissionList['membergroup'][$p] = [$s,'teampage','teampage'];
			$hiddenPermissions[] = $p;
		}
	}

	private function Save($config_vars, $return_config, $sa)
	{
		global $context, $scripturl;

		if ($return_config)
			return $config_vars;

		$context['post_url'] = $scripturl . '?action=admin;area=teampage;sa='. $sa. ';save';

		// Saving?
		if (isset($_GET['save'])) {
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=teampage;sa='. $sa. '');
		}
		prepareDBSettingContext($config_vars);
	}

	public function Index()
	{
		global $txt, $context;

		$subactions = [
			'settings' => $this->Main(),
			'pages' => Pages::List(),
		];
		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'settings';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => $txt['TeamPage_page_settings'],
			'description' => $txt['TeamPage_page_settings_desc'],
			'tabs' => [
				'settings' => ['description' => $txt['TeamPage_page_settings_desc']],
			],
		];
		$subactions[$sa];
	}

	public function Main($return_config = false)
	{
		global $context, $txt, $sourcedir;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('TeamPage-Admin');

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
		$this->Save($config_vars, $return_config, 'settings');
	}
}