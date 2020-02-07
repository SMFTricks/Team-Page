<?php

/**
 * install.php
 *
 * @package Team Page
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');

	elseif (!defined('SMF'))
		exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	global $smcFunc, $context;

	db_extend('packages');

	if (empty($context['uninstalling']))
	{


		// Team Page - Main
		$tables[] = array(
			'table_name' => '{db_prefix}teampage',
			'columns' => array(
				array(
					'name' => 'id_group',
					'type' => 'smallint',
					'size' => 5,
					'unsigned' => true,
					'null' => false,
				),
				array(
					'name' => 'id_page',
					'type' => 'smallint',
					'size' => 3,
					'null' => false,
					'default' => 0,
					'unsigned' => true,
				),
				array(
					'name' => 'place',
					'type' => 'smallint',
					'size' => 3,
					'null' => false,
					'default' => 0,
					'unsigned' => true,
				),
				array(
					'name' => 'roworder',
					'type' => 'smallint',
					'size' => 3,
					'null' => false,
					'default' => 0,
					'unsigned' => true,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id_group', 'id_page')
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);

		// Team Page - Custom Pages
		$tables[] = array(
			'table_name' => '{db_prefix}teampage_cp',
			'columns' => array(
				array(
					'name' => 'id_page',
					'type' => 'smallint',
					'size' => 3,
					'null' => false,
					'auto' => true,
				),
				array(
					'name' => 'name_page',
					'type' => 'text',
				),
				array(
					'name' => 'sub_page',
					'type' => 'text',
				),
				array(
					'name' => 'is_text',
					'type' => 'smallint',
					'default' => 0,
					'size' => 1,
					'null' => false,
				),
				array(
					'name' => 'type',
					'type' => 'text',
				),
				array(
					'name' => 'body',
					'type' => 'text',
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id_page')
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);

		// Installing
		foreach ($tables as $table)
		$smcFunc['db_create_table']($table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);
	}