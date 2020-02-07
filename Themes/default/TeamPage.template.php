<?php
// TeamPage Modification v3.5
// Created by ccbtimewiz (ccbtimewiz@dream-portal.net)


function template_moderators()
{
	
	global $context, $scripturl, $settings, $txt, $modSettings;;
	
	$alternate = array(false, false, false, false);
	
	if ($context['teampage']['onep'] == true)
	{
		echo '
		<div class="buttonlist floatright">
			<ul>';
			
			foreach ($context['teampage']['tpages'] as $pages)
			{
				
				echo '
				<li>
					<a', ($pages['sub_page'] == $context['teampage']['current_sa']) ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa='. $pages['sub_page']. '"><span class="last">', $pages['name_page'], '</span></a>
				</li>';
				
			}
			
		if (!empty($modSettings['TeamPage_enable_modpage']))
			echo '
				<li>
					<a', ($context['teampage']['current_sa'] == 'moderators') ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa=moderators"><span class="last">', $txt['TeamPage_moderators'], '</span></a>
				</li>';
			
		echo '
			</ul>
		</div>
		<br class="clear" /><br />';
	}

echo '
		<div class="cat_bar">
			<h3 class="catbg">', $txt['TeamPage_moderators'], '</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">';
		
		if ($context['teampage']['count_moderators'] >= 1)
		{
	
			foreach ($context['display_groups'] as $info)
			{
				
				if (!empty($modSettings['TeamPage_show_desc']) || !empty($modSettings['TeamPage_show_badges']))
				{
					echo '
					<span style="height: 3px; display: block"></span>
					<div class="description">';
						if (!empty($modSettings['TeamPage_show_desc']))
						echo  empty($modSettings['TeamPage_modpage_description']) ? $txt['TeamPage_moderators_description'] : $modSettings['TeamPage_modpage_description'];
	
						if (!empty($modSettings['TeamPage_show_badges']))
						echo  '
							<div class="floatright">', $info['image'], '</div>
							<div class="clear"></div>';
						
						echo '
					</div>';
				}
				break;
			}
			unset($info);
			foreach ($context['display_groups'] as $group)
			{
			
					// List the members.
					foreach ($group['members'] as $data)
					{
						foreach ($data as $member)
						{
							$alternate[0] = !$alternate[0];
							$window_class = $alternate[0] ? 'windowbg' : 'windowbg2';
							$idboard = explode(',',$member['moderating']['id']);
							$boardname = explode(',',$member['moderating']['name']);
							
							echo '
						<div class="', $window_class, ' tp_modblocks">
							<span class="topslice"><span></span></span>';
							
						// Show avatars?
						if (!empty($modSettings['TeamPage_show_avatars']))
							echo '
							
							<div class="tp_leftcontent">
							
								<div class="tp_avatar">
									<a href="', $scripturl, '?action=profile;u=', $member['id'], '"><img src="', !empty($member['avatar']['href']) ? $member['avatar']['href'] : ''. $settings['images_url'].'/teampage/default_avatar.png', '" alt="" style="width: 60px; height: 60px; border-radius: 100%;" /></a>
								</div><br />
								
								<a href="'.$scripturl.'?action=pm;sa=send;u='.$member['id'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/pm.png" alt="'.$txt['TeamPage_pm'].'" title="'.$txt['TeamPage_pm'].'" /></a>
								&nbsp;<a href="'.$scripturl.'?action=profile;area=showposts;sa=messages;u='.$member['id'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/showmessages.png" alt="'.$txt['TeamPage_showm'].'" title="'.$txt['TeamPage_showm'].'" /></a>
								',empty($member['web_url']) || empty($member['web_title']) ? '' : '&nbsp;<a href="'.$member['web_url'].'" title="'.$member['web_title'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/website.png" alt="'.$member['web_title'].'" title="'.$member['web_title'].'" /></a>' ,'
								
							</div>';
							
							echo '
							
							<div class="tp_user_information">
								<img class="tp_imgs" src="', $member['online']['image_href'], '" alt="', $member['online']['label'], '" />&nbsp;', $member['link'], '',empty($member['title']) ? '' : '&nbsp;-&nbsp;<strong>'.$member['title'].'</strong>' ,'<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/posts.png" alt="'.$txt['TeamPage_showm'].'" title="'.$txt['TeamPage_showm'].'" />&nbsp;'.$txt['posts'].': ', $member['posts'], '<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/register.png" alt="'.$txt['TeamPage_date_registered'].'" title="'.$txt['TeamPage_date_registered'].'" />&nbsp;'.$txt['TeamPage_date_registered'].': '.$member['date'].'<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/login.png" alt="'.$txt['TeamPage_last_login'].'" title="'.$txt['TeamPage_last_login'].'" />&nbsp;'.$txt['TeamPage_last_login'].': '.$member['last_login'].'<br />
								
								<dl class="tp_boards">
									<dt><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/boards.png" alt="'.$txt['TeamPages_boards_moderating'].'" title="'.$txt['TeamPages_boards_moderating'].'" />&nbsp;', $txt['TeamPages_boards_moderating'], ':&nbsp;</dt>
									<dd>
										<ul class="reset">';
										
								for ($i=0;$i < count($idboard);$i++)
								{
									echo '
											<li><a href="'.$scripturl.'?board='.$idboard[$i].'.0" title="'.$boardname[$i].'">'.$boardname[$i].'</a></li>';
								}
										
										
							echo '
										</ul>
									</dd>
								</dl>
							</div>
							<span class="botslice"><span></span></span>
						</div>';
		
						}
					}
			}
			unset($group);
			
		}
		
		else
		{
			
			echo '
				<div class="windowbg">
					<span class="topslice"><span></span></span>
						<div class="content">
							', $txt['TeamPage_no_members'], '
						</div>
					<span class="botslice"><span></span></span>
				</div>';
					
		}
			
	echo '
		<br class="clear" />
		</div>
		<span class="lowerframe"><span></span></span>';
		
		/* Pagination */
		if (!empty($context['page_index']))
			echo '
		<div class="pagesection">
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>
		</div>';
	
}
function template_main()
{
	global $context, $scripturl, $settings, $txt, $modSettings;
	
	// Start the team page.
	
	if ($context['teampage']['onep'] == true)
	{
		echo '
		<div class="buttonlist floatright">
			<ul>';
			
			foreach ($context['teampage']['tpages'] as $pages)
			{
				
				echo '
				<li>
					<a', ($pages['sub_page'] == $context['teampage']['current_sa']) ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa='. $pages['sub_page']. '"><span class="last">', $pages['name_page'], '</span></a>
				</li>';
				
			}
			
		if (!empty($modSettings['TeamPage_enable_modpage']))
			echo '
				<li>
					<a', ($context['teampage']['current_sa'] == 'moderators') ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa=moderators"><span class="last">', $txt['TeamPage_moderators'], '</span></a>
				</li>';
			
		echo '
			</ul>
		</div>
		<br class="clear" /><br />';
	}
	
	
echo '
	<div class="cat_bar">
		<h3 class="catbg">', $context['teampage']['name_page'], '</h3>
	</div>';
		
	// Let's list the group members on each side of the page.
	for ($i = 1; $i <= 2; $i++)
	{
		// Let's check if the array isn't empty
		if (!empty($context['display_groups'][$i]))
		{
			// Check which column we are working with.
			$column = ($i == 0 ? ($i + 1) : ($i - 1));
			
			echo '
			<div class="', !empty($context['display_groups'][2]) ? 'tp_blocks' : 'tp_nblock', ' ', ($column == 1 ? 'right' : 'left'), '">
				<span class="upperframe"><span></span></span>
				<div class="roundframe">';

			foreach ($context['display_groups'][$i] as $id => $group)
			{
				echo '
				<div class="title_bar">
					<h3 class="titlebg">
						<span class="ie6_header floatleft">', $group['name'], '</span>
					</h3>
				</div>';


			if (!empty($modSettings['TeamPage_show_desc']) || !empty($modSettings['TeamPage_show_badges']))
			{
				echo '
				<div class="description">';
					if (!empty($modSettings['TeamPage_show_desc']))
					echo  $group['description'];

					if (!empty($modSettings['TeamPage_show_badges']))
					echo  '
						<div class="floatright">', $group['image'], '</div>
						<div class="clear"></div>';
					
					echo '
				</div>';
			
			}

				// Check if there are any members in this group.
				if (!empty($group['members']))
				{
					// List the members.
					foreach ($group['members'] as $pos => $data)
					{
						foreach ($data as $member)
						{
							echo '
						<div class="windowbg">
							<span class="topslice"><span></span></span>';
							
						// Show avatars?
						if (!empty($modSettings['TeamPage_show_avatars']))
							echo '
							
							<div class="tp_leftcontent">
							
								<div class="tp_avatar">
									<a href="', $scripturl, '?action=profile;u=', $member['id'], '"><img src="', !empty($member['avatar']['href']) ? $member['avatar']['href'] : ''. $settings['images_url'].'/teampage/default_avatar.png', '" alt="" style="width: 60px; height: 60px; border-radius: 100%;" /></a>
								</div><br />
								
								<a href="'.$scripturl.'?action=pm;sa=send;u='.$member['id'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/pm.png" alt="'.$txt['TeamPage_pm'].'" title="'.$txt['TeamPage_pm'].'" /></a>
								&nbsp;<a href="'.$scripturl.'?action=profile;area=showposts;sa=messages;u='.$member['id'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/showmessages.png" alt="'.$txt['TeamPage_showm'].'" title="'.$txt['TeamPage_showm'].'" /></a>
								',empty($member['web_url']) || empty($member['web_title']) ? '' : '&nbsp;<a href="'.$member['web_url'].'" title="'.$member['web_title'].'"><img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/website.png" alt="'.$member['web_title'].'" title="'.$member['web_title'].'" /></a>' ,'
								
							</div>';
							
							echo '
							
							<div class="tp_user_information">
								<img class="tp_imgs" src="', $member['online']['image_href'], '" alt="', $member['online']['label'], '" />&nbsp;', $member['link'], '',empty($member['title']) ? '' : '&nbsp;-&nbsp;<strong>'.$member['title'].'</strong>' ,'<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/posts.png" alt="'.$txt['TeamPage_showm'].'" title="'.$txt['TeamPage_showm'].'" />&nbsp;'.$txt['posts'].': ', $member['posts'], '<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/register.png" alt="'.$txt['TeamPage_date_registered'].'" title="'.$txt['TeamPage_date_registered'].'" />&nbsp;'.$txt['TeamPage_date_registered'].': '.$member['date'].'<br />
								<img class="tp_imgs" src="'. $settings['default_images_url']. '/teampage/login.png" alt="'.$txt['TeamPage_last_login'].'" title="'.$txt['TeamPage_last_login'].'" />&nbsp;'.$txt['TeamPage_last_login'].': '.$member['last_login'].'
							</div>
							<span class="botslice"><span></span></span>
						</div>';
						}
					}
				}
				// No members? Let's tell them.
				else
				{
					echo '
					<div class="windowbg">
						<span class="topslice"><span></span></span>
							<div class="content">
								', $txt['TeamPage_no_members'], '
							</div>
						<span class="botslice"><span></span></span>
					</div>';
				}

				echo '
				<br/>';
			}
			unset($group);

	echo '
				</div>
		<span class="lowerframe"><span></span></span>
	</div>';
	
		}
	}
	
	echo '
	<br class="clear" />';

	// Now grabbing the half groups, if there are any.
	if (!empty($context['display_groups'][3]))
	{
		echo '
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="bordercolor">
					<tr>
						<td align="left" valign="top" class="windowbg2">
							<table border="0" cellspacing="1" cellpadding="4" width="100%" class="bordercolor">
								<tr class="titlebg">';
		
		// Return the group first.
		foreach ($context['display_groups'][3] as $id => $group)
		{
					echo '
									<td align="left" valign="top">
										' . $group['name'] . '&nbsp;', !empty($modSettings['TeamPage_show_badges']) ? '<div class="floatright">' . $group['image'] . '</div>' : '', '
									</td>';

		}		
		echo '
								</tr>
								<tr>';
		
		// Check if there are any members in this group.
		if (!empty($group['members']))
		{
			// List the members.
			foreach ($context['display_groups'][3] as $id => $group)
			{
				echo '
									<td width="15%" class="windowbg2" align="left" valign="top">';

				// Now the members.
				foreach ($group['members'] as $pos => $data)
				{						
					foreach ($data as $member)
						echo '
										<div style="padding-bottom: 3px;">&bull; ', $member['link'], '', (!empty($member['title']) ? '&nbsp;-&nbsp;<strong>' . $member['title'] . '</strong>' : ''), '</div>';
				}
			
				echo '
									</td>';
			}
			unset($group);
			
		}
		else
		{
				echo '
									<td width="15%" class="windowbg2" align="left" valign="top">
										', $txt['TeamPage_no_members'], '
									</td>';
			
		}
		echo '
								</tr>
							</table>';


	echo '
						</td>
					</tr>
				</table>';

	echo '
			</div>
			<span class="lowerframe"><span></span></span>';
		
	}
	
}

