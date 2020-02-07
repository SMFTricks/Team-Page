<?php

/**
 * TeamPageAdmin.template
 *
 * @package Team Page
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014 Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

function template_show_pages()
{
	global $txt, $context, $scripturl, $modSettings, $smcFunc;
	
	// Show a nice message if no Pages are avaliable
	if (empty($context['teampage']['pages']['all']))
	{
			echo '
		<div class="title_bar">
			<h3 class="titlebg">
				<span class="ie6_header floatleft">', $txt['TeamPage_no_pages'], '</span>
			</h3>
		</div>';
		
	}
	else
	{
		echo '
		<table class="table_grid" cellspacing="0" width="100%">
			<thead>
				<tr class="titlebg">
					<th class="first_th lefttext">' . TeamPage::text('page_id'). '</th>
					<th class="lefttext" width="35%">' . TeamPage::text('page_title'). '</th>
					<th class="lefttext">' . TeamPage::text('page_subaction'). '</th>
					<th class="lefttext">' . TeamPage::text('page_type_i'). '</th>
					<th class="lefttext">' . TeamPage::text('page_modify'). '</th>
					<th class="last_th lefttext">' . TeamPage::text('page_delete'). '</th>
				</tr>
			</thead>
			<tbody>';

			$mpages = 0;
			foreach($context['teampage']['pages']['all'] as $pages)
			{
				$mpages++;
	        	$class = ($mpages % 2) ? 'windowbg' : 'windowbg2';

				echo '
				<tr class="', $class, '">
					<td>', $pages['id'], '</td>
					<td><a href="', $scripturl, '?action=teampage;sa=', $pages['subp'], '">', $pages['name'], '</a></td>
					<td>', $pages['subp'], '</td>
					<td>', ($pages['text'] == 1) ? $pages['type'] : $txt['TeamPage_page_type_groups'], '</td>
					<td><a href="',$scripturl,'?action=admin;area=teampage;sa=editpage;id=', $pages['id'], '">', $txt['TeamPage_page_modify_short'], '</a></td>
					<td><a href="',$scripturl,'?action=admin;area=teampage;sa=deletepage;id=', $pages['id'], '">', $txt['TeamPage_page_delete_short'], '</a></td>
				</tr>';
			}

		echo '
			</tbody>
		</table>';
		
		/* Pagination */
		if (!empty($context['page_index']))
			echo '
		<div class="pagesection">
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>
		</div>';
	}
	
	echo '
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">
				<form method="post" action="' . $scripturl . '?action=admin;area=teampage;sa=addpage">
					', $txt['TeamPage_page_title'], ': 
					<input type="text" name="pagetitle" size="50" value="" />
					<br /><br />
					', $txt['TeamPage_page_subaction'], ': 
					<input type="text" name="subact" value="" /><br />
					', $txt['TeamPage_page_subaction_desc'], '
					<br /><br />
					', $txt['TeamPage_page_is_text'], ': 
					<input type="checkbox" name="textpage" value="1" onclick="document.getElementById(\'SelectEditor\').style.display = this.checked ? \'block\' : \'none\';" />
					<fieldset id="SelectEditor" style="display: none; margin-top: 5px; width: 0; margin-bottom: -10px;">
						<select name="type">
							<optgroup label="', $txt['TeamPage_page_type'], '">
								<option value="bbc">', $txt['TeamPage_page_type_bbc'], '</option>
								<option value="html">', $txt['TeamPage_page_type_html'], '</option>
							</optgroup>
						</select>
					</fieldset>
					<br /><br />
					<input class="button_submit" type="submit" value="', $txt['TeamPage_add_page'], '" />
				</form>
			</div>
			<span class="botslice"><span></span></span>
		</div>';
	
}

