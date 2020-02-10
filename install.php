<?php

/**
 * install.php
 *
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, Diego Andrés
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
			'table_name' => '{db_prefix}teampage_groups',
			'columns' => array(
				array(
					'name' => 'id_group',
					'type' => 'int',
					'size' => 10,
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
					'name' => 'placement',
					'type' => 'text',
					'size' => 7,
					'null' => false,
					'default' => 'left',
				),
				array(
					'name' => 'position',
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
			'table_name' => '{db_prefix}teampage_pages',
			'columns' => array(
				array(
					'name' => 'id_page',
					'type' => 'smallint',
					'size' => 3,
					'null' => false,
					'auto' => true,
				),
				array(
					'name' => 'page_name',
					'type' => 'text',
				),
				array(
					'name' => 'page_action',
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
					'name' => 'page_type',
					'type' => 'text',
				),
				array(
					'name' => 'page_body',
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