<?php

/**
 * TeamPage.spanish
 *
 * @package Team Page
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014 Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

global $scripturl, $settings, $txt, $context;


// Buttons, sections, etc.
$txt['TeamPage'] = 'TeamPage';
$txt['TeamPage_button'] = 'Team Page';
$txt['TeamPage_main_button'] = 'Equipo';
$txt['TeamPage_page_main'] = 'Principal';
$txt['TeamPage_page_settings'] = 'Ajustes';
$txt['TeamPage_page_pages'] = 'Páginas';
$txt['TeamPage_page_page_edit'] = 'Editando la página';

// Moderators page
$txt['TeamPage_moderators'] = 'Moderadores';
$txt['TeamPage_moderators_description'] = 'Estos son los moderadores de '. $context['forum_name']. '';
$txt['TeamPages_boards_moderating'] = 'Secciones que modera';

// Permissions
$txt['TeamPage_permissions'] = 'Permisos para la Página de Equipo';
$txt['groups_view_teampage'] = 'Ver la Página de Equipo';
$txt['permissionname_view_teampage'] = 'Ver la Página de Equipo';
$txt['permissionhelp_view_teampage'] = 'Con este permiso el grupo de usuarios en cuestión podrá accesar a la página de equipo.';

// Errors
$txt['TeamPage_error_title_sub'] = 'Tienes que escribir un título y una subacción para la nueva página.';
$txt['TeamPage_error_title'] = 'No puedes dejar el título vacío.';
$txt['TeamPage_error_alnum_sub'] = 'La subacción debe contener solamente caracteres alfanuméricos (en letra minúscula).';
$txt['TeamPage_error_already_sub'] = 'Ya existe una página con esa subacción.';
$txt['TeamPage_error_cannot_mod'] = 'No se permite utilizar la subacción \'moderators\'.';
$txt['TeamPage_page_noexist'] = 'La página que estás intentando editar no existe.';
$txt['cannot_view_teampage'] = 'Lo sentimos, no tienes permisos para acceder a la página de equipo.';
$txt['no_more_groups'] = 'No hay grupos inactivos...';
$txt['no_groups_defined'] = '¡No hay grupos definidos para mostrar en la página de equipo! Por favor agrega algunos grupos. Los usuarios están siendo redirigidos al inicio al accesar a la página hasta que soluciones esto.';
$txt['team_groups_notp'] = 'Grupos sin asignar';
$txt['TeamPage_no_members'] = 'No hay usuarios disponibles...';
$txt['TeamPage_group_no_av'] = 'El grupo que estás tratando de agregar/mover no existe o no está disponible aquí.';
$txt['TeamPage_title_editor_empty'] = 'El cuerpo de la página se dejó en blanco.';

// Others
$txt['TeamPage_page_title'] = 'Título de la página';
$txt['TeamPage_page_subaction'] = 'Subacción de la página';
$txt['TeamPage_page_id'] = 'ID Página';
$txt['TeamPage_page_type_i'] = 'De tipo';
$txt['TeamPage_page_delete'] = 'Eliminar Página';
$txt['TeamPage_page_delete_short'] = 'Eliminar';
$txt['TeamPage_page_modify'] = 'Modificar Página';
$txt['TeamPage_page_modify_short'] = 'Modificar';
$txt['TeamPage_page_subaction_desc'] = '<span class="smalltext">Recuerda que <strong>únicamente</strong> puedes utilizar caracteres alfanuméricos (en minúscula).</span>';
$txt['TeamPage_add_page'] = 'Agregar Página';
$txt['TeamPage_page_is_text'] = '¿Es una página de texto?';
$txt['TeamPage_page_type'] = '¿Qué tipo de texto se permite?';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_groups'] = 'grupos';
$txt['TeamPage_no_pages'] = 'Actualmente no has creado ninguna página.';

// Descriptions
$txt['TeamPage_admin_description_settings'] = 'En esta sección podrás configurar los ajustes principales del mod Team Page.';
$txt['TeamPage_admin_description_pages'] = 'En esta sección podrás configurar las páginas personalizadas del mod Team Page.';
$txt['TeamPage_admin_what_page'] = 'Actualmente estás modificando la página con el nombre:';

// Settings.
$txt['TeamPage_enable'] = '¿Activar página de equipo?';
$txt['TeamPage_enable_modpage'] = '¿Activar la subpágina de moderadores?';
$txt['TeamPage_modpage_description'] = 'Escribe la descripción de la página de moderadores';
$txt['TeamPage_modpage_description_desc'] = 'Por defecto es: <i>'. $txt['TeamPage_moderators_description']. '</i>. Esto se mostrará si las descripciones de los grupos están activadas.';
$txt['TeamPage_show_badges'] = '¿Mostrar las placas en la página?';
$txt['TeamPage_show_avatars'] = '¿Mostrar los avatares en la página?';
$txt['TeamPage_show_avatars_desc'] = 'Esta opción también activará/desactivará el sitio web, enviar mp y en link de los últimos mensajes de la información de los usuarios.';
$txt['TeamPage_show_desc'] = '¿Mostrar la descripción del grupo en la página?';
$txt['TeamPage_additional_groups'] = '¿Mostrar los usuarios en grupos secundarios?';
$txt['TeamPage_settings_saved'] = 'Se han guardado tus ajustes. <a href="' . $scripturl . '?action=teampage;sa=%1" target="_self">Haz click aquí para ver</a>.';
$txt['TeamPage_manage_groups'] = 'Configurar los grupos';
$txt['TeamPage_manage_editor'] = 'Configurar el contenido de la página';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'Viendo la <a href="' . $scripturl . '?action=teampage">Página de Equipo</a>.';
$txt['TeamPage_pm'] = 'Enviar un mensaje privado';
$txt['TeamPage_showm'] = 'Mostrar los últimos mensajes';
$txt['TeamPage_last_login'] = 'Última conexión';
$txt['TeamPage_date_registered'] = 'Fecha de Registro';

// Inside column/table strings for group inclusion
$txt['groups_left'] = 'Grupos de la Izquierda';
$txt['groups_right'] = 'Grupos de la Derecha';
$txt['groups_bottom'] = 'Grupos de Abajo';
$txt['groups_id'] = 'ID del Grupo';
$txt['groups_name'] = 'Nombre del Grupo';
$txt['groups_move'] = 'Mover';
$txt['groups_action'] = 'Acción';
$txt['groups_order'] = 'Orden';
$txt['groups_place'] = 'Lugar';
$txt['groups_stars'] = 'Estrellas';

// Tooltips.
$txt['groups_move_left'] = 'Mover grupo al bloque izquierdo';
$txt['groups_move_right'] = 'Mover grupo al bloque derecho';
$txt['groups_move_bottom'] = 'Mover grupo al bloque de abajo';
$txt['groups_remove'] = 'Remover el grupo de la página';

// Push groups by step (ordering...)
$txt['groups_push_up'] = 'Mover el grupo un lugar hacia arriba';
$txt['groups_push_down'] = 'Mover el grupo un lugar hacia abajo';