function template_t_main()
{
	global $context, $scripturl, $settings, $txt, $modSettings;
	
	// Start the team page.
	
	if ($context['teampage']['onep'] == true)
	{
		echo '
		<div class="buttonlist floatright">
			<ul>';
			
			foreach ($context['teampage']['tpages'] as $pages)
			{
				
				echo '
				<li>
					<a', ($pages['sub_page'] == $context['teampage']['current_sa']) ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa='. $pages['sub_page']. '"><span class="last">', $pages['name_page'], '</span></a>
				</li>';
				
			}
			
		if (!empty($modSettings['TeamPage_enable_modpage']))
			echo '
				<li>
					<a', ($context['teampage']['current_sa'] == 'moderators') ? ' class="active"' : '', ' href="', $scripturl, '?action=teampage;sa=moderators"><span class="last">', $txt['TeamPage_moderators'], '</span></a>
				</li>';
			
		echo '
			</ul>
		</div>
		<br class="clear" /><br />';
	}
	
	
echo '
	<div class="cat_bar">
		<h3 class="catbg">', $context['teampage']['name_page'], '</h3>
	</div>
	<div class="windowbg">
		<span class="topslice"><span></span></span>
		<div class="content">
			', $context['page']['print'], '
		</div>
		<span class="botslice"><span></span></span>
	</div>';
	
}

?>