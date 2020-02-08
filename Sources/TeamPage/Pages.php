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

class Pages
{
	private static $table = 'teampage_cp';
	private static $columns = ['cp.id_page', 'cp.page_name', 'cp.page_action', 'cp.is_text', 'cp.page_type', 'cp.page_body'];
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

		// The entire list
		$listOptions = [
			'id' => 'pageslist',
			'title' => $txt['TeamPage_page_pages'],
			'items_per_page' => 5,
			'base_href' => '?action=admin;area=teampage;sa=pages',
			'default_sort_col' => 'modify',
			'get_items' => [
				'function' => __NAMESPACE__ . '\Helper::Get',
				'params' => [self::$table . ' AS cp', self::$columns, self::$additional_query],
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
						'value' => $txt['TeamPage_page_details'],
						'class' => 'lefttext',
					],
					'data' => [
						'function' => function($row){ global $txt;

							return 'detalles bby';
						},
						'style' => 'width: 5%',
						'class' => 'lefttext',
					],
					'sort' => [
						'default' => 'is_text DESC',
						'reverse' => 'is_text',
					]
				],
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
						'default' => 'id_page DESC',
						'reverse' => 'id_page',
					]
				],
				'delete' => [
					'header' => [
						'value' => $txt['delete']. ' <input type="checkbox" onclick="invertAll(this, this.form, \'delete[]\');" class="input_check" />',
						'class' => 'centertext',
					],
					'data' => [
						'sprintf' => [
							'format' => '<input type="checkbox" name="delete[]" value="%1$d" class="check" />',
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
				'href' => '?action=admin;area=teampage;sa=delete',
				'hidden_fields' => [
					$context['session_var'] => $context['session_id'],
				],
				'include_sort' => true,
				'include_start' => true,
			],
			'additional_rows' => [
				'submit' => [
					'position' => 'below_table_data',
					'value' => '<input type="submit" size="18" value="'.$txt['delete']. '" class="button" />',
				],
				
			],
		];
		// Let's finishem
		createList($listOptions);
	}

	public static function Save()
	{
		global $smcFunc, $txt;

		// Data
		self::$fields_data = [
			'page_id' => (int) isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0,
			'page_name' => (string) isset($_REQUEST['title']) ? $smcFunc['htmlspecialchars']($_REQUEST['title'], ENT_QUOTES) : '',
			'page_action' => (string) isset($_REQUEST['action']) ? strtolower($smcFunc['htmlspecialchars']($_REQUEST['action'], ENT_QUOTES)) : '',
			'is_text' => (int) isset($_REQUEST['is_text']) ? 1 : 0,
			'page_type' => (string) isset($_REQUEST['type']) && !empty($_REQUEST['is_text']) ? $_REQUEST['type'] : $txt['TeamPage_page_type_groups'],
			'page_body' => (string) isset($_REQUEST['body']) ? $smcFunc['htmlspecialchars']($_REQUEST['body'], ENT_QUOTES) : '',
		];

		print_r(self::$fields_data);

		// Type
		foreach(self::$fields_data as $column => $type)
			self::$fields_type[$column] = gettype($type);

		// Validate info
		self::Validate(self::$fields_data);
		checkSession();
		
		if (empty(self::$fields_data['page_id']))
			Helper::Insert(self::$table, self::$fields_data, self::$fields_type);
		else
			Helper::Update(self::$fields);

		redirectexit('?action=pene');
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
		elseif (!empty(Helper::CheckDuplicate(self::$table, 'cp.page_action', $data['page_action'])))
			fatal_error($txt['TeamPage_error_already_sub'], false);
	}
}