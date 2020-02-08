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
			add_integration_function('integrate_' . $point, __CLASS__ . '::'.$callable .'#', false);
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
	public function hookActions(&$actions)
	{
		global $sourcedir;

		// The main action
		$actions['team'] = ['TeamPage/View.php', 'View::Main#'];

		// Add some hooks by action
		switch ($_REQUEST['action']) {
			case 'admin':
				add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::hookAreas', false, '$sourcedir/TeamPage/Settings.php');
				break;
			case 'who':
				loadLanguage('TeamPage');
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
	public function hookButtons(&$buttons)
	{
		global $txt, $scripturl, $modSettings;

		loadLanguage('TeamPage');

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
			$this->Layer();

		// DUH! winning!
		$this->Credits();
	}

	/**
	 * TeamPage::Layer()
	 *
	 * Used for adding the team page tabs quickly
	 * @return void
	 * @author Diego Andrés
	 */
	public function Layer()
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
	public function Credits($return = false)
	{
		global $context, $txt;

		if (isset($context['current_action']) && $context['current_action'] === 'team')
			return '<br /><div style="text-align: center;"><span class="smalltext">Powered by <a href="https://smftricks.com" target="_blank" rel="noopener">Team Page</a></span></div>';
	}
}



class TeamPageLoad
{

	public static function TeamPage_Main()
	{
		global $txt, $scripturl, $context, $modSettings;
				
		// Set all the page stuff
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=teampage',
			'name' => TeamPage::text('main_button'),
		);

		// Is allowed to view the Team Page?
		isAllowedTo('view_teampage');
		
		// Load the TP template
		loadtemplate('TeamPage');
		
		// Load the language
		loadLanguage('TeamPage');
		loadLanguage('Profile');
		
		// Load the Page
		if ((isset($_REQUEST['sa']) && ($_REQUEST['sa'] == 'moderators')) || ((self::CountPages() == 0) && !empty($modSettings['TeamPage_enable_modpage'])))
			TeamPageLoad::LoadModerators();
		else
			TeamPageLoad::TP_Load();
			
	}
	
	public static function GetFirstPage()
	{
		global $smcFunc;
		
		// Now.. Let's bring the pages
		$sortp = $smcFunc['db_query']('','
			SELECT cp.sub_page, cp.id_page
			FROM {db_prefix}teampage_cp AS cp
			ORDER BY cp.id_page ASC
			LIMIT 1',
			array()
		);
		$findsa = $smcFunc['db_fetch_assoc']($sortp);
		
		return $findsa['sub_page'];
			
	}
	
	public static function CountPages()
	{
		global $smcFunc;

		$result = $smcFunc['db_query']('', '
			SELECT id_page
			FROM {db_prefix}teampage_cp',
			array()
		);

		return $smcFunc['db_num_rows']($result);
	}
	
	public static function Handle_Types($type,$body)
	{
		
		if ($type == 'bbc')
		{
			$content = !empty($body) ? $body : '';
			return parse_bbc($content);	
		}
		elseif ($type == 'html')
		{
			$content = !empty($body) ? $body : '';
			return un_htmlspecialchars($content);
		}
	}
	
	public static function TP_Load()
	{
		global $scripturl, $context, $txt, $smcFunc, $modSettings, $settings;
		
		// If there is no subaction in the url, We'll use the first page in the database
		$countpages = TeamPageLoad::CountPages();
		// Let's use it for similar purposes too
		if ($countpages == 1 && empty($modSettings['TeamPage_enable_modpage']))
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : TeamPageLoad::GetFirstPage();
			$context['teampage']['onep'] = false;
		}
		elseif ($countpages == 1 && !empty($modSettings['TeamPage_enable_modpage']))
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : TeamPageLoad::GetFirstPage();
			$context['teampage']['onep'] = true;
		}
		elseif ($countpages >= 2)
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : TeamPageLoad::GetFirstPage();
			$context['teampage']['onep'] = true;
		}		
		elseif ($countpages == 0 && !empty($modSettings['TeamPage_enable_modpage']))
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : 'moderators';
			$context['teampage']['onep'] = false;
		}
		elseif ($countpages == 0 && empty($modSettings['TeamPage_enable_modpage']))
		{
			redirectexit(($context['user']['is_admin']) ? 'action=admin;area=teampage;sa=pages' : '');
		}
		
		// Check for the page before process
		$teampages = $smcFunc['db_query']('', '
			SELECT name_page, sub_page, id_page, type, body, is_text
			FROM {db_prefix}teampage_cp
			WHERE sub_page = {text:sub_pagecp}',
			array(
				'sub_pagecp' => $context['teampage']['current_sa'],
			)
		);
		$checkpage = $smcFunc['db_fetch_assoc']($teampages);
		
		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage_main_button']. ' - '. $checkpage['name_page'];
		$context['teampage']['name_page'] = $checkpage['name_page'];
		$context['sub_template'] = ($checkpage['is_text'] == 0) ? 'main' : 't_main';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=teampage;sa='. $checkpage['sub_page'],
			'name' => $checkpage['name_page'],
		);
		
		// Wait a minute, are you actually editing a text page?
		if ($checkpage['is_text'] == 1)
		{
			// What type of page is??
			$context['page']['type'] = $checkpage['type'];
			// Where's the body??
			$context['page']['body'] = $checkpage['body'];
			// What do we do with the body??
			$context['page']['print'] = TeamPageLoad::Handle_Types($context['page']['type'],$context['page']['body']);
		}
		
		// I need the pages for showing them in the Team Page action
		$tpages = $smcFunc['db_query']('','
			SELECT cp.sub_page, cp.id_page, cp.name_page
			FROM {db_prefix}teampage_cp AS cp
			ORDER BY cp.id_page ASC',
			array()
		);
		
		$findpages = array();
		while ($p = $smcFunc['db_fetch_assoc']($tpages))
		{
			
			$findpages[] = array(
				'id_page' => $p['id_page'],
				'name_page' => $p['name_page'],
				'sub_page' => $p['sub_page'],
			);
		}
		$smcFunc['db_free_result']($tpages);
		foreach ($findpages as $pages)
		{
			
			$context['teampage']['tpages'][] = $pages;
		
		}
		unset($pages);		
		
		// Grab all the team groups!
		$request = $smcFunc['db_query']('', '
			SELECT mg.id_group, mg.group_name, mg.description, mg.online_color, mg.stars, tp.place, tp.roworder, tp.id_page, cp.sub_page
			FROM {db_prefix}membergroups AS mg
			LEFT JOIN {db_prefix}teampage AS tp ON mg.id_group = tp.id_group
			LEFT JOIN {db_prefix}teampage_cp AS cp ON cp.id_page = tp.id_page
			WHERE cp.sub_page = {text:sub_page}
			ORDER BY tp.roworder', array(
				'sub_page' => $context['teampage']['current_sa'],
			)
		);
	
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$row['stars'] = empty($row['stars']) ? array('', '') : explode('#', $row['stars']);
	
			$context['display_groups'][$row['place']][$row['id_group']] = array(
				'name' => $row['group_name'],
				'description' => $row['description'],
				'color' => $row['online_color'],
				'image' => str_repeat('<img src="' . str_replace('$language', $context['user']['language'], isset($row['stars'][1]) ? $settings['images_url'] . '/' . $row['stars'][1] : '') . '" alt="*" border="0" />', empty($row['stars'][0]) || empty($row['stars'][1]) ? 0 : $row['stars'][0]),
				'members' => array(),
				'place' => $row['place'],
			);

				$query_members = $smcFunc['db_query']('', '
					SELECT 
						mem.id_member, mem.id_group, mem.real_name, mem.avatar, mem.last_login, mem.usertitle, mem.website_url, mem.website_title, mem.posts, mem.date_registered,
						IFNULL(lo.log_time, 0) AS is_online, 
						IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type
					FROM {db_prefix}members AS mem
						LEFT JOIN {db_prefix}log_online AS lo ON (lo.id_member = mem.id_member)
						LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
					WHERE mem.id_group = {int:id_group} ' . (!empty($modSettings['TeamPage_additional_groups']) ? 'OR FIND_IN_SET({int:id_group}, mem.additional_groups)' : '') . '
					ORDER BY mem.real_name ASC',
					array(
						'id_group' => $row['id_group'],
					)
				);
	
			// Looping through the members.
			while ($profile = $smcFunc['db_fetch_assoc']($query_members))
			{
	
				$context['display_groups'][$row['place']][$row['id_group']]['members'][][] = array(
					'id' => $profile['id_member'],
					'group_id' => $profile['id_group'],
					'name' => '<span style="color: ' . $row['online_color'] . ';">' . $profile['real_name'] . '</span>',
					'colorless_name' => $profile['real_name'],
					'href' => $scripturl . '?action=profile;u=' . $profile['id_member'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $profile['id_member'] . '"><span style="color: ' . $row['online_color'] . ';">' . $profile['real_name'] . '</span></a>',
					'title' => $profile['usertitle'],
					'web_title' => $profile['website_title'],
					'web_url' => $profile['website_url'],
					'posts' => $profile['posts'],
					'date' => timeformat($profile['date_registered'],'%B %d, %Y, %H:%M'),
					'avatar' => array(
						'name' => $profile['avatar'],
						'image' => $profile['avatar'] == '' ? ($profile['id_attach'] > 0 ? '<img src="' . (empty($profile['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $profile['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $profile['filename']) . '" alt="" class="avatar" border="0" />' : '') : (stristr($profile['avatar'], 'http://') ? '<img src="' . $profile['avatar'] . '" alt="" class="avatar" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($profile['avatar']) . '" alt="" class="avatar" border="0" />'),
						'href' => $profile['avatar'] == '' ? ($profile['id_attach'] > 0 ? (empty($profile['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $profile['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $profile['filename']) : '') : (stristr($profile['avatar'], 'http://') ? $profile['avatar'] : $modSettings['avatar_url'] . '/' . $profile['avatar']),
						'url' => $profile['avatar'] == '' ? '' : (stristr($profile['avatar'], 'http://') ? $profile['avatar'] : $modSettings['avatar_url'] . '/' . $profile['avatar'])
					),
					'last_login' => timeformat($profile['last_login']),
					'online' => array(
						'label' => $txt[$profile['is_online'] ? 'online' : 'offline'],
						'href' => $scripturl . '?action=pm;sa=send;u=' . $profile['id_member'],
						'link' => '<a href="' . $scripturl . '?action=pm;sa=send;u=' . $profile['id_member'] . '">' . $txt[$profile['is_online'] ? 'online' : 'offline'] . '</a>',
							'image_href' => $settings['images_url'] . '/' . ($profile['is_online'] ? 'useron' : 'useroff') . '.gif',
					),
				);
			}
			$smcFunc['db_free_result']($query_members);
		}
		$smcFunc['db_free_result']($request);
	
		// Empty? That can't be right, looks like they configured it wrong. Let's do some figuring out..
		if (empty($context['display_groups']) && ($context['teampage']['current_sa'] != 'moderators') && ($checkpage['is_text'] == 0))
			redirectexit(($context['user']['is_admin']) ? 'action=admin;area=teampage;sa=editpage;id='. $checkpage['id_page']. ';error' : '');
		elseif (empty($checkpage['body']) && ($checkpage['is_text'] == 1))
			redirectexit(($context['user']['is_admin']) ? 'action=admin;area=teampage;sa=editpage;id='. $checkpage['id_page']. ';error' : '');
			
	}
	
	public static function CountModerators()
	{
		global $smcFunc;
		
		$countmods = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}moderators
			GROUP BY id_member',
			array()
		);
		
		return $smcFunc['db_num_rows']($countmods);
		
	}
	
	public static function LoadModerators()
	{
		global $scripturl, $context, $txt, $smcFunc, $modSettings, $settings, $user_info;
		
		// If there is no subaction in the url, We'll use the first page in the database
		$countpages = TeamPageLoad::CountPages();
		// Let's use it for similar purposes too
		if ($countpages == 1)
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : TeamPageLoad::GetFirstPage();
			$context['teampage']['onep'] = false;
		}
		
		elseif ($countpages >= 2)
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : TeamPageLoad::GetFirstPage();
			$context['teampage']['onep'] = true;
		}
		elseif ($countpages == 0 && !empty($modSettings['TeamPage_enable_modpage']))
		{
			$context['teampage']['current_sa'] = (isset($_REQUEST['sa'])) ? $_REQUEST['sa'] : 'moderators';
			$context['teampage']['onep'] = false;
		}
		elseif ($countpages == 0 && empty($modSettings['TeamPage_enable_modpage']))
		{
			redirectexit(($context['user']['is_admin']) ? 'action=admin;area=teampage;sa=pages' : '');
		}
		
		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage_main_button']. ' - '. $txt['TeamPage_moderators'];
		$context['sub_template'] = 'moderators';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=teampage;sa=moderators',
			'name' => $txt['TeamPage_moderators'],
		);
		
		$context['teampage']['count_moderators'] = self::CountModerators();
		
		if (empty($modSettings['TeamPage_enable_modpage']))
			redirectexit(($context['user']['is_admin']) ? 'action=admin;area=teampage;sa=settings' : '');
			
		// I need the pages for showing them in the Team Page action
		$tpages = $smcFunc['db_query']('','
			SELECT cp.sub_page, cp.id_page, cp.name_page
			FROM {db_prefix}teampage_cp AS cp
			ORDER BY cp.id_page ASC',
			array()
		);
		
		$findpages = array();
		while ($p = $smcFunc['db_fetch_assoc']($tpages))
		{
			
			$findpages[] = array(
				'id_page' => $p['id_page'],
				'name_page' => $p['name_page'],
				'sub_page' => $p['sub_page'],
			);
		}
		$smcFunc['db_free_result']($tpages);
		foreach ($findpages as $pages)
		{
			
			$context['teampage']['tpages'][] = $pages;
		
		}
		unset($pages);	
			
		// Grab all the team groups!
		$request = $smcFunc['db_query']('', '
			SELECT mg.id_group, mg.group_name, mg.description, mg.online_color, mg.stars
			FROM {db_prefix}membergroups AS mg
			WHERE mg.id_group = 3
			LIMIT 1'
		);
		
		$total = self::CountModerators();
		$maxIndex = 8;
		$sort = 8;
		
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$row['stars'] = empty($row['stars']) ? array('', '') : explode('#', $row['stars']);
	
			$context['display_groups'][] = array(
				'name' => $row['group_name'],
				'description' => $row['description'],
				'color' => $row['online_color'],
				'image' => str_repeat('<img src="' . str_replace('$language', $context['user']['language'], isset($row['stars'][1]) ? $settings['images_url'] . '/' . $row['stars'][1] : '') . '" alt="*" border="0" />', empty($row['stars'][0]) || empty($row['stars'][1]) ? 0 : $row['stars'][0]),
				'members' => array(),
			);

				$query_members = $smcFunc['db_query']('', '
				SELECT
					mem.id_member, mem.id_group, mem.real_name, mem.avatar, mem.last_login, mem.usertitle, mem.website_url, mem.website_title, group_concat(o.id_board) idboard, group_concat(b.name) boardname, b.id_board, mem.posts, mem.date_registered,
					IFNULL(lo.log_time, 0) AS is_online,
					IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type
				FROM ({db_prefix}members AS mem, {db_prefix}moderators AS o, {db_prefix}boards AS b)
					LEFT JOIN {db_prefix}log_online AS lo ON (lo.id_member = mem.id_member)
					LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
				WHERE o.id_member = mem.id_member
					AND b.id_board = o.id_board
					AND ' . $user_info['query_see_board'] . '
				GROUP BY mem.id_member
				LIMIT {int:start}, {int:maxindex}',
				array(
					'start' => $_REQUEST['start'],
					'maxindex' => $maxIndex,
					'sort' => $sort
				)
			);
	
			// Looping through the members.
			while ($profile = $smcFunc['db_fetch_assoc']($query_members))
			{
	
				$context['display_groups'][]['members'][][] = array(
					'id' => $profile['id_member'],
					'group_id' => $profile['id_group'],
					'name' => '<span style="color: ' . $row['online_color'] . ';">' . $profile['real_name'] . '</span>',
					'colorless_name' => $profile['real_name'],
					'href' => $scripturl . '?action=profile;u=' . $profile['id_member'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $profile['id_member'] . '"><span style="color: ' . $row['online_color'] . ';">' . $profile['real_name'] . '</span></a>',
					'title' => $profile['usertitle'],
					'web_title' => $profile['website_title'],
					'web_url' => $profile['website_url'],
					'posts' => $profile['posts'],
					'moderating' => array(
						'id' => $profile['idboard'],
						'name' => $profile['boardname'],
					),
					'date' => timeformat($profile['date_registered']),
					'avatar' => array(
						'name' => $profile['avatar'],
						'image' => $profile['avatar'] == '' ? ($profile['id_attach'] > 0 ? '<img src="' . (empty($profile['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $profile['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $profile['filename']) . '" alt="" class="avatar" border="0" />' : '') : (stristr($profile['avatar'], 'http://') ? '<img src="' . $profile['avatar'] . '" alt="" class="avatar" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($profile['avatar']) . '" alt="" class="avatar" border="0" />'),
						'href' => $profile['avatar'] == '' ? ($profile['id_attach'] > 0 ? (empty($profile['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $profile['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $profile['filename']) : '') : (stristr($profile['avatar'], 'http://') ? $profile['avatar'] : $modSettings['avatar_url'] . '/' . $profile['avatar']),
						'url' => $profile['avatar'] == '' ? '' : (stristr($profile['avatar'], 'http://') ? $profile['avatar'] : $modSettings['avatar_url'] . '/' . $profile['avatar'])
					),
					'last_login' => timeformat($profile['last_login']),
					'online' => array(
						'label' => $txt[$profile['is_online'] ? 'online' : 'offline'],
						'href' => $scripturl . '?action=pm;sa=send;u=' . $profile['id_member'],
						'link' => '<a href="' . $scripturl . '?action=pm;sa=send;u=' . $profile['id_member'] . '">' . $txt[$profile['is_online'] ? 'online' : 'offline'] . '</a>',
							'image_href' => $settings['images_url'] . '/' . ($profile['is_online'] ? 'useron' : 'useroff') . '.gif',
					),
				);
			}
			$smcFunc['db_free_result']($query_members);
		}
		$smcFunc['db_free_result']($request);
		
		/* Build the pagination */
		$context['page_index'] = constructPageIndex($scripturl . '?action=teampage;sa=moderators', $_REQUEST['start'], $total, $maxIndex, false);
		
	}
}