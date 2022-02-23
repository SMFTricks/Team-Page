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

class Pages
{
	public static $table = 'teampage_pages';
	public static $columns = ['cp.id_page', 'cp.page_name', 'cp.page_action', 'cp.page_type', 'cp.page_body', 'cp.page_order', 'cp.page_boards', 'cp.mods_style'];
	public static $page_type = ['Groups', 'Mods', 'BBC', 'HTML'];
	private static $additional_query = '';
	private static $fields_data = [];
	private static $fields_type = [];

	public static function List()
	{
		global $context, $scripturl, $sourcedir, $modSettings, $txt;

		require_once($sourcedir . '/Subs-List.php');
		$context['template_layers'][] = 'pages_post';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'pageslist';
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_pages'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];
		$context['TeamPage_pages_title'] = $txt['TeamPage_add_page'];

		// The entire list
		$listOptions = [
			'id' => 'pageslist',
			'title' => $txt['TeamPage_page_pages'],
			'items_per_page' => 5,
			'base_href' => '?action=admin;area=teampage;sa=pages',
			'default_sort_col' => 'page_order',
			'get_items' => [
				'function' => __NAMESPACE__ . '\Helper::Get',
				'params' => [self::$table . ' AS cp', self::$columns],
			],
			'get_count' => [
				'function' => __NAMESPACE__ . '\Helper::Count',
				'params' => [self::$table . ' AS cp', self::$columns]
			],
			'no_items_label' => $txt['TeamPage_no_pages'],
			'no_items_align' => 'center',
			'columns' => [
				'name' => [
					'header' => [
						'value' => $txt['TeamPage_page_title'],
						'class' => 'lefttext',
					],
					'data' => [
						'sprintf' => [
							'format' => '<a href="'. $scripturl. '?action=team;sa=%1$s">%2$s</a>',
							'params' => [
								'page_action' => true,
								'page_name' => true,
							],
						],
						'style' => 'width: 8%',
						'class' => 'lefttext',
					],
					'sort' => [
						'default' => 'page_name DESC',
						'reverse' => 'page_name',
					]
				],
				'action' => [
					'header' => [
						'value' => $txt['TeamPage_page_subaction'],
						'class' => 'lefttext',
					],
					'data' => [
						'db' => 'page_action',
						'style' => 'width: 8%',
						'class' => 'lefttext',
					],
					'sort' => [
						'default' => 'page_action DESC',
						'reverse' => 'page_action',
					]
				],
				'details' => [
					'header' => [
						'value' => $txt['TeamPage_page_type'],
						'class' => 'lefttext',
					],
					'data' => [
						'db' => 'page_type',
						'db_htmlsafe' => true,
						'style' => 'width: 5%',
						'class' => 'lefttext',
					],
					'sort' => [
						'default' => 'page_type DESC',
						'reverse' => 'page_type',
					]
				],
				'page_order' => array(
					'header' => array(
						'value' => $txt['TeamPage_page_order'],
						'class' => 'lefttext',
					),
					'data' => array(
						'function' => function($row) {
							return '<input type="number" min="0" value="'. $row['page_order'].'" name="page_order['.$row['id_page'].']" title="page_order['.$row['id_page'].']" />';
						},
						'style' => 'width: 5%',
					),
					'sort' =>  array(
						'default' => 'page_order',
						'reverse' => 'page_order DESC',
					),
				),
				'modify' => [
					'header' => [
						'value' => $txt['TeamPage_page_modify'],
						'class' => 'centertext',
					],
					'data' => [
						'sprintf' => [
							'format' => '<a href="'. $scripturl. '?action=admin;area=teampage;sa=edit;id=%1$d">'. $txt['TeamPage_page_modify_short']. '</a>',
							'params' => [
								'id_page' => true,
							],
						],
						'style' => 'width: 5%',
						'class' => 'centertext',
					],
					'sort' => [
						'default' => 'id_page',
						'reverse' => 'id_page DESC',
					]
				],
				'delete' => [
					'header' => [
						'value' => $txt['delete'],
						'class' => 'centertext',
					],
					'data' => [
						'sprintf' => [
							'format' => '<a href="'. $scripturl. '?action=admin;area=teampage;sa=delete;id=%1$d">'. $txt['delete']. '</a>',
							'params' => [
								'id_page' => false,
							],
						],
						'class' => 'centertext',
						'style' => 'width: 3%',
					],
				],
			],
			'form' => [
				'href' => '?action=admin;area=teampage;sa=order',
				'hidden_fields' => [
					$context['session_var'] => $context['session_id'],
				],
				'include_sort' => true,
				'include_start' => true,
			],
			'additional_rows' => [
				'submit' => [
					'position' => 'below_table_data',
					'value' => '<input type="submit" size="18" value="'.$txt['TeamPage_page_save_order']. '" class="button" />',
				],
				'updated' => [
					'position' => 'top_of_list',
					'value' => (!isset($_REQUEST['deleted']) ? (!isset($_REQUEST['added']) ? (!isset($_REQUEST['updated']) ? '' : '<div class="infobox">'. $txt['TeamPage_pages_updated']. '</div>') : '<div class="infobox">'. $txt['TeamPage_pages_added']. '</div>') : '<div class="infobox">'. $txt['TeamPage_pages_deleted']. '</div>'),
				],
			],
		];
		// Let's finishem
		createList($listOptions);
	}

