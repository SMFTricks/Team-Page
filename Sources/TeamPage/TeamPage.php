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

class TeamPage
{
	public const NAME = 'TeamPage';
	public const VERSION = '5.0';

	public function initialize()
	{
		$this->defineHooks();
		$this->setDefaults();
	}

	/**
	 * TeamPage::setDefaults()
	 *
	 * Sets almost every setting to a default value
	 * @return void
	 */
	public function setDefaults()
	{
		global $modSettings;

		$defaults = [
			'TeamPage_enable' => 1,
			'TeamPage_enable_modpage' => 0,
			'TeamPage_show_badges' => 1,
			'TeamPage_show_avatars' => 0,
			'TeamPage_show_desc' => 0,
			'TeamPage_additional_groups' => 0,
			'TeamPage_modpage_description' => '',
		];
		$modSettings = array_merge($defaults, $modSettings);
	}

	/**
	 * TeamPage::defineHooks()
	 *
	 * Load hooks quietly
	 * @return void
	 * @author Peter Spicer (Arantor)
	 */
	public function defineHooks()
	{
		$hooks = [
			'autoload' => 'autoload',
			'actions' => 'hookActions',
			'menu_buttons' => 'hookButtons',
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
	 * @author Peter Spicer (Arantor)
	 */
	public static function hookActions(&$actions)
	{
		global $sourcedir;

		// The main action
		$actions['team'] = ['TeamPage/View.php', __NAMESPACE__  . '\View::Main#'];

		// Add some hooks by action
		switch ($_REQUEST['action']) {
			case 'admin':
				add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::hookAreas', false, '$sourcedir/TeamPage/Settings.php');
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
					'icon' => 'icons/team.png',
					'show' => allowedTo('teampage_canAccess') && !empty($modSettings['TeamPage_enable']),
				);
			}
			$temp_buttons[$k] = $v;
		}
		$buttons = $temp_buttons;
		
		// Too lazy for adding the menu on all the sub-templates
		if (!empty($modSettings['TeamPage_enable']))
			self::Layer();

		// DUH! winning!
		self::Credits();
	}

	/**
	 * TeamPage::Layer()
	 *
	 * Used for adding the team page tabs quickly
	 * @return void
	 * @author Diego Andrés
	 */
	public static function Layer()
	{
		global $context;

		if (isset($context['current_action']) && $context['current_action'] === 'team' && allowedTo('teampage_canAccess')) {
			$position = array_search('body', $context['template_layers']);
			if ($position === false)
				$position = array_search('main', $context['template_layers']);

			if ($position !== false) {
				$before = array_slice($context['template_layers'], 0, $position + 1);
				$after = array_slice($context['template_layers'], $position + 1);
				$context['template_layers'] = array_merge($before, array('TeamPage'), $after);
			}
		}
	}

	/**
	 * TeamPage::Credits()
	 *
	 * Used in the credits action.
	 * @param boolean $return decide between returning a string or append it to a known context var.
	 * @return string A link for copyright notice
	 */
	public static function Credits($return = false)
	{
		global $context, $txt;

		if (isset($context['current_action']) && $context['current_action'] === 'team')
			return '<br /><div style="text-align: center;"><span class="smalltext">Powered by <a href="https://smftricks.com" target="_blank" rel="noopener">Team Page</a></span></div>';
	}
}