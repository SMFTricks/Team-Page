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

class View
{
	public $table = 'members AS mem';
	private $columns = ['mem.id_member', 'mem.member_name', 'mem.real_name', 'mem.id_group', 'mem.additional_groups', 'mem.posts', 'mem.date_registered', 'mem.last_login', 'mem.personal_text', 'mem.website_title', 'mem.website_url', 'mem.usertitle', 'mem.avatar'];
	private $attachments = ['a.id_attach', 'a.filename', 'a.attachment_type'];
	private $list = [];
	private $tabs = [];

	/**
	 * @var object The groups
	 */
	private $_groups;

	public function Main()
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
		$this->list = Helper::Get(0, 10000, 'cp.page_order ASC', Pages::$table . ' AS cp', Pages::$columns);

		// Team Page enabled?
		if (empty($modSettings['TeamPage_enable']))
			fatal_lang_error('TeamPage_error_disabled', false);

		// Check if user has permission then
		isAllowedTo('teampage_canAccess');

		// Lovely copyright in pages
		$context['teampage']['copyright'] = TeamPage::Credits();

		// any pages to show?
		if (empty($this->list))
			redirectexit('action=admin;area=teampage;sa=pages');

		// Setup the tabs
		$context['teampage_tabs'] = $this->Tabs();
		$context['template_layers'][] = 'TeamPage';

		// Load the page
		$this->Load();
	}

	public function Tabs()
	{
		// Loop thru the items
		foreach($this->list as $id => $page)
		{
			$this->tabs[$page['page_action']] = [
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

		return $this->tabs;
	}

	public function Load()
	{
		global $context, $scripturl, $modSettings;

		// Get the correct page details, or the first found
		$page_details = !empty($this->tabs[isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '']) && isset($_REQUEST['sa']) ? $this->tabs[$_REQUEST['sa']] : $this->tabs[array_key_first($this->tabs)];
		
		// Check if there are actual details?
		if (empty($page_details))
			fatal_lang_error('TeamPage_page_noexist', false);

		// Details
		$context['teampage']['page_id'] = $page_details['id_page'];
		$context['teampage_title'] = $page_details['page_name'];
		$context['page_title'] .= ' - ' . $page_details['page_name'];
		$context['teampage']['page_type'] = $page_details['page_type'];
		$context['teampage']['moderators'] = [];
		$context['teampage']['groups'] = [];
		$context['teampage']['members'] = [];
		$this->_groups = new Groups();

		// Linktree
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=team;sa=' . $page_details['page_action'],
			'name' => $page_details['page_name'],
		);

		// Groups
		if ($page_details['page_type'] === 'Groups')
		{
			// Get the information from this page
			$context['teampage']['groups'] = $this->_groups->PageSort($page_details['id_page']);

			// Get the actual members
			$context['teampage']['members'] = Helper::Nested('mem.id_member',
				'members AS mem', $this->_groups->groups_columns, $this->columns, 'members',
				'WHERE m.id_group IN ({array_int:groups})',
				'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = mem.id_group)
				LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)',
				$this->attachments,
				[
					'groups' => !empty($context['teampage']['groups']['all']) ? $context['teampage']['groups']['all'] : [0]
				]
			);

			// Check if there are any groups
			if (empty($context['teampage']['groups']))
				redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);

			// Get additional groups
			if (!empty($modSettings['TeamPage_show_members_ag']))
			{
				$context['teampage']['ag_members'] = [];
				foreach ($context['teampage']['groups']['all'] as $group)
				{
					$context['teampage']['ag_members'][$group] = Helper::Nested('mem.id_member',
						'members AS mem', $this->_groups->groups_columns, $this->columns, 'members',
						'WHERE FIND_IN_SET({int:group}, mem.additional_groups)',
						'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = mem.id_group)
						LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)',
						$this->attachments,
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
						$context['teampage']['members'][$id_group]['members'] = isset($context['teampage']['members'][$id_group]['members']) ? array_merge($context['teampage']['members'][$id_group]['members'], $group['members']) : $group['members'];
				}
			}

			// Load the custom fields
			if (!empty($modSettings['TeamPage_show_custom_fields']))
			{
				// Get all the members
				$cust_members = [];
				foreach ($context['teampage']['members'] as $team_group)
				{
					foreach($team_group['members'] as $team_member)
					{
						$cust_members[] = $team_member['id_member'];
					}
				}

				// Get the custom fields
				$search_custom_fields = json_decode($modSettings['TeamPage_show_custom_fields'], true);
				$search_custom_fields[] = 'team_page_dummy';
				$custom_fields = loadMemberCustomFields($cust_members, $search_custom_fields);

				// Add the custom field to the users
				foreach ($context['teampage']['members'] as $id_group => $team_group)
				{
					foreach($team_group['members'] as $id_member => $team_member)
					{
						if (in_array($id_member, array_keys($custom_fields)))
							$context['teampage']['members'][$id_group]['members'][$id_member]['custom_fields'] = $custom_fields[$id_member];
					}
				}
			}

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
					#tp_block_right ul,
					#tp_block_left ul {
						display: flex;
					}
					#tp_block_left ul li,
					#tp_block_right ul li {
						width: 50%;
					}
					#tp_block_right ul li:nth-child(odd),
					#tp_block_left ul li:nth-child(odd) {
						margin-right: 10px;
					}'
				);
			}
		}
		// Moderators
		elseif ($page_details['page_type'] === 'Mods')
		{
			// Check for boards available
			if (empty($page_details['page_boards']))
				redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);

			// Sort by boards
			if (!empty($page_details['mods_style']))
			{
				$context['teampage']['moderators'] = Helper::Nested('md.id_member',
					'moderators AS md', array_merge(Moderators::$boards_columns, Moderators::$mods_columns), $this->columns, 'members',
					'WHERE b.id_board IN ({array_int:page_boards})',
					'LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = md.id_member)
					LEFT JOIN {db_prefix}boards AS b ON (b.id_board = md.id_board)
					LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', $this->attachments,
					['page_boards' => explode(',', $page_details['page_boards'])]
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
				$context['teampage']['moderators'][3]['members'] =  Helper::Nested('md.id_member',
					'moderators AS md', $this->columns, array_merge(Moderators::$boards_columns, Moderators::$mods_columns), 'boards',
					'WHERE b.id_board IN ({array_int:page_boards})',
					'LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = md.id_member)
					LEFT JOIN {db_prefix}boards AS b ON (b.id_board = md.id_board)
					LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', $this->attachments,
					['page_boards' => explode(',', $page_details['page_boards'])],
					true
				);
			}
		}
		// Text
		else
		{
			// Check for the body
			if (empty($page_details['page_body']))
				redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);

			// BBC
			$context['teampage']['body'] = parse_bbc($page_details['page_body']);
		}
	}
}