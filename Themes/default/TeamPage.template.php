<?php

/**
 * @package Team Page
 * @version 5.4
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

 /**
  * Display the tabs/navigation along with the page title
  */
function template_TeamPage_above()
{
	global $context, $scripturl;

	// No need for tabs with a single page
	if (count($context['teampage_tabs']) > 1)
	{
		echo '
		<div class="buttonlist floatright">';

		// the tabs
		foreach ($context['teampage_tabs'] as $tab)
		{
			echo '
			<a class="button', (isset($_REQUEST['sa']) && $_REQUEST['sa'] == $tab['page_action'] || $tab['id_page'] == $context['teampage']['page_id']) ? ' active' : (!isset($_REQUEST['sa']) && $tab['page_order'] == 0 ? ' active' : ''), '" href="' , $scripturl . '?action=team;sa=', $tab['page_action'], '">
				', $tab['page_name'], '
			</a>';
		}

		echo '
		</div>
		<div class="clear"></div>';
	}

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $context['teampage_title'], '
		</h3>
	</div>';
}

/**
 * Display the footer/copyright
 */
function template_TeamPage_below()
{
	global $context;

	echo $context['teampage']['copyright'];
}

function template_teampage_view()
{
	global $context;

	// Display the page
	echo '
		<div class="roundframe" id="', !empty($context['teampage']['team']) ? 'tp_main_box' : 'tp_main_boards', '">';

		// Load the correct type of page
		switch($context['teampage']['page_type'])
		{
			// Groups
			case 'Groups': 
				echo display_groups();
				break;
			// Boards
			case 'Mods': 
				echo display_moderators();
				break;
			default: 
				echo $context['teampage']['body'];
		}

	echo '
		</div>';
}

function display_groups()
{
	global $context, $modSettings, $txt;

	// Nothing?
	if (empty($context['teampage']['team']))
		return $txt['TeamPage_groups_empty'];

	// Blocks
	foreach($context['teampage']['team'] as $placement => $groups)
	{
		echo '
		<div id="team_block_'. $placement . '">';

		// Groups
		foreach($groups as $group)
		{
			echo  '
			<div class="team_group_container">
				<div class="title_bar">
					<h4 class="titlebg">
						', $group['group_name'], '
					</h4>
				</div>';

			// Description and emmm... Badge?
			if (!empty($modSettings['TeamPage_show_badges']) || (!empty($modSettings['TeamPage_show_description']) && !empty($group['description'])))
			{
				echo '
				<div class="information">
					', !empty($modSettings['TeamPage_show_description']) && !empty($group['description']) ? '
					<span class="team_group_description">'. $group['description'].'</span>' : '', '
					', display_badge($group['icons']), '
				</div>';
			}

			// Members
			echo  '
				<div class="team_members">';

				foreach($group['members'] as $user)
				{
					display_member($user, (isset($group['online_color']) ? $group['online_color'] : ''));
				}

			echo  '
				</div>
			</div>';
		}

		echo '
		</div>';
	}
}

function display_moderators()
{
	global $context, $modSettings, $txt, $scripturl;

	// Nothing?
	if (empty($context['teampage']['moderators']))
		return $txt['TeamPage_groups_empty'];

	// Blocks
	foreach($context['teampage']['moderators'] as $boards => $member_board)
	{
		echo  '
		<div class="team_group_container">
			<div class="title_bar">
				<h4 class="titlebg">', (!empty($member_board['id_board']) ? '
					<a href="' . $scripturl . '?board=' . $member_board['id_board'] . '.0">' . $member_board['name'] . '</a>' :
					$member_board['name']), '
				</h4>
			</div>';

			// Description and emmm... Badge?
			if ((!empty($modSettings['TeamPage_show_badges']) && !empty($member_board['icons'])) || (!empty($modSettings['TeamPage_show_description']) && !empty($board['description'])))
			{
				echo '
				<div class="information">
					', !empty($modSettings['TeamPage_show_description']) && !empty($member_board['description']) ? '
					<span class="team_group_description">' . $member_board['description'] . '</span>' : '', '
					', display_badge($member_board['icons']), '
				</div>';
			}

			// Nothing?
			if (empty($member_board['members']))
			{
				echo '
				<div class="windowbg">
					' . $txt['TeamPage_groups_empty'] . '
				</div>';

				break;
			}

			// Members
			echo  '
				<div class="team_members">';

				foreach($member_board['members'] as $user)
				{
					display_member($user, (isset($group['online_color']) ? $group['online_color'] : ''));
				}

			echo  '
				</div>';

		echo '
		</div>';
	}
}

