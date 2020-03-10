<?php

/**
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

function template_pages_post_above() {}

function template_pages_post_below()
{
	global $context, $txt;

	echo '
		<hr class="divider" />
		', template_pages_edit_above(), '
					<dt>
						<a id="setting_istext"></a>
						<span><label for="istext">', $txt['TeamPage_page_type_select'], ':</label></span>
					</dt>
					<dd>
						<select name="type">
							<optgroup label="', $txt['TeamPage_page_type'], '">
								<option value="Groups" selected>', $txt['TeamPage_page_type_groups'], '</option>
								<option value="Mods">', $txt['TeamPage_page_type_mods'], '</option>
								<option value="BBC">', $txt['TeamPage_page_type_bbc'], '</option>
								<option value="HTML">', $txt['TeamPage_page_type_html'], '</option>
							</optgroup>
						</select>
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['TeamPage_add_page'], '" />
			</form>
		</div>';
}

function template_pages_edit_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $context['TeamPage_pages_title'], '
		</h3>
	</div>
	<div class="windowbg">
		<form method="post" action="', $scripturl, '?action=admin;area=teampage;sa=save" name="page_post">
			', isset($_REQUEST['id']) && !empty($context['page_details']['id_page']) ? '<input type="hidden" name="id" value="'.$context['page_details']['id_page'].'">' : '', '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<dl class="settings">
				<dt>
					<a id="setting_title"></a>
					<span><label for="title">', $txt['TeamPage_page_title'], '</label></span>
				</dt>
				<dd>
					<input class="input_text" name="title" id="title" type="text" value="', !empty($context['page_details']['page_name']) ? $context['page_details']['page_name'] : '', '" style="width: 100%;" />
				</dd>
				<dt>
					<a id="setting_page_action"></a>
					<span><label for="page_action">', $txt['TeamPage_page_subaction'], '</label></span><br/>
					<span class="smalltext">', $txt['TeamPage_page_subaction_desc'], '</span>
				</dt>
				<dd>
					<input class="input_text" name="page_action" id="page_action" type="text" value="', !empty($context['page_details']['page_action']) ? $context['page_details']['page_action'] : '', '" style="width: 100%;" />
				</dd>';
}

function template_pages_edit()
{
	global $txt, $context;

	// Text Page?
	if (!empty($context['page_details']['is_text']))
	{
		echo '
				<dt>
					<a id="setting_page_body"></a>
					<span><label for="page_body">', $txt['TeamPage_page_modify_body'], ':</label></span>
				</dt>
				<dd style="width:100%">';

		// BBC?
		if ($context['page_details']['page_type'] == 'BBC')
		{
			// Showing BBC?
			if (!empty($context['show_bbc']))
				echo '
					<div id="bbcBox_message"></div>';

			// What about smileys?
			if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
				echo '
					<div id="smileyBox_message"></div>';

					// Show BBC buttons, smileys and textbox.
					template_control_richedit($context['page_details']['body_bbc'], 'smileyBox_message', 'bbcBox_message');
		}
		else
			echo '
					<textarea name="page_body" id="page_body" rows="2" style="width: 100%">', !empty($context['page_details']['page_body']) ? $context['page_details']['page_body'] : '', '</textarea>';

			echo '
				</dd>';
	}

	echo '
			</dl>
			<input class="button floatleft" type="submit" value="', $txt['save'], '" />
		</form>
	</div>';
}

function template_pages_edit_below()
{
	global $txt, $context, $scripturl, $modSettings;

	if (empty($context['page_details']['is_text']))
	{
		echo '
		<hr class="divider" />
		<div class="cat_bar" id="tp_manage_'.$context['page_details']['page_type'].'" page-id="', $context['page_details']['id_page'], '">
			<h3 class="catbg">
				', $txt['TeamPage_manage_'.$context['page_details']['page_type']], '
			</h3>
		</div>
		<div class="information">
			', $txt['TeamPage_page_'.$context['page_details']['page_type'].'_desc'], '
		</div>';
	}

	// Page groups
	if ($context['page_details']['page_type'] == 'Groups')
	{
		echo '
		<div class="half_content">
			', display_groups(), '
		</div>
		<div class="half_content">
			', display_groups('right'), '
		</div>
		<div class="content">
			', display_groups('bottom'), '
		</div>';

		// Forum groups that aren't in the page yet
		echo '
		<div class="title_bar">
			<h4 class="titlebg">
				', $txt['TeamPage_groups_forum'], '
			</h4>
		</div>
		<ul class="information" id="tp_group_sort_all">';

	if (!empty($context['forum_groups']))
		foreach($context['forum_groups'] as $group)
			echo  '
			<li class="windowbg" group-id="'.$group['id_group'].'">
				<a href="', $scripturl, '?action=admin;area=membergroups;sa=members;group=', $group['id_group'], '" style="color: ', $group['online_color'], ';">', $group['group_name'], '</a>
			</li>';

	echo '
		</ul>';
	}

	// Moderators
	if ($context['page_details']['page_type'] == 'Mods')
	{
		echo '
		<div class="title_bar">
			<h4 class="titlebg">', $txt['settings'], '</h4>
		</div>
		<div class="windowbg">
			<form method="post" action="', $scripturl, '?action=admin;area=teampage;sa=modsave" id="mods_settings" name="mods_settings">
			', isset($_REQUEST['id']) && !empty($context['page_details']['id_page']) ? '<input type="hidden" name="id" value="'.$context['page_details']['id_page'].'">' : '', '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<dl class="settings">
					<dt>
						<a id="setting_mod_style"></a>
						<span><label for="mod_style">', $txt['TeamPage_mods_type'], ':</label></span>
					</dt>
					<dd>
						<select name="type">
							<optgroup label="', $txt['TeamPage_mods_type_select'], '">
								<option value="0" selected>', $txt['TeamPage_mods_type_user'], '</option>
								<option value="1">', $txt['TeamPage_mods_type_board'], '</option>
							</optgroup>
						</select>
					</dd>
					<dt>
						<a id="setting_mod_boards"></a>
						<span><label for="mod_boards">', $txt['TeamPage_mods_boards'], ':</label></span>
					</dt>
					<dd>
						', boards_list(true, 'mods_settings'), '
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['save'], '" />
			</form>
		</div>';
	}
}

/**
 * The template for determining which boards a group has access to.
 * 
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2020 Simple Machines and individual contributors
 * @param bool $collapse Whether to collapse the list by default
 */