	public static function Delete()
	{
		global $txt;

		// Delete
		Helper::Delete(self::$table, 'id_page', $_REQUEST['id']);
		redirectexit('action=admin;area=teampage;sa=pages;deleted');
	}

	public static function Edit()
	{
		global $context, $scripturl, $sourcedir, $modSettings, $txt;

		// Load the sort script :P
		loadCSSFile('tempage.css', ['default_theme' => true]);
		loadJavaScriptFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', ['external' => true]);
		loadJavaScriptFile('teampage.js', ['default_theme' => true]);

		// Page information
		$where_query = 'WHERE cp.id_page = "'. (int) (isset($_REQUEST['id']) ? $_REQUEST['id'] : 0) . '"';
		$context['page_details'] = Helper::Get('', '', '', self::$table . ' AS cp', self::$columns, $where_query, true);
		$context[$context['admin_menu_name']]['current_subsection'] = 'pages';

		// We found a page
		if (empty($context['page_details']))
			fatal_error($txt['TeamPage_page_noexist'], false);

		// Text and BBC?
		if ($context['page_details']['page_type'] == 'BBC') {
			// Now create the editor.
			require_once($sourcedir . '/Subs-Editor.php');
			$editorOptions = array(
				'id' => 'page_body',
				'value' => !empty($context['page_details']['page_body']) ? $context['page_details']['page_body'] : '',
				'width' => '100%',
				'form' => 'page_post',
				'labels' => array(
					'post_button' => ''
				),
			);
			create_control_richedit($editorOptions);
			$context['page_details']['body_bbc'] = $editorOptions['id'];
		}

		// Groups
		if ($context['page_details']['page_type'] == 'Groups')
		{

			// Load the sort scripts and cute css :P
			loadCSSFile('teampage.css', ['default_theme' => true]);
			loadJavaScriptFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', ['external' => true]);
			loadJavaScriptFile('teampage.js', ['default_theme' => true]);

			// Fill the groups
			$context['page_groups'] = !empty(Helper::Find(Groups::$table . ' AS tp', 'tp.id_page', $_REQUEST['id'])) ? Groups::PageSort($_REQUEST['id']) : [];
			$context['forum_groups'] = Helper::Get(0, 10000, 'm.group_name', 'membergroups AS m', Groups::$groups_columns, 'WHERE m.min_posts = -1 AND m.id_group != 3 AND  m.id_group NOT IN (' . implode(',', !empty($context['page_groups']['all']) ? $context['page_groups']['all'] : [0]).')');
		}

		// Moderators
		if ($context['page_details']['page_type'] == 'Mods')
		{
			// Set the boards
			$context['page_details']['page_boards'] = explode(',', $context['page_details']['page_boards']);

			// Additional text
			loadLanguage('ManageMembers');
			// Load boards
			$context['forum_categories'] = Helper::Nested('b.board_order', 'boards AS b', Moderators::$cats_columns, Moderators::$boards_columns, 'boards', '', 'LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)');

			// Now, let's sort the list of categories into the boards for templates that like that.
			foreach ($context['forum_categories'] as $category)
				// Include a list of boards per category for easy toggling.
				$context['forum_categories'][$category['id_cat']]['child_ids'] = array_keys($category['boards']);
		}

		// Page details
		$context['template_layers'][] = 'pages_edit';
		$context['sub_template'] = 'pages_edit';
		$context['page_title'] = $txt['TeamPage']. ' - ' . sprintf($txt['TeamPage_pages_editing_page'], $context['page_details']['page_name']);
		$context['TeamPage_pages_title'] = $context['page_title'];
	}

