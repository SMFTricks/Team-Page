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
						<span><label for="istext">', $txt['TeamPage_page_is_text'], ':</label></span>
					</dt>
					<dd>
						<input class="input_check" type="checkbox" name="istext" id="istext" value="1" onclick="document.getElementById(\'SelectEditor\').style.display = this.checked ? \'block\' : \'none\';" />
						<fieldset id="SelectEditor" style="display: none;">
							<select name="type">
								<optgroup label="', $txt['TeamPage_page_type'], '">
									<option value="BBC">', $txt['TeamPage_page_type_bbc'], '</option>
									<option value="HTML">', $txt['TeamPage_page_type_html'], '</option>
								</optgroup>
							</select>
						</fieldset>
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
	if (!empty($context['page_details']['is_text'])) {
		echo '
				<dt>
					<a id="setting_page_body"></a>
					<span><label for="page_body">', $txt['TeamPage_page_modify_body'], ':</label></span>
				</dt>
				<dd style="width:100%">';

		// BBC?
		if ($context['page_details']['page_type'] == 'BBC') {

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
	global $txt, $context;

	// Page groups
	if (empty($context['page_details']['is_text'])) {

		echo '
		<hr class="divider" />
		<div class="cat_bar">
			<h3 class="catbg">
				', $txt['TeamPage_manage_groups'], '
			</h3>
		</div>
		<div class="half_content">
			<div class="title_bar">
				<h4 class="titlebg">
					', $txt['TeamPage_groups_left'], '
				</h4>
			</div>
			<div class="windowbg">

			</div>
		</div>
		<div class="half_content">
			<div class="title_bar">
				<h4 class="titlebg">
					', $txt['TeamPage_groups_right'], '
				</h4>
			</div>
			<div class="windowbg">

			</div>
		</div>
		<div class="content">
			<div class="title_bar">
				<h4 class="titlebg">
					', $txt['TeamPage_groups_bottom'], '
				</h4>
			</div>
			<div class="windowbg">

			</div>
		</div>';
	}

	// Forum groups that aren't in the page already

}