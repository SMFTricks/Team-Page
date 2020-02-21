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
	public static function Main()
	{
		global $context;

		loadTemplate('TeamPage');

		$context['sub_template'] = 'teampage_view';
	}
}