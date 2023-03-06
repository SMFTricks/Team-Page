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

class Moderators
{
	/**
	 * @var string The table name
	 */
	public static $table = 'moderators';

	/**
	 * @var array The moderator columns
	 */
	public static $mods_columns = ['md.id_board', 'md.id_member'];

	/**
	 * @var array The categories columns
	 */
	public static $cats_columns = ['c.id_cat', 'c.name AS cat_name', 'c.cat_order'];

	/**
	 * @var array The boards columns
	 */
	public static $boards_columns = ['b.id_board', 'b.board_order', 'b.id_cat', 'b.name', 'b.child_level'];

	/**
	 * @var array The insert data
	 */
	private $fields_data = [];

	/**
	 * @var string The fields type
	 */
	private $fields_type = '';

	/**
	 * Moderators::Save()
	 * 
	 * Save the moderators page
	 * 
	 * @return void
	 */
	public function Save() : void
	{
		// Unlucky
		if (!isset($_REQUEST['id']) || empty($_REQUEST['id']) || empty(Helper::Get('', '', '', Pages::$table . ' AS cp', ['cp.id_page'], 'WHERE cp.id_page = {int:page}', true, '', ['page' => (int) $_REQUEST['id']])))
			fatal_lang_error('TeamPage_page_noexist', false);

		// Data
		$this->fields_data = [
			'id_page' => (int) $_REQUEST['id'],
			'page_boards' => (string) isset($_REQUEST['boardset']) && !empty($_REQUEST['boardset']) && is_array($_REQUEST['boardset']) ? implode(',', $_REQUEST['boardset']) : '',
			'mods_style' => (int) isset($_REQUEST['mod_style']) ? $_REQUEST['mod_style'] : 0,
		];
		checkSession();

		// Type
		foreach($this->fields_data as $column => $type)
			$this->fields_type .= $column . ' = {'.str_replace('integer', 'int', gettype($type)).':'.$column.'}, ';

		// Update
		Helper::Update(Pages::$table, $this->fields_data, $this->fields_type, 'WHERE id_page = {int:id_page}');

		redirectexit('action=admin;area=teampage;sa=pages;updated');
	}
}