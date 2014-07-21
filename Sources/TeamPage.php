<?php

/**
 * TeamPage
 *
 * @package Team Page
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

	/**
	 * Wrapper function
	 *
	 * SMF cannot handle static methods being called via a variable: $static_method();
	 */
	function TeamPage()
	{
		 TeamPageLoad::TeamPage_Main();
	}

class TeamPageLoad extends TeamPage
{

	public static function TeamPage_Main()
	{
		global $txt, $scripturl, $context, $modSettings;
				
		// Set all the page stuff
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=teampage',
			'name' => parent::text('main_button'),
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
			self::LoadModerators();
		else
			self::TP_Load();
			
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
		$countpages = self::CountPages();
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
			$context['page']['print'] = self::Handle_Types($context['page']['type'],$context['page']['body']);
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
		$countpages = self::CountPages();
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
