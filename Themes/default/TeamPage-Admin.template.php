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
	global $context, $txt, $scripturl;

	echo '
		<hr class="divider" />
		<div class="cat_bar">
			<h3 class="catbg">
				', $txt['TeamPage_add_page'], '
			</h3>
		</div>
		<div class="windowbg">
			<form method="post" action="', $scripturl, '?action=admin;area=teampage;sa=save" accept-charset="', $context['character_set'], '" name="page_post">
				<dl class="settings">
					<dt>
						<a id="setting_title"></a>
						<span><label for="title">', $txt['TeamPage_page_title'], '</label></span>
					</dt>
					<dd>
						<input class="input_text" name="title" id="title" type="text" value="" style="width: 100%" />
					</dd>
					<dt>
						<a id="setting_action"></a>
						<span><label for="action">', $txt['TeamPage_page_subaction'], '</label></span><br/>
						<span class="smalltext">', $txt['TeamPage_page_subaction_desc'], '</span>
					</dt>
					<dd>
						<input class="input_text" name="action" id="action" type="text" value="" style="width: 100%" />
					</dd>
					<dt>
						<a id="setting_istext"></a>
						<span><label for="istext">', $txt['TeamPage_page_is_text'], ':</label></span>
					</dt>
					<dd>
						<input class="input_check" type="checkbox" name="istext" id="istext" value="1" onclick="document.getElementById(\'SelectEditor\').style.display = this.checked ? \'block\' : \'none\';" />
						<fieldset id="SelectEditor" style="display: none;">
							<select name="type">
								<optgroup label="', $txt['TeamPage_page_type'], '">
									<option value="bbc">', $txt['TeamPage_page_type_bbc'], '</option>
									<option value="html">', $txt['TeamPage_page_type_html'], '</option>
								</optgroup>
							</select>
						</fieldset>
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['TeamPage_add_page'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}