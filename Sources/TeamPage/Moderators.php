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
	public  static $cats_columns = ['c.id_cat', 'c.name', 'c.cat_order'];
	public  static $boards_columns = ['b.id_board', 'b.board_order', 'b.id_cat', 'b.name', 'b.child_level'];
	private static $additional_query = '';
	private static $groups;
	private static $fields_data = [];
	private static $fields_insert = [];
	private static $fields_update = [];


}