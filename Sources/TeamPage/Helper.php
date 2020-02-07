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

class Helper
{
	public static function Save($config_vars, $return_config, $sa)
	{
		global $context, $scripturl;

		if ($return_config)
			return $config_vars;

		$context['post_url'] = $scripturl . '?action=admin;area=teampage;sa='. $sa. ';save';

		// Saving?
		if (isset($_GET['save'])) {
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=teampage;sa='. $sa. '');
		}
		prepareDBSettingContext($config_vars);
	}

	public static function Count($table, $columns)
	{
		global $smcFunc;

		$columns = implode(', ', $columns);
		$request = $smcFunc['db_query']('','
			SELECT ' . $columns . '
			FROM {db_prefix}{raw:table}',
			[
				'table' => $table,
			]
		);
		$rows = $smcFunc['db_num_rows']($request);
		$smcFunc['db_free_result']($request);

		return $rows;
	}

	public static function Get($start, $items_per_page, $sort, $table, $columns, $additional_query)
	{
		global $smcFunc;

		$columns = implode(', ', $columns);
		$result = $smcFunc['db_query']('', '
			SELECT ' . $columns . '
			FROM {db_prefix}{raw:table}' .
			$additional_query . '
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			[
				'table' => $table,
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			]
		);
		$items = [];
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$items[] = $row;
		$smcFunc['db_free_result']($result);

		return $items;
	}
}