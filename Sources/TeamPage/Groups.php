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

class Groups
{
	public  static $table = 'teampage_groups';
	private static $columns = ['tp.id_group', 'tp.id_page', 'tp.placement', 'tp.position'];
	public  static $groups_columns = ['m.id_group', 'm.group_name', 'm.description', 'm.online_color', 'm.icons'];
	private static $additional_query = '';
	private static $groups = [];
	private static $fields_data = [];
	private static $fields_insert = [];
	private static $fields_update = [];

	public static function PageSort($id)
	{
		global $context;

		// Set the ID
		self::$additional_query .= 'WHERE tp.id_page = "'. (int) $id. '"';

		// Get the groups for this page
		$context['page_groups_all'] = Helper::Get(0, 10000, 'tp.position ASC', self::$table . ' AS tp', array_merge(self::$columns, self::$groups_columns), self::$additional_query, false, 'LEFT JOIN {db_prefix}membergroups AS m ON (tp.id_group = m.id_group)');

		// ... Alright
		self::$groups['all'] = [];
		foreach ($context['page_groups_all'] as $group)
		{
			// All
			self::$groups['all'][] += $group['id_group'];
			// Left
			if ($group['placement'] == 'left')
				self::$groups['left'][$group['id_group']] = $group;
			// Right
			elseif ($group['placement'] == 'right')
				self::$groups['right'][$group['id_group']] = $group;
			// Bottom
			else
				self::$groups['bottom'][$group['id_group']] = $group;
		}

		return self::$groups;
	}

	public static function Save()
	{
		global $context, $txt;

		// Page info
		$context['sub_template'] = 'pages_edit';
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_groups'];

		// Unlucky
		if (!isset($_REQUEST['page']) || empty($_REQUEST['page']) || empty(Helper::Find(Pages::$table . ' AS cp', 'cp.id_page', $_REQUEST['page'])))
			die;

		// Define our vars
		self::$groups = isset($_REQUEST['groups']) ? $_REQUEST['groups'] : [];
		self::$fields_data = [];
		self::$fields_insert = [];
		self::$fields_update = [];

		// We are sorting!!
		if (!isset($_REQUEST['delete']) || empty($_REQUEST['delete']))
		{
			// Data
			foreach (self::$groups as $position => $group)
				self::$fields_data[$position] = [
					'id_group' => (int) $group,
					'id_page' => (int) $_REQUEST['page'],
					'placement' => (string) $_REQUEST['placement'],
					'position' => (int) $position,
				];

			// Type for insert
			foreach(self::$fields_data as $group) {
				self::$fields_update[$group['position']] = '';
				foreach($group as $column => $type) {
					self::$fields_insert[$group['position']][$column] = str_replace('integer', 'int', gettype($type));
					self::$fields_update[$group['position']] .= $column . ' = {'.str_replace('integer', 'int', gettype($type)).':'.$column.'}, ';
				}
			}
		
			// Update!
			foreach(self::$fields_data as $group) {
				Helper::Insert(self::$table, self::$fields_data[$group['position']], self::$fields_insert[$group['position']]);
				Helper::Update(self::$table . ' AS tp', self::$fields_data[$group['position']], self::$fields_update[$group['position']], 'WHERE tp.id_group = ' . self::$fields_data[$group['position']]['id_group'] . ' AND tp.id_page = ' . self::$fields_data[$group['position']]['id_page']);
			}
		}
		// We are deleting this group!
		else
			self::Delete(self::$groups, ' AND id_page = ' . $_REQUEST['page']);

		// Exit
		die;
	}

	public static function Delete($delete_groups, $query = '')
	{
		// sooo basically delete the groups from team page as well
		Helper::Delete(self::$table, 'id_group', $delete_groups, $query);
	}
}