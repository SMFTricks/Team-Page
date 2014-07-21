<?php

/**
 * hooks.php
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

	// So... looking for something new
	$hooks = array(
		'integrate_pre_include' => '$sourcedir/Subs-TeamPage.php',
		'integrate_menu_buttons' => 'TeamPage::menu',
		'integrate_admin_areas' => 'TeamPage::admin',
		'integrate_actions' => 'TeamPage::actions',
		'integrate_whos_online' =>	'TeamPage::online',
		'integrate_load_permissions' => 'TeamPage::permissions',
	);

	foreach ($hooks as $hook => $function)
		add_integration_function($hook, $function);
