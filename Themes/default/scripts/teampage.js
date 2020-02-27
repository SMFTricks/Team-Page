
/**
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

// Sortable for the groups management
$(document).ready(function() {
	// Left
	$('#tp_group_sort_left').sortable({
		connectWith: '.information',
		placeholder: 'tp_placeholder_sort',
		update: function( event, ui ) {
			savePositions($("#tp_group_sort_left").sortable('toArray', {attribute : 'group-id'}), 'left');
		}
	}).disableSelection();

	// Right
	$('#tp_group_sort_right').sortable({
		connectWith: '.information',
		placeholder: 'tp_placeholder_sort',
		update: function( event, ui ) {
			savePositions($("#tp_group_sort_right").sortable('toArray', {attribute : 'group-id'}), 'right');
		}
	}).disableSelection();

	// Bottom
	$('#tp_group_sort_bottom').sortable({
		connectWith: '.information',
		placeholder: 'tp_placeholder_sort',
		update: function( event, ui ) {
			savePositions($("#tp_group_sort_bottom").sortable('toArray', {attribute : 'group-id'}), 'bottom');
		}
	}).disableSelection();

	// All Groups
	$('#tp_group_sort_all').sortable({
		connectWith: '.information',
		placeholder: 'tp_placeholder_sort',
		receive: function( event, ui ) {
			savePositions($("#tp_group_sort_all").sortable('toArray', {attribute : 'group-id'}), 'all', true);
		}
	}).disableSelection();
});

// Update and save order
function savePositions(tp_groups, placement, group_delete = false) {
	var groups = [];
	var direction = (placement != undefined ? placement : 'left');

	if (tp_groups != undefined)
		groups = tp_groups;

	if (groups.length > 0) {
		$.ajax({
			url: smf_scripturl + "?action=admin;area=teampage;sa=sort",
			type: 'POST',
			dataType: 'json',
			data: {
				page: document.getElementById('tp_manage_groups').getAttribute('page-id'),
				delete: (group_delete == true ? 1 : 0),
				groups: groups,
				placement: direction,
			}, success: function(response) {
				console.log(response);
			}
		});
	}
}