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

class Groups
{
	public  static $table = 'teampage_groups';
	private static $columns = ['tp.id_group', 'tp.id_page', 'tp.placement', 'tp.position'];
	public  static $groups_columns = ['mem.group_name', 'mem.description', 'mem.online_color', 'mem.icons', 'mem.id_group'];
	private static $additional_columns = 'LEFT JOIN {db_prefix}membergroups AS mem ON (tp.id_group = mem.id_group)';
	private static $additional_query = '';
	private static $groups;

	public static function PageSort($id)
	{
		global $context;

		// Set the ID
		self::$additional_query .= 'WHERE tp.id_page = "'. (int) $id. '"';
		self::$groups = [];

		// Get the groups for this page
		$context['page_groups_all'] = Helper::Get(0, 10000, 'tp.position ASC', self::$table . ' AS tp', array_merge(self::$columns, self::$groups_columns), self::$additional_query, false, self::$additional_columns);
		self::$groups['all'] = [];
		foreach ($context['page_groups_all'] as $group)
		{
			self::$groups['all'][] += $group['id_group'];
			if ($group['placement'] == 'left')
				self::$groups['left'][$group['id_group']] = $group;
			elseif ($group['placement'] == 'right')
				self::$groups['right'][$group['id_group']] = $group;
			else
				self::$groups['bottom'][$group['id_group']] = $group;
		}

		return self::$groups;
	}
}