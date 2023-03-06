<?php

/**
 * @package Team Page
 * @version 5.4
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace TeamPage;

if (!defined('SMF'))
	die('No direct access...');

class View
{
	/**
	 * @var array The member columns
	 */
	private $_columns = ['mem.id_member', 'mem.member_name', 'mem.real_name', 'mem.id_group', 'mem.additional_groups', 'mem.posts', 'mem.date_registered', 'mem.last_login', 'mem.personal_text', 'mem.website_title', 'mem.website_url', 'mem.usertitle', 'mem.avatar'];

	/**
	 * @var array Attachments columns
	 */
	private $_attachments = ['a.id_attach', 'a.filename', 'a.attachment_type'];

	/**
	 * @var array The pages
	 */
	private $_list = [];

	/**
	 * @var array Tabs for the navigation
	 */
	private $_tabs = [];

	/**
	 * @var object The groups
	 */
	private $_groups;

	/**
	 * @var array The page details
	 */
	private $_page_details = [];

	/**
	 * @var array The members id's
	 */
	private $_cust_members = [];

	/**
	 * @var string The sorting method
	 */
	private $_sorting_method;

	/**
	 * View:Main()
	 * 
	 * Loads the essential files and setup
	 * 
	 * @return void
	 */
	public function Main() : void
	{
		global $context, $txt, $scripturl, $modSettings;

		// Language, template and css
		loadTemplate('TeamPage');
		loadLanguage('TeamPage/');
		loadCSSFile('teampage.css', ['default_theme' => true, 'minimize' => false]);

		// Main details
		$context['page_title'] = $context['forum_name'] . ' - ' . $txt['TeamPage_main_button'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=team',
			'name' => $txt['TeamPage_main_button'],
		);
		$context['sub_template'] = 'teampage_view';

		// Load the pages
		$this->_list = Helper::Get(0, 10000, 'cp.page_order ASC',
			Pages::$table . ' AS cp',
			Pages::$columns
		);

		// Team Page enabled?
		if (empty($modSettings['TeamPage_enable']))
			fatal_lang_error('TeamPage_error_disabled', false);

		// Check if user has permission then
		isAllowedTo('teampage_canAccess');

		// Lovely copyright in pages
		$context['teampage']['copyright'] = $context['instances']['TeamPage\TeamPage']->Credits();

		// any pages to show?
		if (empty($this->_list))
			redirectexit('action=admin;area=teampage;sa=pages');

		// Setup the tabs
		$context['teampage_tabs'] = $this->Tabs();
		$context['template_layers'][] = 'TeamPage';

		// Load the page
		$this->Load();
	}

	/**
	 * View:Tabs()
	 * 
	 * Builds the navigation array for the pages
	 * 
	 * @return array The tabs
	 */
	public function Tabs() : array
	{
		// Loop thru the items
		foreach($this->_list as $id => $page)
		{
			$this->_tabs[$page['page_action']] = [
				'id_page' => $page['id_page'],
				'page_order' => $id,
				'page_name' => $page['page_name'],
				'page_action' => $page['page_action'],
				'page_type' => $page['page_type'],
				'page_body' => $page['page_body'],
				'page_boards' => $page['page_boards'],
				'mods_style' => $page['mods_style'],
			];
		}

		return $this->_tabs;
	}

	/**
	 * View:Load()
	 * 
	 * Loads the pages and populates the groups with the members
	 * 
	 * @return void
	 */
	public function Load() : void
	{
		global $context, $scripturl, $modSettings;

		// Get the correct page details, or the first found
		$this->_page_details = !empty($this->_tabs[isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '']) && isset($_REQUEST['sa']) ? $this->_tabs[$_REQUEST['sa']] : $this->_tabs[array_key_first($this->_tabs)];
		
		// Check if there are actual details?
		if (empty($this->_page_details))
			fatal_lang_error('TeamPage_page_noexist', false);

		// Details
		$context['teampage']['page_id'] = $this->_page_details['id_page'];
		$context['teampage_title'] = $this->_page_details['page_name'];
		$context['page_title'] .= ' - ' . $this->_page_details['page_name'];
		$context['teampage']['page_type'] = $this->_page_details['page_type'];
		$context['teampage']['moderators'] = [];
		$context['teampage']['groups'] = [];
		$context['teampage']['members'] = [];

		// Linktree
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=team;sa=' . $this->_page_details['page_action'],
			'name' => $this->_page_details['page_name'],
		);

		// Grouping
		$this->_groups = new Groups();

		// Sorting method
		$this->_sorting_method = (empty($modSettings['TeamPage_sort_by']) ? 'id_member' : 'real_name ASC');

		// Groups
		$this->view_groups();
	
		// Moderators
		$this->view_moderators();

		// Text
		if ($this->_page_details['page_type'] !== 'Mods' && $this->_page_details['page_type'] !== 'Groups')
		{
			if (empty($this->_page_details['page_body']))
				redirectexit('action=admin;area=teampage;sa=edit;id='.$this->_page_details['id_page']);

			// BBC
			$context['teampage']['body'] = parse_bbc($this->_page_details['page_body']);
		}
	}

	/**
	 * View:Groups()
	 * 
	 * Groups page setup
	 * 
	 * @return void
	 */
	private function view_groups()
	{
		global $context;

		// Is this for groups?
		if ($this->_page_details['page_type'] !== 'Groups')
			return;

		// Get the information from this page
		$context['teampage']['groups'] = $this->_groups->PageSort($this->_page_details['id_page']);

		// Check if there are any groups
		if (empty($context['teampage']['groups']))
			redirectexit('action=admin;area=teampage;sa=edit;id='.$this->_page_details['id_page']);

		// Get the actual members
		$context['teampage']['members'] = Helper::Nested('mem.' . $this->_sorting_method,
			'members AS mem', $this->_groups->groups_columns, $this->_columns, 'members',
			'WHERE m.id_group IN ({array_int:groups})',
			'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = mem.id_group)
			LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)',
			$this->_attachments,
			[
				'groups' => !empty($context['teampage']['groups']['all']) ? $context['teampage']['groups']['all'] : [0]
			]
		);

		// Get additional groups
		$this->additional_groups();

		// Get all the member id's
		// This is done after additional groups, for good measure.
		foreach ($context['teampage']['members'] as $team_group)
		{
			if (empty($team_group['members']))
				continue;

			foreach($team_group['members'] as $team_member)
				$this->_cust_members[] = $team_member['id_member'];
		}

		// Load the custom fields
		$this->custom_fields();

		// We got groups
		$context['teampage']['team'] = [];
		// Populate the users into the groups
		foreach ($context['teampage']['groups'] as $placement => $groups)
		{
			// Just specific placement
			if ($placement == 'all')
				continue;

			// Asign each group where it belongs
			foreach ($groups as $group)
			{
				if (!empty($context['teampage']['members'][$group['id_group']]['members']))
					$context['teampage']['team'][$placement][$group['id_group']] = $context['teampage']['members'][$group['id_group']];
			}
		}

		// Some wacky things for css
		if (empty($context['teampage']['team']['left']))
			$grid_area = 'right right';
		elseif (empty($context['teampage']['team']['right']))
			$grid_area = 'left left';

		// Black magic allowed for once
		if (empty($context['teampage']['team']['left']) || empty($context['teampage']['team']['right']))
		{
			addInlineCss(
				'#tp_main_box {
					grid-template-areas:
						"' . $grid_area. '"
						"bottom bottom";
				}
				div:where(#team_block_left, #team_block_right) .team_members {
					grid-template-columns: 1fr 1fr;
				}
				div:where(#team_block_bottom) .team_members {
					grid-template-columns: 1fr 1fr 1fr;
				}'
			);
		}
	}

	/**
	 * View:additional_groups()
	 * 
	 * Include additional groups into the page
	 * 
	 * @return void
	 */
	private function additional_groups() : void
	{
		global $context, $modSettings;

		// Are we including additional groups?
		if (empty($modSettings['TeamPage_show_members_ag']))
			return;

		$context['teampage']['ag_members'] = [];
		foreach ($context['teampage']['groups']['all'] as $group)
		{
			$context['teampage']['ag_members'][$group] = Helper::Nested('mem.' . $this->_sorting_method,
				'members AS mem', $this->_groups->groups_columns, $this->_columns, 'members',
				'WHERE FIND_IN_SET({int:group}, mem.additional_groups)',
				'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = mem.id_group)
				LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)',
				$this->_attachments,
				[
					'group' => $group,
				]
			);

			// Query the group info if it's empty
			if (!isset($context['teampage']['members'][$group]))
			{
				$context['teampage']['members'][$group] = Helper::Get(0, 10000, 'm.id_group',
					'membergroups AS m', $this->_groups->groups_columns,
					'WHERE m.id_group = {int:group}', true, '',
					[
						'group' => $group,
					]
				);
			}
		}

		// Now, go through the members and add them to the main array
		foreach ($context['teampage']['ag_members'] as $id_group => $a_groups)
		{
			foreach ($a_groups as $group)
			{
				// Need to loop the next one too, to get the id of the member
				foreach($group['members'] as $id_member => $group_member)
				{
					// Don't need to add them if they are here already, somehow?
					if (isset($context['teampage']['members'][$id_group]['members'][$id_member]))
						continue;

					// Add them
					$context['teampage']['members'][$id_group]['members'][$id_member] = $group_member;
				}
			}
		}
	}

	/**
	 * View:custom_fields()
	 * 
	 * Load the custom fields
	 * 
	 * @return void
	 */
	private function custom_fields() : void
	{
		global $context, $modSettings;

		// Custom fields enabled?
		if (empty($modSettings['TeamPage_show_custom_fields']))
			return;

		// Get the custom fields
		$search_custom_fields = json_decode($modSettings['TeamPage_show_custom_fields'], true);
		$search_custom_fields[] = 'team_page_dummy';
		$custom_fields = loadMemberCustomFields($this->_cust_members, $search_custom_fields);

		// GROUPS - Add the custom fields to the users
		if ($this->_page_details['page_type'] === 'Groups')
		{
			foreach ($context['teampage']['members'] as $id_group => $team_group)
			{
				if (empty($team_group['members']))
					continue;
				foreach($team_group['members'] as $id_member => $team_member)
				{
					if (in_array($id_member, array_keys($custom_fields)))
						$context['teampage']['members'][$id_group]['members'][$id_member]['custom_fields'] = $custom_fields[$id_member];
				}
			}
		}
		// BOARDS - Add the custom fields to the users
		else
		{
			foreach($context['teampage']['moderators'] as $id_mod => $moderators)
			{
				foreach($moderators['members'] as $id_member => $team_member)
				{
					if (in_array($id_member, array_keys($custom_fields)))
						$context['teampage']['moderators'][$id_mod]['members'][$id_member]['custom_fields'] = $custom_fields[$id_member];
				}
			}
		}
	}

	/**
	 * View:view_moderators()
	 * 
	 * Moderators page setup
	 * 
	 * @return void
	 */
	private function view_moderators()
	{
		global $context;

		// Is this a moderators page?
		if ($this->_page_details['page_type'] !== 'Mods')
			return;

		// Check for boards available
		if (empty($this->_page_details['page_boards']))
			redirectexit('action=admin;area=teampage;sa=edit;id='.$this->_page_details['id_page']);

		// Sort by boards
		if (!empty($this->_page_details['mods_style']))
		{
			$context['teampage']['moderators'] = Helper::Nested('mem.' . $this->_sorting_method,
				'moderators AS md', array_merge(Moderators::$boards_columns, Moderators::$mods_columns), $this->_columns, 'members',
				'WHERE b.id_board IN ({array_int:page_boards})',
				'LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = md.id_member)
				LEFT JOIN {db_prefix}boards AS b ON (b.id_board = md.id_board)
				LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', $this->_attachments,
				[
					'page_boards' => explode(',', $this->_page_details['page_boards'])
				]
			);
		}
		// Sort by users
		else
		{
			// Moderator group
			// 3 because that's the id of the moderator group in SMF
			$context['teampage']['moderators'][3] = Helper::Get(0, 10000, 'm.group_name', 'membergroups AS m', $this->_groups->groups_columns, 'WHERE m.id_group = {int:group}', true, '', ['group' => 3]);

			// Add the name
			$context['teampage']['moderators'][3]['name'] = $context['teampage']['moderators'][3]['group_name'];

			// Moderators
			$context['teampage']['moderators'][3]['members'] =  Helper::Nested('mem.' . $this->_sorting_method,
				'moderators AS md', $this->_columns, array_merge(Moderators::$boards_columns, Moderators::$mods_columns), 'boards',
				'WHERE b.id_board IN ({array_int:page_boards})',
				'LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = md.id_member)
				LEFT JOIN {db_prefix}boards AS b ON (b.id_board = md.id_board)
				LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', $this->_attachments,
				[
					'page_boards' => explode(',', $this->_page_details['page_boards'])
				],
				true
			);
		}

		// Get all the members id's
		foreach($context['teampage']['moderators'] as $moderators)
		{
			foreach($moderators['members'] as $moderator)
				$this->_cust_members[] = $moderator['id_member'];
		}

		// Custom fields
		$this->custom_fields();
	}
}