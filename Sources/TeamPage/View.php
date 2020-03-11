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

class View
{
	public  static $table = 'members AS mem';
	private static $columns = ['mem.id_member', 'mem.member_name', 'mem.real_name', 'mem.id_group', 'mem.additional_groups', 'mem.posts', 'mem.date_registered', 'mem.last_login', 'mem.personal_text', 'mem.website_title', 'mem.website_url', 'mem.usertitle', 'mem.avatar'];
	private static $attachments = ['a.id_attach', 'a.filename', 'a.attachment_type'];
	private static $additional_query = 'WHERE mem.id_group = ';
	private static $list = [];
	private static $tabs = [];

	public static function Main()
	{
		global $context, $txt, $scripturl, $modSettings;

		// Language, template and css
		loadTemplate('TeamPage');
		loadLanguage('TeamPage/');
		loadCSSFile('tempage.css', ['default_theme' => true]);

		// Main details
		$context['page_title'] = $context['forum_name'] . ' - ' . $txt['TeamPage_main_button'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=team',
			'name' => $txt['TeamPage_main_button'],
		);
		$context['sub_template'] = 'teampage_view';
		self::$list = Helper::Get(0, 10000, 'cp.page_order ASC', Pages::$table . ' AS cp', Pages::$columns, '');


		// What if the Shop is disabled? User shouldn't be able to access the Shop
		if (empty($modSettings['TeamPage_enable']))
			fatal_error($txt['TeamPage_error_disabled'], false);

		// Check if user has permission then
		isAllowedTo('teampage_canAccess');

		// Lovely copyright in pages
		$context['teampage']['copyright'] = TeamPage::Credits();

		// We got at least one page?
		if (!empty(self::$list)) {
			$context['teampage_tabs'] = self::Tabs();
			self::Load();
		}
		else
		{
			$context['teampage_title'] = 'nada';
		}
	}

	public static function Tabs()
	{
		global $context;

		// Loop thru the items
		foreach(self::$list as $id => $page)
			self::$tabs[$page['page_action']] = [
				'id_page' => $page['id_page'],
				'page_order' => $id,
				'page_name' => $page['page_name'],
				'page_action' => $page['page_action'],
				'page_type' => $page['page_type'],
				'page_body' => $page['page_body'],
				'is_text' => $page['is_text'],
			];

		return self::$tabs;
	}

	public static function Load()
	{
		global $txt, $context, $modSettings, $scripturl;

		// Obatain the page ID
		$page_details = (isset($_REQUEST['sa']) && !empty($_REQUEST['sa']) && !empty(Helper::Find(Pages::$table . ' AS cp', 'cp.page_action', $_REQUEST['sa'])) ? self::$tabs[$_REQUEST['sa']] : self::$list[0]);

		// Details
		$context['teampage']['page_id'] = $page_details['id_page'];
		$context['teampage']['is_text'] = $page_details['is_text'];
		$context['teampage_title'] = $page_details['page_name'];
		$context['page_title'] .= ' - ' . $page_details['page_name'];
		$context['teampage']['moderators'] = [];
		$context['teampage']['groups'] = [];
		$context['teampage']['members'] = [];

		// Linktree
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=team;sa=' . $page_details['page_action'],
			'name' => $page_details['page_name'],
		);

		// Text type?
		if (empty($page_details['is_text']))
		{
			// Groups
			if ($page_details['page_type'] == 'Groups')
			{
				$context['teampage']['groups'] = !empty(Helper::Find(Groups::$table . ' AS tp', 'tp.id_page', $page_details['id_page'])) ? Groups::PageSort($page_details['id_page']) : [];

				//self::$columns = array_merge(self::$columns, self::$attachments);
				$context['teampage']['members'] = Helper::Nested('mem.id_member', 'members AS mem', Groups::$groups_columns, self::$columns, 'members', 'WHERE m.id_group IN (' . implode(',', !empty($context['teampage']['groups']['all']) ? $context['teampage']['groups']['all'] : [0]).')', 'LEFT JOIN {db_prefix}membergroups AS m ON (m.id_group = mem.id_group) LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', self::$attachments);

				// We got groups
				if (!empty($context['teampage']['groups']))
				{
					$context['teampage']['team'] = [];
					// Populate the users into the groups
					foreach ($context['teampage']['groups'] as $placement => $groups)
					{
						// Just specific placement
						if ($placement == 'all')
							continue;

						// Asign each group where it belongs
						foreach ($groups as $group => $data)
							if (!empty($context['teampage']['members'][$group]))
								$context['teampage']['team'][$placement][$group] = $context['teampage']['members'][$group];
					}

					// Some wacky strings
					if (empty($context['teampage']['team']['left']))
						$grid_area = 'right right';
					elseif (empty($context['teampage']['team']['right']))
						$grid_area = 'left left';

					// Black magic allowed for once
					if (empty($context['teampage']['team']['left']) || empty($context['teampage']['team']['right']))
						addInlineCss(
							'#tp_main_box {
								grid-template-areas:
									"' . $grid_area. '"
									"bottom bottom";
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
					
					// We didn't have any members? oof
					if (empty($context['teampage']['team']))
						redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);
				}
				else
					redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);
			}

			// Moderators
			if ($page_details['page_type'] == 'Mods')
			{

				// We got mods
				if (!empty(Helper::Find('moderators', 'id_board')))
				{
					// Sort the users
					$context['teampage']['moderators'] = Helper::Nested('md.id_member', 'moderators AS md', array_merge(Moderators::$boards_columns, Moderators::$mods_columns), self::$columns, 'members', '', 'LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = md.id_member) LEFT JOIN {db_prefix}boards AS b ON (b.id_board = md.id_board) LEFT JOIN {db_prefix}attachments as a ON (a.id_member = mem.id_member)', self::$attachments);
				}
				else
					redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);
			}
		}
		// Text
		else {
			// We got the body?
			if (!empty($page_details['page_body']))
			{
				if ($page_details['page_type'] == 'HTML')
					$context['teampage']['body'] = un_htmlspecialchars($page_details['page_body']);
				else
					$context['teampage']['body'] = parse_bbc($page_details['page_body']);
			}
			// Let's go to the admin then...
			else
				redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);
		}
	}
}