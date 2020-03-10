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

class Moderators
{
	public  static $table = 'moderators';
	public  static $cats_columns = ['c.id_cat', 'c.name AS cat_name', 'c.cat_order'];
	public  static $boards_columns = ['b.id_board', 'b.board_order', 'b.id_cat', 'b.name', 'b.child_level'];
	private static $additional_query = '';
	private static $fields_data = [];
	private static $fields_type = '';

	public static function Save()
	{
		global $smcFunc, $txt;

		// Unlucky
		if (!isset($_REQUEST['id']) || empty($_REQUEST['id']) || empty(Helper::Find(Pages::$table . ' AS cp', 'cp.id_page', $_REQUEST['id'])))
			fatal_error($txt['TeamPage_page_noexist'], false);

		// Data
		self::$fields_data = [
			'id_page' => (int) $_REQUEST['id'],
			'page_boards' => (string) isset($_REQUEST['boardset']) && !empty($_REQUEST['boardset']) && is_array($_REQUEST['boardset']) ? implode(',', $_REQUEST['boardset']) : '',
			'mods_style' => (int) isset($_REQUEST['mod_style']) ? $_REQUEST['mod_style'] : 0,
		];
		checkSession();

		// Type
		foreach(self::$fields_data as $column => $type)
			self::$fields_type .= $column . ' = {'.str_replace('integer', 'int', gettype($type)).':'.$column.'}, ';

		// Update
		Helper::Update(Pages::$table, self::$fields_data, self::$fields_type, 'WHERE id_page = ' . self::$fields_data['id_page']);

		redirectexit('action=admin;area=teampage;sa=pages;updated');
	}
}