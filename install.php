<?php

/**
 * install.php
 *
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, Diego Andrés
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');

	elseif (!defined('SMF'))
		exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	global $smcFunc, $context;

	db_extend('packages');

	if (empty($context['uninstalling']))
	{
		// Team Page - Groups
		$tables[] = [
			'table_name' => '{db_prefix}teampage_groups',
			'columns' => [
				[
					'name' => 'id_group',
					'type' => 'int',
					'size' => 10,
					'unsigned' => true,
					'not_null' => true,
				],
				[
					'name' => 'id_page',
					'type' => 'smallint',
					'size' => 3,
					'not_null' => true,
					'default' => 0,
					'unsigned' => true,
				],
				[
					'name' => 'placement',
					'type' => 'text',
					'size' => 7,
					'not_null' => true,
				],
				[
					'name' => 'position',
					'type' => 'smallint',
					'size' => 3,
					'not_null' => true,
					'default' => 0,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id_group', 'id_page']
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];

		// Team Page - Custom Pages
		$tables[] = [
			'table_name' => '{db_prefix}teampage_pages',
			'columns' => [
				[
					'name' => 'id_page',
					'type' => 'smallint',
					'size' => 3,
					'not_null' => true,
					'auto' => true,
				],
				[
					'name' => 'page_name',
					'type' => 'text',
				],
				[
					'name' => 'page_action',
					'type' => 'text',
				],
				[
					'name' => 'page_type',
					'type' => 'text',
				],
				[
					'name' => 'page_body',
					'type' => 'text',
				],
				[
					'name' => 'page_order',
					'type' => 'int',
					'size' => 5,
					'default' => 0,
					'not_null' => true,
				],
				[
					'name' => 'mods_style',
					'type' => 'smallint',
					'size' => 3,
					'not_null' => true,
				],
				[
					'name' => 'page_boards',
					'type' => 'text',
					'not_null' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id_page']
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];

		// Installing
		foreach ($tables as $table)
		$smcFunc['db_create_table']($table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);
	}