function boards_list($collapse = true, $form_id = 'mods_settings')
{
	global $context, $txt, $modSettings;

	echo '
							<fieldset id="visible_boards"', !empty($modSettings['deny_boards_access']) ? ' class="denyboards_layout"' : '', '>
								<legend>', $txt['TeamPage_mods_boards'], '</legend>
								<ul class="padding floatleft">';

	foreach ($context['forum_categories'] as $category)
	{
		if (empty($modSettings['deny_boards_access']))
			echo '
									<li class="category">
										<a href="javascript:void(0);" onclick="selectBoards([', implode(', ', $category['child_ids']), '], \''.$form_id.'\'); return false;"><strong>', $category['name'], '</strong></a>
										<ul>';
		else
			echo '
									<li class="category clear">
										<strong>', $category['name'], '</strong>
										<span class="select_all_box floatright">
											<em class="all_boards_in_cat">', $txt['all_boards_in_cat'], ': </em>
											<select onchange="select_in_category(', $category['id_cat'], ', this, [', implode(',', array_keys($category['boards'])), ']);">
												<option>---</option>
												<option value="allow">', $txt['board_perms_allow'], '</option>
												<option value="ignore">', $txt['board_perms_ignore'], '</option>
												<option value="deny">', $txt['board_perms_deny'], '</option>
											</select>
										</span>
										<ul id="boards_list_', $category['id_cat'], '">';

		foreach ($category['boards'] as $board)
		{
			$board['allow'] = false;
			$board['deny'] = false;

			if (empty($modSettings['deny_boards_access']))
				echo '
											<li class="board" style="margin-', $context['right_to_left'] ? 'right' : 'left', ': ', $board['child_level'], 'em;">
												<input type="checkbox" name="boardaccess[', $board['id_board'], ']" id="brd', $board['id_board'], '" value="allow"', $board['allow'] ? ' checked' : '', '> <label for="brd', $board['id_board'], '">', $board['name'], '</label>
											</li>';
			else
				echo '
											<li class="board clear">
												<span style="margin-', $context['right_to_left'] ? 'right' : 'left', ': ', $board['child_level'], 'em;">', $board['name'], ': </span>
												<span class="floatright">
													<input type="radio" name="boardaccess[', $board['id_board'], ']" id="allow_brd', $board['id_board'], '" value="allow"', $board['allow'] ? ' checked' : '', '> <label for="allow_brd', $board['id_board'], '">', $txt['permissions_option_on'], '</label>
													<input type="radio" name="boardaccess[', $board['id_board'], ']" id="ignore_brd', $board['id_board'], '" value="ignore"', !$board['allow'] && !$board['deny'] ? ' checked' : '', '> <label for="ignore_brd', $board['id_board'], '">', $txt['permissions_option_off'], '</label>
													<input type="radio" name="boardaccess[', $board['id_board'], ']" id="deny_brd', $board['id_board'], '" value="deny"', $board['deny'] ? ' checked' : '', '> <label for="deny_brd', $board['id_board'], '">', $txt['permissions_option_deny'], '</label>
												</span>
											</li>';
		}

		echo '
										</ul>
									</li>';
	}

	echo '
								</ul>';

	if (empty($modSettings['deny_boards_access']))
		echo '
								<br class="clear"><br>
								<input type="checkbox" id="checkall_check" onclick="invertAll(this, this.form, \'boardaccess\');">
								<label for="checkall_check"><em>', $txt['check_all'], '</em></label>
							</fieldset>';
	else
		echo '
								<br class="clear">
								<span class="select_all_box">
									<em>', $txt['all'], ': </em>
									<input type="radio" name="select_all" id="allow_all" onclick="selectAllRadio(this, this.form, \'boardaccess\', \'allow\');"> <label for="allow_all">', $txt['board_perms_allow'], '</label>
									<input type="radio" name="select_all" id="ignore_all" onclick="selectAllRadio(this, this.form, \'boardaccess\', \'ignore\');"> <label for="ignore_all">', $txt['board_perms_ignore'], '</label>
									<input type="radio" name="select_all" id="deny_all" onclick="selectAllRadio(this, this.form, \'boardaccess\', \'deny\');"> <label for="deny_all">', $txt['board_perms_deny'], '</label>
								</span>
							</fieldset>
							<script>
								$(document).ready(function () {
									$(".select_all_box").each(function () {
										$(this).removeClass(\'select_all_box\');
									});
								});
							</script>';

	if ($collapse)
		echo '
							<a href="javascript:void(0);" onclick="document.getElementById(\'visible_boards\').classList.remove(\'hidden\'); document.getElementById(\'visible_boards_link\').classList.add(\'hidden\'); return false;" id="visible_boards_link" class="hidden">[ ', $txt['membergroups_select_visible_boards'], ' ]</a>
							<script>
								document.getElementById("visible_boards_link").classList.remove(\'hidden\');
								document.getElementById("visible_boards").classList.add(\'hidden\');
							</script>';
}

function display_groups($placement = 'left')
{
	global $context, $txt, $scripturl;

	echo '
		<div class="title_bar">
			<h4 class="titlebg">
				', $txt['TeamPage_groups_'.$placement], '
			</h4>
		</div>
		<ul class="information" id="tp_group_sort_'.$placement.'">';

	// Load groups
	if (!empty($context['page_groups'][$placement]))
		foreach($context['page_groups'][$placement] as $group)
			echo  '
			<li class="windowbg" group-id="'.$group['id_group'].'">
				<a href="', $scripturl, '?action=admin;area=membergroups;sa=members;group=', $group['id_group'], '" style="color: ', $group['online_color'], ';">', $group['group_name'], '</a>
			</li>';

	echo '
		</ul>';
}