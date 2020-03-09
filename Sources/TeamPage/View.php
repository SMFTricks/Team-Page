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
	private static $columns = ['mem.id_member', 'mem.member_name', 'mem.real_name AS name', 'mem.id_group', 'mem.additional_groups', 'mem.posts', 'mem.date_registered', 'mem.last_login', 'mem.personal_text', 'mem.website_title', 'mem.website_url', 'mem.usertitle'];
	public  static $groups_columns = ['mem.group_name', 'mem.description', 'mem.online_color', 'mem.icons', 'mem.id_group'];
	private static $additional_columns = 'LEFT JOIN {db_prefix}membergroups AS mem ON (tp.id_group = mem.id_group)';
	private static $additional_query = 'WHERE mem.id_group = ';
	private static $list = [];
	private static $tabs = [];

	public static function Main()
	{
		global $context, $txt;

		// Language and template
		loadTemplate('TeamPage');
		loadLanguage('TeamPage/');

		// Load the sort scripts and cute css :P
		loadCSSFile('tempage.css', ['default_theme' => true]);

		// Main details
		$context['page_title'] = $context['forum_name'] . ' - ' . $txt['TeamPage_main_button'];
		$context['sub_template'] = 'teampage_view';
		self::$list = Helper::Get(0, 10000, 'cp.page_order ASC', Pages::$table . ' AS cp', Pages::$columns, '');

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
		global $txt, $context, $modSettings;

		// Obatain the page ID
		$page_details = (isset($_REQUEST['sa']) && !empty($_REQUEST['sa']) && !empty(Helper::Find(Pages::$table . ' AS cp', 'cp.page_action', $_REQUEST['sa'])) ? self::$tabs[$_REQUEST['sa']] : self::$list[0]);

		// Details
		$context['teampage']['page_id'] = $page_details['id_page'];
		$context['teampage_title'] = $page_details['page_name'];
		$context['page_title'] .= ' - ' . $page_details['page_name'];
		$context['teampage']['moderators'] = [];
		$context['teampage']['groups'] = [];
		$context['teampage']['members'] = [];

		// Text type?
		if (empty($page_details['is_text']))
		{
			// Groups
			if ($page_details['page_type'] == 'Groups')
			{
				$context['teampage']['groups'] = !empty(Helper::Find(Groups::$table . ' AS tp', 'tp.id_page', $page_details['id_page'])) ? Groups::PageSort($page_details['id_page']) : [];

				// We got groups
				if (!empty($context['teampage']['groups']))
				{
					// Populate the users into the groups
					foreach ($context['teampage']['groups']['all'] as $group)
						$context['teampage']['members'][$group] = Helper::Get(0, 100000, 'mem.member_name ASC', self::$table, self::$columns, self::$additional_query . $group . (!empty($modSettings['TeamPage_additional_groups']) ? ' OR FIND_IN_SET('.$group.', mem.additional_groups)' : ''));

					// Some wacky strings
					if (empty($context['teampage']['groups']['left']))
						$grid_area = 'right right';
					elseif (empty($context['teampage']['groups']['right']))
						$grid_area = 'left left';

					// Black magic allowed for once
					if (empty($context['teampage']['groups']['left']) || empty($context['teampage']['groups']['right']))
						addInlineCss(
							'#tp_main_box {
								grid-template-areas:
									"' . $grid_area. '"
									"bottom bottom";
							}'
						);
				}
				else
					redirectexit('action=admin;area=teampage;sa=edit;id='.$page_details['id_page']);
			}

			// Moderators
			if ($page_details['page_type'] == 'Mods')
			{
				// We got mods
				if (!empty($context['teampage']['moderators']))
				{

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