function display_member($user, $group_color = false)
{
	global $modSettings, $scripturl, $boardurl, $txt;

	echo '
	<div class="windowbg">
		', !empty($modSettings['TeamPage_show_avatars']) ? '
		<img class="teamuser_avatar" src="' . (!empty($user['avatar']['href']) ? $user['avatar']['href'] : $boardurl . '/avatars/default.png') . '" alt=""
			style="' . (!empty($modSettings['TeamPage_avatars_width']) ? '
				width:' . $modSettings['TeamPage_avatars_width'] . 'px;' : '') . (!empty($modSettings['TeamPage_avatars_height']) ? '
				height:' . $modSettings['TeamPage_avatars_height'] . 'px;' : '') . '
		" />' : '', '
		<h4 class="teamuser_name">
			<a href="', $scripturl, '?action=profile;u=', $user['id_member'], '"', !empty($group_color) ? ' style="color: '. $group_color . ' !important;"' : '', '>', $user['real_name'], '</a>
			', !empty($modSettings['TeamPage_show_custom']) && !empty($user['usertitle']) ? ' 
			- <strong>'. $user['usertitle']. '</strong>' : '', '
		</h4>
		<div class="teamuser_info">

			<!-- Boards -->
			', boards_list($user), '
			<!-- Boards -->

			<!-- Personal Text -->
			', !empty($modSettings['TeamPage_show_personal']) && !empty($user['personal_text']) ? '
			<span>
				<strong>' . $txt['personal_text'] . ':</strong>
				<em>' . $user['personal_text'] . '</em>
			</span>' : '', '
			<!-- Personal Text -->

			<!-- Date Registered -->
			', !empty($modSettings['TeamPage_show_registered']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_date_registered'] . ':</strong>
				<span>' . timeformat($user['date_registered']) . '</span>
			</span>' : '', '
			<!-- Date Registered -->

			<!-- Last Activity -->
			', !empty($modSettings['TeamPage_show_login']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_last_login'] . ':</strong>
				<span>' . timeformat($user['last_login']) . '</span>
			</span>' : '', '
			<!-- Last Activity -->

			<!-- Posts -->
			', !empty($modSettings['TeamPage_show_posts']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['posts'] . ':</strong>
				<span>' . $user['posts'] .  '</span>
			</span>' : '', '
			<!-- Posts -->

			<!-- Website -->
			', !empty($modSettings['TeamPage_show_website']) && !empty($user['website_url']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_website'] . ': </strong>
				<a href="' . $user['website_url'] . '" target="_blank" rel="noopener">' . $user['website_title'] . '</a>
			</span>' : '', '
			<!-- Website -->

			<!-- Custom Fields -->
			', user_custom_fields($user), '
			<!-- Custom Fields -->
		</div>
	</div>';
}

/**
 * Dsplay the custom fields
 */
function user_custom_fields($user) : void
{
	global $modSettings;

	if (empty($modSettings['TeamPage_show_custom_fields']) || empty($user['custom_fields']))
		return;

	// Format the custom fields
	$custom_fields = json_decode($modSettings['TeamPage_show_custom_fields']);

	// Display each custom field
	foreach($custom_fields as $custom_field)
	{
		// Check if the user has it
		if (empty($user['custom_fields'][$custom_field]))
			continue;

		// Alright, add it
		echo '
			<span class="tp_user_joined">
				<strong>' . tokenTxtReplace($user['custom_fields'][$custom_field]['field_name']) . ': </strong>', $user['custom_fields'][$custom_field]['value'], '</a>
			</span>';
	}
}

/**
 * Check for any boards available to list
 */
function boards_list($user)
{
	global $scripturl, $txt;

	// Any boards?
	if (empty($user['boards']))
		return;

	// Format the boards
	$b_list = [];
	foreach($user['boards'] as $board)
		$b_list[] = '<a href="' . $scripturl . '?board=' . $board['id_board'] . '.0">' . $board['name'] . '</a>';

	// Return the list of boards
	return '
		<span class="tp_user_boards">
			<strong>' . $txt['TeamPages_boards_moderating'] . ': </strong>
			' . implode(', ', $b_list) . '
		</span>';
}

function display_badge($icon)
{
	global $settings, $modSettings;

	// Display badge? is there a badge/icon?
	if (empty($modSettings['TeamPage_show_badges']))
		return;

	// Get the icon
	$icon = empty($icon) ? array('', '') : explode('#', $icon);

	//We need a little fallback for the membergroup icons. If it doesn't exist in the current theme, fallback to default theme
	if (isset($icon[1]) && file_exists($settings['actual_theme_dir'] . '/images/membericons/' . $icon[1])) //icon is set and exists
		$group_icon_url = $settings['images_url'] . '/membericons/' . $icon[1];
	//icon is set and doesn't exist, fallback to default
	elseif (isset($profile['icons'][1]))
		$group_icon_url = $settings['default_images_url'] . '/membericons/' . $icon[1];
	//not set, bye bye
	else
		return;

	// Get the badge
	return '
		<span class="team_group_badge">
			' . str_repeat('<img class="tp_group_badge" src="'.$group_icon_url.'" alt="" />', $icon[0]) . '
		</span>';
}