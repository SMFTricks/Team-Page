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
	public $table = 'teampage_groups';
	public $columns = ['tp.id_group', 'tp.id_page', 'tp.placement', 'tp.position'];
	public $groups_columns = ['m.id_group', 'm.group_name', 'm.description', 'm.online_color', 'm.icons'];
	private $groups = [];
	private $fields_data = [];
	private $fields_insert = [];
	private $fields_update = [];

	public function PageSort($id)
	{
		global $context;

		// Page id?
		if (empty($id))
			return false;

		// Get the groups for this page
		$context['page_groups_all'] = Helper::Get(0, 10000, 'tp.position ASC', $this->table . ' AS tp', array_merge($this->columns, $this->groups_columns), 'WHERE tp.id_page = {int:page}', false, 'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = tp.id_group)', ['page' => (int) $id]);

		// ... Alright
		$this->groups['all'] = [];
		foreach ($context['page_groups_all'] as $group)
		{
			// Empty?
			if (empty($group['id_group']))
				continue;

			// All
			$this->groups['all'][] += $group['id_group'];
			// Left
			if ($group['placement'] == 'left')
				$this->groups['left'][$group['id_group']] = $group;
			// Right
			elseif ($group['placement'] == 'right')
				$this->groups['right'][$group['id_group']] = $group;
			// Bottom
			else
				$this->groups['bottom'][$group['id_group']] = $group;
		}

		return $this->groups;
	}

	public function Save()
	{
		global $context, $txt;

		// Page info
		$context['sub_template'] = 'pages_edit';
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_groups'];

		// Unlucky
		if (!isset($_REQUEST['page']) || empty($_REQUEST['page']) || empty(Helper::Get('', '', '', Pages::$table . ' AS cp', ['cp.id_page'], 'WHERE cp.id_page = {int:page}', true, '', ['page' => (int) $_REQUEST['page']])))
			die;

		// Define our vars
		$this->groups = isset($_REQUEST['groups']) ? $_REQUEST['groups'] : [];
		$this->fields_data = [];
		$this->fields_insert = [];
		$this->fields_update = [];

		// We are sorting!!
		if (!isset($_REQUEST['delete']) || empty($_REQUEST['delete']))
		{
			// Data
			foreach ($this->groups as $position => $group)
			{
				// No ghost groups
				if (empty($group))
					continue;

				$this->fields_data[$position] = [
					'id_group' => (int) $group,
					'id_page' => (int) $_REQUEST['page'],
					'placement' => (string) $_REQUEST['placement'],
					'position' => (int) $position,
				];
			}

			// Type for insert
			foreach($this->fields_data as $group)
			{
				$this->fields_update[$group['position']] = '';
				foreach($group as $column => $type) {
					$this->fields_insert[$group['position']][$column] = str_replace('integer', 'int', gettype($type));
					$this->fields_update[$group['position']] .= $column . ' = {'.str_replace('integer', 'int', gettype($type)).':'.$column.'}, ';
				}
			}
		
			// Update!
			foreach($this->fields_data as $group)
			{
				Helper::Insert($this->table, $this->fields_data[$group['position']], $this->fields_insert[$group['position']], 'replace', ['id_group', 'id_page']);
				Helper::Update($this->table . ' AS tp', $this->fields_data[$group['position']], $this->fields_update[$group['position']], 'WHERE tp.id_group = {int:id_group}
				AND tp.id_page = {int:id_page}');
			}
		}
		// We are deleting this group!
		else
			$this->groupsDelete($this->groups, $_REQUEST['page']);

		// Exit
		die;
	}

	public function groupsDelete(&$groups, $page)
	{
		// Make sure the groups are integer, in case we have an unexpected guest.
		foreach ($groups as $position => $group)
			$groups[$position] = (int) $group;
		
		// Delete
		Helper::Delete($this->table, 'id_group', $groups, ' AND id_page = {int:page}', ['page' => (int) $page]);
	}

	public function Delete($delete_groups)
	{
		// sooo basically delete the groups from team page as well
		Helper::Delete($this->table, 'id_group', $delete_groups);
	}
}