function template_manage_groups()
{
	global $context, $modSettings, $txt, $settings, $scripturl;

	$idpag = $_GET['id'];
	$sscr = $scripturl . '?action=admin;area=teampage;sa=editpage;id='. $idpag. '';
	$alternate = array(false, false, false, false);

	echo '
		<div class="title_bar">
			<h4 class="titlebg">
				' . $txt['TeamPage_manage_groups'] . '
			</h4>
		</div>';

	if (isset($_GET['error']) ||( empty($context['groups']['left']) && empty($context['groups']['right']) && empty($context['groups']['bottom'])))
	{
		echo '
			<span class="upperframe"><span></span></span>
				<div class="roundframe smalltext">
				<span class="error">' . $txt['no_groups_defined'] . '</span>
				</div>
			<span class="lowerframe"><span></span></span>';
	}
	
	if (isset($_GET['passed']) || (isset($_GET['del'])))
	{
		echo '
			<span class="upperframe"><span></span></span>
				<div class="roundframe smalltext">
				<span style="color: green;">' , str_replace('%1',$context['page']['sub_page'],$txt['TeamPage_settings_saved']) , '</span>
				</div>
			<span class="lowerframe"><span></span></span>';
	}
	
	echo '
		<span class="upperframe"><span></span></span>
		<div class="roundframe">
			<form method="post" action="' . $sscr. ';act=save">
				', $txt['TeamPage_page_title'], ': <br />
				<input type="text" name="pagetitle" size="50" value="', !empty($context['teampage']['p_title']) ? $context['teampage']['p_title'] : '', '" />
				<br /><br />
				<input class="button_submit" type="submit" value="', $txt['save'], '" />
			</form>
		</div>
		<span class="lowerframe"><span></span></span>';

	echo '
		<span class="upperframe"><span></span></span>
		<div class="roundframe smalltext">';

	echo '
	<div class="cat_bar"><h3 class="catbg grid_header"><span class="left"></span>' . $txt['groups_left']  . '</h3></div>
		<table class="bordercolor" align="center" border="0" cellpadding="4" cellspacing="1" width="100%">
			<tr class="titlebg">
				<th width="7%">' . $txt['groups_order'] . '</th>
				<th width="45%">' . $txt['groups_name'] . '</th>
				<th width="20%">' . $txt['groups_stars'] . '</th>
				<th width="15%">' . $txt['groups_move'] . '</th>
				<th width="10%">' . $txt['groups_action'] . '</th>
			</tr>';
			
			
	// Need this for the not active groups
	foreach ($context['groups']['all'] as $groups)
	{
		$findgroups[] = $groups['id'];
		
	}

	$count = 0;

	if (!empty($context['groups']['left']))
	{
		foreach ($context['groups']['left'] as $group)
		{
			// If we're at this point, here's our man!
			$count++;

			// Doing the alternating background game...
			$alternate[0] = !$alternate[0];
			$window_class = $alternate[0] ? 'windowbg' : 'windowbg2';

			// Some declarations.
			$order = array(
				'is_last' => $count == count($context['groups']['left']),
				'is_first' => $count == 1,
			);

			echo '
				<tr class="' . $window_class . '">
					<td align="center">' . (!$order['is_last'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=down"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" title="Push down" /></a>' : '') . '' . (!$order['is_first'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=up"><img src="' . $settings['default_images_url'] . '/sort_up.gif" alt="" title="Push up" /></a>' : '') . '</td>
					<td>' . ($group['id'] == 3 ? $group['name'] : '<a href="' . $scripturl . '?action=admin;area=membergroups;sa=members;group=' . $group['id'] . '" style="color: ' . $group['color'] . '">' . $group['name'] . '</a>') . '</td>
					<td>' . $group['image'] . '</td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=2"><img src="' . $settings['default_images_url'] . '/selected.gif" alt="" title="' . $txt['groups_move_right'] . '" /></a>&nbsp;<a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=3"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" title="' . $txt['groups_move_bottom'] . '" /></a></td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=0"><img src="' . $settings['default_images_url'] . '/pm_recipient_delete.gif" alt="" title="' . $txt['groups_remove'] . '" /></a></td>
				</tr>';

		}
		unset($group);
	}

	echo '
		</table><br />
		<div class="cat_bar"><h3 class="catbg grid_header"><span class="left"></span>' . $txt['groups_right']  . '</h3></div>
		<table class="bordercolor" align="center" border="0" cellpadding="4" cellspacing="1" width="100%">
			<tr class="titlebg">
				<th width="7%">' . $txt['groups_order'] . '</th>
				<th width="45%">' . $txt['groups_name'] . '</th>
				<th width="20%">' . $txt['groups_stars'] . '</th>
				<th width="15%">' . $txt['groups_move'] . '</th>
				<th width="10%">' . $txt['groups_action'] . '</th>
			</tr>';

	$count2 = 0;

	if (!empty($context['groups']['right']))
	{
		foreach ($context['groups']['right'] as $group)
		{
			// If we're at this point, here's our man!
			$count2++;

			// Doing the alternating background game...
			$alternate[1] = !$alternate[1];
			$window_class = $alternate[1] ? 'windowbg' : 'windowbg2';

			// Some declarations.
			$order = array(
				'is_last' => $count2 == count($context['groups']['right']),
				'is_first' => $count2 == 1,
			);

			echo '
				<tr class="' . $window_class . '">
					<td align="center">' . (!$order['is_last'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=down"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" title="Push down" /></a>' : '') . '' . (!$order['is_first'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=up"><img src="' . $settings['default_images_url'] . '/sort_up.gif" alt="" title="Push up" /></a>' : '') . '</td>
					<td>' . ($group['id'] == 3 ? $group['name'] : '<a href="' . $scripturl . '?action=admin;area=membergroups;sa=members;group=' . $group['id'] . '" style="color: ' . $group['color'] . '">' . $group['name'] . '</a>') . '</td>
					<td>' . $group['image'] . '</td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=1"><img src="' . $settings['default_images_url'] . '/sort_left.gif" alt="" title="' . $txt['groups_move_left'] . '" /></a>&nbsp;<a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=3"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" title="' . $txt['groups_move_bottom'] . '" /></a></td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=0"><img src="' . $settings['default_images_url'] . '/pm_recipient_delete.gif" alt="" title="' . $txt['groups_remove'] . '" /></a></td>
				</tr>';
		}
		unset($group);
	}

	echo '
		</table><br />
		<div class="cat_bar"><h3 class="catbg grid_header"><span class="left"></span>' . $txt['groups_bottom']  . '</h3></div>
		<table class="bordercolor" align="center" border="0" cellpadding="4" cellspacing="1" width="100%">
			<tr class="titlebg">
				<th width="7%">' . $txt['groups_order'] . '</th>
				<th width="45%">' . $txt['groups_name'] . '</th>
				<th width="20%">' . $txt['groups_stars'] . '</th>
				<th width="15%">' . $txt['groups_move'] . '</th>
				<th width="10%">' . $txt['groups_action'] . '</th>
			</tr>';

	$count3 = 0;

	if (!empty($context['groups']['bottom']))
	{
		foreach ($context['groups']['bottom'] as $group)
		{
			// If we're at this point, here's our man!
			$count3++;

			// Doing the alternating background game...
			$alternate[2] = !$alternate[2];
			$window_class = $alternate[2] ? 'windowbg' : 'windowbg2';

			// Some declarations.
			$order = array(
				'is_last' => $count3 == count($context['groups']['bottom']),
				'is_first' => $count3 == 1,
			);

			echo '
				<tr class="' . $window_class . '">
					<td align="center">' . (!$order['is_last'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=down"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" title="Push down" /></a>' : '') . '' . (!$order['is_first'] ? '<a href="' . $sscr. ';act=move;group=' . $group['id'] . ';direction=up"><img src="' . $settings['default_images_url'] . '/sort_up.gif" alt="" title="Push up" /></a>' : '') . '</td>
					<td>' . ($group['id'] == 3 ? $group['name'] : '<a href="' . $scripturl . '?action=admin;area=membergroups;sa=members;group=' . $group['id'] . '" style="color: ' . $group['color'] . '">' . $group['name'] . '</a>') . '</td>
					<td>' . $group['image'] . '</td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=1"><img src="' . $settings['default_images_url'] . '/sort_left.gif" alt="" title="' . $txt['groups_move_left'] . '" /></a>&nbsp;<a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=2"><img src="' . $settings['default_images_url'] . '/selected.gif" alt="" title="' . $txt['groups_move_right'] . '" /></a></td>
					<td align="center"><a href="' . $sscr. ';act=swap;group=' . $group['id'] . ';place=0"><img src="' . $settings['default_images_url'] . '/pm_recipient_delete.gif" alt="" title="' . $txt['groups_remove'] . '" /></a></td>
				</tr>';
		}
		unset($group);
	}
	
	echo '
		</table>';

	echo '
		<table border="0" width="100%">
		<tr>
			<td width="40%" align="left" valign="top">
				<span class="upperframe"><span></span></span>
				<div class="roundframe">
				<div class="cat_bar"><h3 class="catbg"><span class="left"></span>
					', $txt['team_groups_notp'], '
				</h3></div>';
				
				
	$found = $count + $count2 + $count3;
	
	if ($context['groups']['count'] == $found)
	{
		echo '
			<em>' . $txt['no_more_groups'] . '</em>';
	}
	
	else
	{
		echo '
		<table class="bordercolor" align="center" border="0" cellpadding="4" cellspacing="1" width="100%">
			<tr class="titlebg">
				<th width="70%">' . $txt['groups_name'] . '</th>
				<th width="25%">' . $txt['groups_place'] . '</th>
			</tr>';
				
		if ($found == 0)
		{	
			foreach ($context['groups']['notactive'] as $sg)
			{
				// Doing the alternating background game...
				$alternate[3] = !$alternate[3];
				$window_class = $alternate[3] ? 'windowbg' : 'windowbg2';
					
				echo '
				<tr class="' . $window_class . '">
					<td>' . ($sg['id'] == 3 ? $sg['name'] : '<a href="' . $scripturl . '?action=admin;area=membergroups;sa=members;group=' . $sg['id'] . '" style="color: ' . $sg['color'] . '">' . $sg['name'] . '</a>') . '</td>
					<td><a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=1"><img src="' . $settings['default_images_url'] . '/sort_left.gif" alt="" /></a>&nbsp;<a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=3"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" /></a>&nbsp;<a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=2"><img src="' . $settings['default_images_url'] . '/selected.gif" alt="" /></a></td>
				</tr>';
					
			}
		}
		
		else
		{	
			foreach ($context['groups']['notactive'] as $sg)
			{
				// Doing the alternating background game...
				$alternate[3] = !$alternate[3];
				$window_class = $alternate[3] ? 'windowbg' : 'windowbg2';
					
				if (!in_array($sg['id'],$findgroups))	
				echo '
				<tr class="' . $window_class . '">
					<td>' . ($sg['id'] == 3 ? $sg['name'] : '<a href="' . $scripturl . '?action=admin;area=membergroups;sa=members;group=' . $sg['id'] . '" style="color: ' . $sg['color'] . '">' . $sg['name'] . '</a>') . '</td>
					<td><a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=1"><img src="' . $settings['default_images_url'] . '/sort_left.gif" alt="" /></a>&nbsp;<a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=3"><img src="' . $settings['default_images_url'] . '/sort_down.gif" alt="" /></a>&nbsp;<a href="' . $sscr. ';act=add;group=' . $sg['id'] . ';place=2"><img src="' . $settings['default_images_url'] . '/selected.gif" alt="" /></a></td>
				</tr>';
					
			}
		}
		
		echo '
		</table>';
	}

	echo '
				</div>
				<span class="lowerframe"><span></span></span>
			</td>
		</table>';

	echo '
		</div>
		<span class="lowerframe"><span></span></span>';

}

function template_manage_editor()
{
	global $context, $modSettings, $txt, $settings, $scripturl;

	$idpag = $_GET['id'];
	$sscr = $scripturl . '?action=admin;area=teampage;sa=editpage;id='. $idpag. '';
	$alternate = array(false, false, false, false);

	echo '
		<div class="title_bar">
			<h4 class="titlebg">
				' . $txt['TeamPage_manage_editor'] . '
			</h4>
		</div>';

	if (isset($_GET['error']) ||(empty($context['teampage']['p_title']) || empty($context['teampage']['p_body'])))
	{
		echo '
			<span class="upperframe"><span></span></span>
				<div class="roundframe smalltext">
					<span class="error">', $txt['TeamPage_title_editor_empty'], '</span>
				</div>
			<span class="lowerframe"><span></span></span>';
	}
	
	if (isset($_GET['passed']))
	{
		echo '
			<span class="upperframe"><span></span></span>
				<div class="roundframe smalltext">
					<span style="color: green;">' , str_replace('%1',$context['teampage']['p_subpage'],$txt['TeamPage_settings_saved']) , '</span>				
				</div>
			<span class="lowerframe"><span></span></span>';
	}

	echo '
		<span class="upperframe"><span></span></span>
		<div class="roundframe">
			<form method="post" action="' . $sscr. ';act=save">
				', $txt['TeamPage_page_title'], ': <br />
				<input type="text" name="pagetitle" size="50" value="', !empty($context['teampage']['p_title']) ? $context['teampage']['p_title'] : '', '" />
				<br /><br />';
				
			// Only show bbcode bar if BBCodes are enabled, AND if its a quick reply mode with bbcode bar		
			if ($context['teampage']['p_type'] == 'bbc')
			{	
				echo '
				<div id="bbcBox_message"></div>
				<div id="smileyBox_message"></div>'; 				
				template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');
				
			}
			
			else
			{	
				echo '
				<br class="clear" />
				<textarea name="body" rows="10" style="width: 95%;">', !empty($context['teampage']['p_body']) ? $context['teampage']['p_body'] : '', '</textarea>';
				
			}
			echo '
				<br /><br />
				<input class="button_submit" type="submit" value="', $txt['save'], '" />
			</form>
		</div>
		<span class="lowerframe"><span></span></span>';
		
}