	public static function Save()
	{
		global $smcFunc, $txt;

		// Data
		self::$fields_data = [
			'id_page' => (int) isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0,
			'page_name' => (string) isset($_REQUEST['title']) ? $smcFunc['htmlspecialchars']($_REQUEST['title'], ENT_QUOTES) : '',
			'page_action' => (string) isset($_REQUEST['page_action']) ? strtolower($smcFunc['htmlspecialchars']($_REQUEST['page_action'], ENT_QUOTES)) : '',
			'page_type' => (string) isset($_REQUEST['type']) && in_array($_REQUEST['type'], self::$page_type) ? $_REQUEST['type'] : $txt['TeamPage_page_type_groups'],
			'page_body' => (string) isset($_REQUEST['page_body']) ? $smcFunc['htmlspecialchars']($_REQUEST['page_body'], ENT_QUOTES) : '',
		];

		// Validate info
		self::Validate(self::$fields_data);
		checkSession();
		$status = 'updated';

		if (empty(self::$fields_data['id_page']))
		{
			// Type
			foreach(self::$fields_data as $column => $type)
				self::$fields_type[$column] = str_replace('integer', 'int', gettype($type));

			// Insert
			Helper::Insert(self::$table, self::$fields_data, self::$fields_type);
			$status = 'added';
		}
		else
		{
			self::$fields_type = '';
			
			// Remove those that don't require updating
			unset(self::$fields_data['page_type']);

			// Type
			foreach(self::$fields_data as $column => $type)
				self::$fields_type .= $column . ' = {'.str_replace('integer', 'int', gettype($type)).':'.$column.'}, ';

			// Update
			Helper::Update(self::$table, self::$fields_data, self::$fields_type, 'WHERE id_page = ' . self::$fields_data['id_page']);
		}

		redirectexit('action=admin;area=teampage;sa=pages;'.$status);
	}

	public static function Validate($data)
	{
		global $txt;

		// Empty name
		if (empty($data['page_name']) || empty($data['page_action']))
			fatal_error($txt['TeamPage_error_title_sub'], false);

		// Only lowercase and alphanumeric
		elseif (!ctype_alnum($data['page_action']))
			fatal_error($txt['TeamPage_error_alnum_sub'], false);

		// Duplicated action?
		if (empty($data['id_page']) && !empty(Helper::Find(self::$table . ' AS cp', 'cp.page_action', $data['page_action'])))
			fatal_error($txt['TeamPage_error_already_sub'], false);

		// Doesn't exist
		elseif (!empty($data['id_page']) && empty(Helper::Find(self::$table . ' AS cp', 'cp.id_page', $data['id_page'])))
			fatal_error($txt['TeamPage_page_noexist'], false);
	}

	public static function Order()
	{
		global $context, $txt;

		// Set all the page stuff
		$context['page_title'] = $txt['TeamPage']. ' - ' . $txt['TeamPage_page_pages'];

		// Sesh
		checkSession();

		// Update order
		if (!empty($_REQUEST['page_order']))
			foreach ($_REQUEST['page_order'] as $page => $order)
				Helper::Update(self::$table, ['id_page' => $page, 'page_order' => $order], 'page_order = {int:page_order}', 'WHERE id_page = {int:id_page}');
		
		redirectexit('action=admin;area=teampage;sa=pages;updated');
	}
}