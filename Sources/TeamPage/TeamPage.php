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

class TeamPage
{
	public static $name = 'TeamPage';
	public static $version = '5.4';

	public static function initialize()
	{
		self::defineHooks();
		self::setDefaults();
	}

	/**
	 * TeamPage::setDefaults()
	 *
	 * Sets almost every setting to a default value
	 * @return void
	 */
	public static function setDefaults()
	{
		global $modSettings;

		$defaults = [
			'TeamPage_enable' => 1,
			'TeamPage_show_badges' => 1,
			'TeamPage_show_description' => 1,
			'TeamPage_show_avatars' => 1,
			'TeamPage_avatars_width' => 64,
			'TeamPage_avatars_height' => 64,
			'TeamPage_show_personal' => 0,
			'TeamPage_show_custom' => 0,
			'TeamPage_show_posts' => 0,
			'TeamPage_show_website' => 0,
			'TeamPage_show_login' => 0,
			'TeamPage_show_registered' => 0,
			'TeamPage_enable_modpage' => 0,
		];
		$modSettings = array_merge($defaults, $modSettings);
	}

	/**
	 * TeamPage::defineHooks()
	 *
	 * Load hooks quietly
	 * @return void
	 */
	public static function defineHooks()
	{
		$hooks = [
			'autoload' => 'autoload',
			'actions' => 'hookActions',
			'menu_buttons' => 'hookButtons',
			'pre_css_output' => 'preCSS',
		];
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, __CLASS__ . '::'.$callable, false);
	}

	/**
	 * TeamPage::autoload()
	 *
	 * @param array $classMap
	 * @return void
	 */
	public static function autoload(&$classMap)
	{
		$classMap['TeamPage\\'] = 'TeamPage/';
	}

	/**
	 * TeamPage::hookActions()
	 *
	 * Insert the actions needed by this mod
	 * @param array $actions An array containing all possible SMF actions. This includes loading different hooks for certain areas.
	 * @return void
	 */
	public static function hookActions(&$actions)
	{
		// The main action
		$actions['team'] = ['TeamPage/View.php', __NAMESPACE__  . '\View::Main#'];

		// Add some hooks by action
		switch ($_REQUEST['action'])
		{
			case 'admin':
				add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::hookAreas', false, '$sourcedir/TeamPage/Settings.php');
				break;
			case 'helpadmin':
				add_integration_function('integrate_helpadmin', __NAMESPACE__ . '\Settings::helpadmin', false, '$sourcedir/TeamPage/Settings.php');
				break;
			case 'who':
				loadLanguage('TeamPage/');
				add_integration_function('who_allowed', __CLASS__ . '::whoAllowed', false);
				add_integration_function('integrate_whos_online', __CLASS__ . '::whoData', false);
				break;
		}
	}

	/**
	 * TeamPage::hookButtons()
	 *
	 * Insert a Team button on the menu buttons array
	 * @param array $buttons An array containing all possible tabs for the main menu.
	 * @return void
	 */
	public static function hookButtons(&$buttons)
	{
		global $txt, $scripturl, $modSettings;

		loadLanguage('TeamPage/');

		$before = 'admin';
		$temp_buttons = array();
		foreach ($buttons as $k => $v) {
			if ($k == $before) {
				$temp_buttons['team'] = array(
					'title' => $txt['TeamPage_main_button'],
					'href' => $scripturl . '?action=team',
					'icon' => 'team',
					'show' => allowedTo('teampage_canAccess') && !empty($modSettings['TeamPage_enable']),
				);
			}
			$temp_buttons[$k] = $v;
		}
		$buttons = $temp_buttons;

		// DUH! winning!
		self::Credits();
	}

	/**
	 * TeamPage::Credits()
	 *
	 * Used in the credits action.
	 * @param boolean $return decide between returning a string or append it to a known context var.
	 * @return string A link for copyright notice
	 */
	public static function Credits()
	{
		global $context;

		if (isset($context['current_action']) && $context['current_action'] === 'team')
			return '<br /><div style="text-align: center;"><span class="smalltext">Powered by <a href="https://smftricks.com" target="_blank" rel="noopener">Team Page</a></span></div>';
	}

	/**
	 * TeamPage::whoAllowed()
	 *
	 * Used in the who's online action.
	 * @param $allowedActions is the array of actions that require a specific permission.
	 * @return void
	 */
	public static function whoAllowed(&$allowedActions)
	{
		$allowedActions += array(
			'teampage' => array('admin_forum'),
		);
	}

	/**
	 * TeamPage::whoData()
	 *
	 * Used in the who's online action.
	 * @param $action It gets the request parameters 
	 * @return string for the current action
	 */
	public static function whoData($actions)
	{
		global $txt;

		// Show this only in the who's online action.
		if (isset($actions['action']) && ($actions['action'] === 'team'))
			return $txt['TeamPage_whoall_teampage'];
	}

	/**
	 * TeamPage::preCSS()
	 * 
	 * Add the icon via CSS
	 * 
	 * @return void
	 */
	public static function preCSS()
	{
		global $settings;

		// Add the icon using inline css
		addInlineCss('
			.main_icons.team::before {
				background-position: 0;
				background-image: url("' . $settings['default_images_url'] . '/icons/team.png");
				background-size: contain;
			}
		');
	}
}