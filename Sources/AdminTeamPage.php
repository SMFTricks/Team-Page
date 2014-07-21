<?php

/**
 * TeamPageAdmin
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
	function loadAdmin()
	{
		 TeamPageAdmin::AdminIndex();
	}

class TeamPageAdmin extends TeamPage
{

	public static function AdminIndex()
	{
		global $context, $sourcedir;

		require_once($sourcedir . '/ManageSettings.php');
		
		loadtemplate('TeamPageAdmin');
		loadLanguage('TeamPage');
				
		$subActions = array(
			'settings' => 'self::TP_Settings',
			'pages' => 'self::TP_Pages',
			'addpage' => 'self::TP_PagesAdd',
			'deletepage' => 'self::TP_PagesDelete',
			'editpage' => 'self::TP_PagesEdit',
		);

		loadGeneralSettingParameters($subActions, 'settings');

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'tabs' => array(
				'settings' => array(),
				'pages' => array(),
			),
		);
		
		// Call the sub-action
		call_user_func($subActions[$_REQUEST['sa']]);

	}

	public static function TP_Settings($return_config = false)
	{
	
		global $scripturl, $context, $sourcedir, $txt;
			
		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage']. ' - '. $txt['TeamPage_page_settings'];
		$context['sub_template'] = 'show_settings';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['TeamPage_admin_description_settings'],
		);

		require_once($sourcedir . '/ManageServer.php');

		$config_vars = array(
		array('title', 'TeamPage_page_settings'),
		array('check', 'TeamPage_enable'),
		array('check', 'TeamPage_show_badges'),
		array('check', 'TeamPage_show_avatars', 'subtext' => $txt['TeamPage_show_avatars_desc']),
		array('check', 'TeamPage_show_desc'),
		array('check', 'TeamPage_additional_groups'),
		array('title', 'TeamPage_moderators'),
		array('check', 'TeamPage_enable_modpage'),
		array('text', 'TeamPage_modpage_description', 'subtext' => $txt['TeamPage_modpage_description_desc']),
		array('title', 'TeamPage_permissions'),
		array('permissions', 'view_teampage', 'subtext' => $txt['permissionhelp_view_teampage']),
		);

		if ($return_config)
			return $config_vars;

		$context['post_url'] = $scripturl . '?action=admin;area=teampage;sa=settings;save';

		// Saving?
		if (isset($_GET['save']))
		{
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=teampage;sa=settings');
		}

		prepareDBSettingContext($config_vars);
		
	}
	
	public static function TP_Pages()
	{
	
		global $context, $txt;
			
		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage']. ' - '. $txt['TeamPage_page_pages'];
		$context['sub_template'] = 'show_pages';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['TeamPage_admin_description_pages'],
		);
		
		/* Get All */
		$context['teampage']['pages']['all'] = self::getAll();
		
	}
	
	public static function getCount()
	{
		global $smcFunc;

		$result = $smcFunc['db_query']('', '
			SELECT id_page
			FROM {db_prefix}teampage_cp',
			array()
		);

		return $smcFunc['db_num_rows']($result);
	}
	
	public static function getAll()
	{
		global $smcFunc, $scripturl, $context;

		$total = self::getCount();
		$maxIndex = 5;
		$sort = 5;

		$result = $smcFunc['db_query']('', '
			SELECT tp.id_page, tp.name_page, tp.sub_page, tp.is_text, tp.type, tp.body
			FROM {db_prefix}teampage_cp AS tp
			ORDER BY tp.id_page ASC
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $_REQUEST['start'],
				'maxindex' => $maxIndex,
				'sort' => $sort
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($result))
			$return[$row['id_page']] = array(
				'id' => $row['id_page'],
				'name' => $row['name_page'],
				'subp' => $row['sub_page'],
				'text' => $row['is_text'],
				'type' => $row['type'],
				'body' => $row['body'],
			);

		$smcFunc['db_free_result']($result);

		/* Build the pagination */
		$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=teampage;sa=pages', $_REQUEST['start'], $total, $maxIndex, false);

		/* Done? */
		return !empty($return) ? $return : false;
	}
	
	public static function TP_PagesAdd()
	{
		global $txt, $smcFunc, $context;
		
		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage']. ' - '. $txt['TeamPage_page_pages'];
		$context[$context['admin_menu_name']]['current_subsection'] = 'pages';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['TeamPage_admin_description_pages'],
		);

		$alnum = '/^(\S)([a-z0-9])+$/'; 

		$ptitle = $smcFunc['htmlspecialchars']($_REQUEST['pagetitle'],ENT_QUOTES);
		$subact = $smcFunc['htmlspecialchars']($_REQUEST['subact'],ENT_QUOTES);
		
		if (!empty($_REQUEST['textpage']))
		{
			$textpage = $_REQUEST['textpage'];
			$type = $_REQUEST['type'];
		}
		else
		{
			$textpage = 0;
			$type = '';
		}

		// Check to make sure there is a title and a subaction
		if (empty($ptitle) || empty($subact))
			fatal_error(parent::text('error_title_sub'));
		
		// Only alnum
		if (!preg_match($alnum, $subact))
			fatal_error(parent::text('error_alnum_sub'));
	
		// If already exists, we will show a nice message
		$result = $smcFunc['db_query']('', "
					SELECT
						sub_page
					FROM {db_prefix}teampage_cp
					LIMIT 1
					");
		$checkresult = $smcFunc['db_fetch_assoc']($result);
		if ($subact == $checkresult['sub_page'])
			fatal_error(parent::text('error_already_sub'));
		elseif ($subact == 'moderators')
			fatal_error(parent::text('error_cannot_mod'));

		// Insert the page into the database
		$smcFunc['db_query']('', "INSERT INTO {db_prefix}teampage_cp 
				(name_page, sub_page, is_text, type)
			VALUES ('$ptitle', '$subact', '$textpage', '$type')");
			
		// Redirect to the pages section
		redirectexit('action=admin;area=teampage;sa=pages');
			
	}

	public static function TP_PagesDelete()
	{
		global $smcFunc;

		$id = (int) $_REQUEST['id'];

		// Delete the page from the database
		$smcFunc['db_query']('', "DELETE FROM {db_prefix}teampage_cp 
				WHERE id_page = $id");
		
		// Drop the content of that page
		$smcFunc['db_query']('', "DELETE FROM {db_prefix}teampage
				WHERE id_page = $id");
			
		// Redirect to the pages section
		redirectexit('action=admin;area=teampage;sa=pages');
			
	}
	
	public static function getCountGroups()
	{
		global $smcFunc;

		$result = $smcFunc['db_query']('', '
			SELECT mg.id_group
			FROM {db_prefix}membergroups AS mg
			WHERE mg.min_posts = -1',
			array()
		);

		return $smcFunc['db_num_rows']($result);
	}

	public static function TP_PagesEdit()
	{
		global $context, $txt, $smcFunc, $settings;
		
		$pageid = (int) $_REQUEST['id'];
		
		// Check for the page before process
		$teampages = $smcFunc['db_query']('', "
			SELECT id_page, name_page, sub_page, is_text
			FROM {db_prefix}teampage_cp
			WHERE id_page = $pageid
		");
		$checkpage = $smcFunc['db_fetch_assoc']($teampages);
			
		// So we are here... let's use the information above to show a description maybe?
		$editingpage = $checkpage['name_page'];
		$context['page']['sub_page'] = $checkpage['sub_page'];

		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage']. ' - '. $txt['TeamPage_page_page_edit']. ' '. $editingpage;
		$context['sub_template'] = ($checkpage['is_text'] == 1) ? 'manage_editor' : 'manage_groups';
		$context[$context['admin_menu_name']]['current_subsection'] = 'pages';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['TeamPage_admin_what_page']. ' '. $editingpage,
		);
		
		// We don't want any errors, so we'll check the page after the above stuff
		if($pageid != $checkpage['id_page'])
			fatal_error(parent::text('page_noexist'));
		if(empty($pageid))
			redirectexit('action=admin;area=teampage;sa=pages');
			
		// Wait a minute, are you actually editing a text page?
		if ($checkpage['is_text'] == 1)
		{
			// We are going to have something else instead of the groups...
			self::PagesEditText();
			
		}
		
		else
		{
			
			$context['teampage']['p_title'] = $checkpage['name_page'];
			$context['teampage']['p_subpage'] = $checkpage['sub_page'];
		
			// Default array...
			$context['groups'] = array(
				'left' => array(),
				'right' => array(),
				'bottom' => array(),
				'notactive' => array(),
				'all' => array(),
			);
			
			// Count them
			$context['groups']['count'] = self::getCountGroups();
			
			// Now.. Let's bring the groups
			$sort = $smcFunc['db_query']('', "
				SELECT mg.id_group, mg.group_name, mg.online_color
				FROM {db_prefix}membergroups AS mg
				WHERE mg.min_posts = -1 AND mg.id_group != 3",
				array()
			);
		
			$sortgroups = array();
			while ($g = $smcFunc['db_fetch_assoc']($sort))
			{
				
				$sortgroups[] = array(
					'id' => $g['id_group'],
					'name' => $g['group_name'],
					'color' => $g['online_color'],
				);
			}
			$smcFunc['db_free_result']($sort);
			
			foreach ($sortgroups as $sg)
			{
				
				$context['groups']['notactive'][] = $sg;
			
			}
			unset($sg);
					
			// Look for the groups that are already placed
			$request = $smcFunc['db_query']('', "
				SELECT mg.id_group, mg.group_name, mg.stars, mg.online_color, mg.description, tp.place, tp.roworder, tp.id_page
				FROM {db_prefix}membergroups AS mg
					LEFT JOIN {db_prefix}teampage AS tp ON (tp.id_group = mg.id_group)
				WHERE mg.min_posts = -1 AND tp.id_page = $pageid
				ORDER BY tp.roworder ASC",
				array()
			);
		
			$temp = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$row['stars'] = empty($row['stars']) ? array('', '') : explode('#', $row['stars']);
				
				$temp[] = array(
					'id' => $row['id_group'],
					'name' => $row['group_name'],
					'color' => $row['online_color'],
					'image' => str_repeat('<img src="' . str_replace('$language', $context['user']['language'], isset($row['stars'][1]) ? $settings['images_url'] . '/' . $row['stars'][1] : '') . '" alt="*" border="0" />', empty($row['stars'][0]) || empty($row['stars'][1]) ? 0 : $row['stars'][0]),
					'place' => $row['place'],
					'id_page' => $row['id_page'],
				);
			}
			$smcFunc['db_free_result']($request);		
		
			foreach ($temp as $group)
			{
				switch ($group['place'])
				{
					case 1: $context['groups']['left'][] = $group; break;
					case 2: $context['groups']['right'][] = $group; break;
					case 3: $context['groups']['bottom'][] = $group; break;
				}			
		
				$context['groups']['all'][] = array('id' => $group['id'], 'name' => $group['name'], 'id_page' => $group['id_page'],);
			}
			unset($group);
		
			// Figure out where we are...
			$cur_act = (isset($_REQUEST['act']) ? $_REQUEST['act'] : '');
		
			// More actions... based on what they're doing in the admin panel.
			$assoc_acts = array(
				'add' => 'self::Edit_AddGroup',
				'move' => 'self::Edit_MoveGroup',
				'swap' => 'self::Edit_SwapGroup',
				// Want to change the title or the subaction??
				'save' => 'self::Text_Save2',
			);
		
			if (in_array($cur_act, array_keys($assoc_acts)))
				call_user_func($assoc_acts[$cur_act]);
		
		}
			
	}
	
	public static function PagesEditText()
	{
		global $context, $smcFunc, $txt, $sourcedir;
		
		$pageid = (int) $_REQUEST['id'];
		
		// Bring the data for use it
		$what = $smcFunc['db_query']('', "
			SELECT id_page, name_page, sub_page, is_text, type, body
			FROM {db_prefix}teampage_cp
			WHERE id_page = $pageid
		");
		$btext = $smcFunc['db_fetch_assoc']($what);
		
		$context['teampage']['p_title'] = $btext['name_page'];
		$context['teampage']['p_subpage'] = $btext['sub_page'];
		$context['teampage']['p_body'] = $btext['body'];
		$context['teampage']['p_type'] = $btext['type'];
		
		// Let's get started...
		if ($btext['type'] == 'bbc')
		{

			// Load the editor file
			require_once($sourcedir.'/Subs-Editor.php');
					
			// Now create the editor.
			$editorOptions = array(
				'id' => 'body',
				'value' => !empty($context['teampage']['p_body']) ? $context['teampage']['p_body'] : '',
				'labels' => array(
					'post_button' => $txt['save'],
				),
			);
			create_control_richedit($editorOptions);
		
			// Store the ID.
			$context['post_box_name'] = $editorOptions['id'];

		}
		
		// Figure out where we are...
		$cur_act = (isset($_REQUEST['act']) ? $_REQUEST['act'] : '');
		
		// More actions... based on what they're doing in the admin panel.
		$assoc_acts = array(
			'save' => 'self::Text_Save',
		);
		
		if (in_array($cur_act, array_keys($assoc_acts)))
			call_user_func($assoc_acts[$cur_act]);
		
	}
	
	public static function Text_Save()
	{
		global $smcFunc;
				
		$id = (int) $_REQUEST['id'];
		$text = $smcFunc['htmlspecialchars']($_REQUEST['body'],ENT_QUOTES);
		$title = $smcFunc['htmlspecialchars']($_REQUEST['pagetitle'],ENT_QUOTES);
		
		if (empty($title))
			fatal_error(parent::text('error_title'));

		// Update the page with the data
		$smcFunc['db_query']('', "UPDATE {db_prefix}teampage_cp SET body = '$text', name_page = '$title' WHERE id_page = " . $id . "  LIMIT 1");
			
		// Redirect to the pages section
		redirectexit('action=admin;area=teampage;sa=editpage;id='. $id. ';passed');	
		
	}
	public static function Text_Save2()
	{
		global $smcFunc;
		
		$id = (int) $_REQUEST['id'];
		$text = $smcFunc['htmlspecialchars']($_REQUEST['body'],ENT_QUOTES);
		$title = $smcFunc['htmlspecialchars']($_REQUEST['pagetitle'],ENT_QUOTES);
		
		if (empty($title))
			fatal_error(parent::text('error_title'));

		// Update the page with the data
		$smcFunc['db_query']('', "UPDATE {db_prefix}teampage_cp SET name_page = '$title' WHERE id_page = " . $id . "  LIMIT 1");
			
		// Redirect to the pages section
		redirectexit('action=admin;area=teampage;sa=editpage;id='. $id. ';passed');	
		
	}
	
	public static function Edit_AddGroup()
	{
		global $context, $smcFunc;

		$id = (isset($_REQUEST['group']) ? (int) $_REQUEST['group'] : '');
		$place = (isset($_REQUEST['place']) ? (int) $_REQUEST['place'] : '');
		$pageid = (int) $_REQUEST['id'];
	
		$found = false;
		foreach ($context['groups']['notactive'] as $sg)
		{
			if ($sg['id'] == $id)
			{
				$found = true;
				break;
			}
		}
	
		if (!$found)
			fatal_error('No mames');
	
		$query = $smcFunc['db_query']('', '
			SELECT MAX(roworder)
			FROM {db_prefix}teampage', array()
		);
	
		$row = $smcFunc['db_fetch_row']($query);
			$lastgroupid = $row[0] + 1;
	
		$smcFunc['db_free_result']($query);
	
		$smcFunc['db_insert']('ignore',
			'{db_prefix}teampage',
			array(
				'id_group' => 'int', 'place' => 'int', 'roworder' => 'int', 'id_page' => 'int',
			),
			array(
				'id_group' => $id, 'place' => $place, 'roworder' => $lastgroupid, 'id_page' => $pageid,
			),
			array('id_group')
		);
	 
		// All done here.
		redirectexit('action=admin;area=teampage;sa=editpage;id='. $_REQUEST['id']. ';passed');
		
	}
	
	public static function Edit_MoveGroup()
	{
		global $smcFunc;

		$id = (isset($_REQUEST['group']) ? (int) $_REQUEST['group'] : fatal_lang_error('no_access'));
		$direction = (isset($_REQUEST['direction']) ? $_REQUEST['direction'] : fatal_lang_error('no_access'));
		$pageid = (int) $_REQUEST['id'];
	
		if (!in_array($direction, array('up', 'down')))
			fatal_lang_error('no_access');
	
		$request = $smcFunc['db_query']('', '
			SELECT roworder, id_page
			FROM {db_prefix}teampage
			WHERE id_group = {int:group} AND id_page = {int:id_page}',
			array(
				'group' => $id,
				'id_page' => $pageid
			)
		);
	
		$row = $smcFunc['db_fetch_assoc']($request);
			$oldrow = $row['roworder'];
	
		$o = $row['roworder'];
	
		// Which way we going?
		if ($direction == 'up')
			$o--;
		else
			$o++;
	
		$smcFunc['db_free_result']($request);
	
		// Move aside!
		$query = $smcFunc['db_query']('', '
			SELECT id_group, roworder, id_page
			FROM {db_prefix}teampage
			WHERE roworder = {int:order} AND id_page = {int:id_page}',
			array(
				'order' => $o,
				'id_page' => $pageid
			)
		);
	
		if ($smcFunc['db_affected_rows']() == 0)
			die('Nothing here...');
	
		$recd = $smcFunc['db_fetch_assoc']($query);
	
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}teampage
			SET roworder = {int:old_row}
			WHERE id_group = {int:id_group} AND id_page = {int:id_page}',
			array(
				'id_group' => $recd['id_group'],
				'old_row' => $oldrow,
				'id_page' => $pageid
			)
		);
	
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}teampage
			SET roworder = {int:order}
			WHERE id_group = {int:id_group} AND id_page = {int:id_page}',
			array(
				'order' => $o,
				'id_group' => $id,
				'id_page' => $pageid
			)
		);
	
		$smcFunc['db_free_result']($query);
	 
		// All done here.
		redirectexit('action=admin;area=teampage;sa=editpage;id='. $_REQUEST['id']. ';passed');
		
	}
	
	public static function Edit_SwapGroup()
	{
		global $context, $smcFunc;

		$id = (isset($_REQUEST['group']) ? (int) $_REQUEST['group'] : fatal_lang_error('no_access'));
		$place = (isset($_REQUEST['place']) ? (int) $_REQUEST['place'] : fatal_lang_error('no_access'));
		$pageid = (int) $_REQUEST['id'];
	
		$found = false;
		foreach ($context['groups']['all'] as $group)
		{
			if ($group['id'] == $id)
			{
				$found = true;
				break;
			}
		}
	
		if (!$found)
			fatal_lang_error('error_group_bad');
	 
		if (empty($place))
		{
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}teampage
				WHERE id_group = {int:requested_group} AND id_page = {int:id_page}
				LIMIT 1',
				array(
					'requested_group' => $id,
					'id_page' => $pageid,
				)
			);
	
			$query = $smcFunc['db_query']('', '
				SELECT id_group, id_page
				FROM {db_prefix}teampage
				ORDER BY roworder', array()
			);
	
			$groups = array();
			while ($row = $smcFunc['db_fetch_assoc']($query))
				$groups[] = array('id_group' => $row['id_group'], 'id_page' => $row['id_page']);
	
			$smcFunc['db_free_result']($query);
			
			$roworder = 0;
			foreach ($groups as $id => $data)
			{
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}teampage
					SET roworder = {int:roworder}
					WHERE id_group = {int:id_group} AND id_page = {int:id_page}',
					array(
						'id_group' => $data['id_group'],
						'roworder' => $roworder,
						'id_page' => $data['id_page'],
					)
				);
				$roworder++;	
			}
	
			// Move out
			redirectexit('action=admin;area=teampage;sa=editpage;id='. $_REQUEST['id']. ';del');
		}
		elseif (in_array($place, array(1, 2, 3)))
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}teampage
				SET place = {int:requested_place}
				WHERE id_group = {int:requested_group} AND id_page = {int:id_page}
				LIMIT 1',
				array(
					'requested_place' => $place,
					'requested_group' => $id,
					'id_page' => $pageid,
				)
			);
		}
		else
			fatal_lang_error('no_access');
	 
		// All done here.
		redirectexit('action=admin;area=teampage;sa=editpage;id='. $_REQUEST['id']. ';passed');
		
	}

}
