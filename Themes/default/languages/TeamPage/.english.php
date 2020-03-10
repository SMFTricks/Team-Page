<?php

/**
 * TeamPage.english
 *
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $scripturl, $settings, $txt, $context;

// Buttons, sections, etc.
$txt['TeamPage'] = 'TeamPage';
$txt['TeamPage_button'] = 'Team Page';
$txt['TeamPage_main_button'] = 'Team';
$txt['TeamPage_page_main'] = 'Main';
$txt['TeamPage_page_settings'] = 'Settings';
$txt['TeamPage_page_settings_desc'] = 'Main settings for the team page, permissions and other options.';
$txt['TeamPage_page_settings_layout'] = 'Layout';
$txt['TeamPage_page_pages'] = 'Pages';
$txt['TeamPage_page_pages_desc'] = 'In this section you\'ll be able to manage the Team Page custom pages.';
$txt['TeamPage_page_page_edit'] = 'Editing Page';
$txt['TeamPage_page_groups'] = 'Managing groups';
$txt['TeamPage_page_mods'] = 'Managing moderators';
$txt['TeamPage_page_Groups_desc'] = 'You can drag and drop each group into the desired position and order, it will save automatically. To delete a group just drop it into the forum groups.';
$txt['TeamPage_page_Mods_desc'] = 'Here you can select the style for your moderators page and the boards you want to include.';

// Moderators page
$txt['TeamPage_moderators'] = 'Moderators page';
$txt['TeamPage_moderators_description'] = 'These are the moderators of '. $context['forum_name']. '';
$txt['TeamPages_boards_moderating'] = 'Boards Moderating';

// Permissions
$txt['TeamPage_permissions'] = 'Permissions for Team Page';
$txt['permissiongroup_teampage_canAccess'] = 'Team Page permissions';
$txt['permissionname_teampage_canAccess'] = 'Access Team Page';
$txt['groups_teampage_canAccess'] = 'Access Team Page';
$txt['permissionhelp_teampage_canAccess'] = 'If the user can access the team page.';
$txt['permissionhelp_groups_teampage_canAccess'] = 'If the user can access the team page.';
$txt['cannot_teampage_canAccess'] = ' You\'re not allowed to access the team page.';

// Errors
$txt['TeamPage_error_title_sub'] = 'You have to enter a title and a subaction for your new page.';
$txt['TeamPage_error_alnum_sub'] = 'The subaction must contain only alphanumeric characters.';
$txt['TeamPage_error_already_sub'] = 'There is already a page with that subaction.';
$txt['TeamPage_error_cannot_mod'] = 'You can\'t use the \'moderators\' subaction.';
$txt['TeamPage_page_noexist'] = 'Unable to find a custom page.';
$txt['cannot_view_teampage'] = 'Sorry, but you\'re not allowed to access to the team page.';
$txt['no_more_groups'] = 'There are no inactive groups...';
$txt['no_groups_defined'] = 'No groups are defined to show on the team page! Please add some groups. Users are being redirected to the index on accessing the page until you resolve this.';
$txt['team_groups_notp'] = 'Groups not placed';
$txt['TeamPage_no_members'] = 'No members available...';
$txt['TeamPage_group_no_av'] = 'The group you\'re trying to add/move does not exist or is not available here.';
$txt['TeamPage_title_editor_empty'] = 'The page body is not defined.';

// Pages
$txt['TeamPage_page_title'] = 'Page title';
$txt['TeamPage_page_subaction'] = 'Page subaction';
$txt['TeamPage_page_subaction_desc'] = 'Remember that you can <strong>only</strong> use alphanumeric characters.';
$txt['TeamPage_page_details'] = 'Details';
$txt['TeamPage_page_id'] = 'Page ID';
$txt['TeamPage_page_type'] = 'Page Type';
$txt['TeamPage_page_type_select'] = 'Select the page type';
$txt['TeamPage_page_delete'] = 'Delete Page';
$txt['TeamPage_page_delete_short'] = 'Delete';
$txt['TeamPage_page_order'] = 'Page Order';
$txt['TeamPage_page_modify'] = 'Modify Page';
$txt['TeamPage_page_modify_short'] = 'Modify';
$txt['TeamPage_add_page'] = 'Add Page';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_mods'] = 'Moderators';
$txt['TeamPage_page_type_groups'] = 'Groups';
$txt['TeamPage_no_pages'] = 'Currently you have not created any custom pages.';
$txt['TeamPage_pages_added'] = 'Page was successfully added';
$txt['TeamPage_pages_updated'] = 'Page successfully updated';
$txt['TeamPage_pages_deleted'] = 'Pages successfully deleeted';
$txt['TeamPage_pages_editing_page'] = 'Editing %s page';
$txt['TeamPage_page_modify_body'] = 'Page content';
$txt['TeamPage_page_save_order'] = 'Save order';

// Groups strings
$txt['TeamPage_list_all_groups'] = 'Group ID listing';
$txt['TeamPage_groups_left'] = 'Left Groups';
$txt['TeamPage_groups_right'] = 'Right Groups';
$txt['TeamPage_groups_bottom'] = 'Bottom Groups';
$txt['TeamPage_groups_forum'] = 'Forum Groups';
$txt['TeamPage_groups_id'] = 'Group ID';
$txt['TeamPage_groups_name'] = 'Group Name';
$txt['TeamPage_groups_move'] = 'Move';
$txt['TeamPage_groups_action'] = 'Action';
$txt['TeamPage_groups_order'] = 'Order';
$txt['TeamPage_groups_place'] = 'Place';
$txt['TeamPage_groups_stars'] = 'Stars';
$txt['TeamPage_groups_move_left'] = 'Move group to the left side';
$txt['TeamPage_groups_move_right'] = 'Move group to the right side';
$txt['TeamPage_groups_move_bottom'] = 'Move group to the bottom';
$txt['TeamPage_groups_remove'] = 'Remove group from team page';

// Moderators
$txt['TeamPage_mods_type'] = 'Type of list';
$txt['TeamPage_mods_type_select'] = 'Select type of list';
$txt['TeamPage_mods_type_user'] = 'Users based';
$txt['TeamPage_mods_type_board'] = 'Boards based';
$txt['TeamPage_mods_boards'] = 'Select boards';

// Settings.
$txt['TeamPage_enable'] = 'Enable the team page?';
$txt['TeamPage_show_badges'] = 'Display group badges/icons?';
$txt['TeamPage_show_avatars'] = 'Display avatars for each member?';
$txt['TeamPage_addinfo_desc'] = 'Only available for left and right blocks';
$txt['TeamPage_show_personal'] = 'Display personal text';
$txt['TeamPage_show_custom'] = 'Display custom title';
$txt['TeamPage_show_description'] = 'Display group descriptions?';
$txt['TeamPage_show_posts'] = 'Display post count';
$txt['TeamPage_show_website'] = 'Display website';
$txt['TeamPage_show_login'] = 'Display last login';
$txt['TeamPage_show_registered'] = 'Display register date';
$txt['TeamPage_additional_groups'] = 'Display members in their secondary groups?';
$txt['TeamPage_additional_groups_desc'] = 'This setting will display members in multiple groups if they\'re part of any additional groups.';
$txt['TeamPage_settings_saved'] = 'Your settings have been saved. <a href="' . $scripturl . '?action=team;sa=%1">Click here to view them</a>.';
$txt['TeamPage_manage_Groups'] = 'Manage the groups';
$txt['TeamPage_manage_Mods'] = 'Manage the moderators';
$txt['TeamPage_manage_editor'] = 'Manage the page content';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'Viewing the forum <a href="' . $scripturl . '?action=team">Team</a>.';
$txt['TeamPage_pm'] = 'Send a personal message';
$txt['TeamPage_showm'] = 'Show the last posts';
$txt['TeamPage_last_login'] = 'Last active';
$txt['TeamPage_date_registered'] = 'Member since';
$txt['TeamPage_website'] = 'Website';