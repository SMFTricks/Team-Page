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
$txt['TeamPage'] = 'Página de equipo';
$txt['TeamPage_button'] = 'Página de equipo';
$txt['TeamPage_main_button'] = 'Equipo';
$txt['TeamPage_page_main'] = 'Principal';
$txt['TeamPage_page_settings'] = 'Ajustes';
$txt['TeamPage_page_settings_desc'] = 'Ajustes principales para la página de equipo, permisos y otras opciones.';
$txt['TeamPage_page_settings_layout'] = 'Diseño';
$txt['TeamPage_page_pages'] = 'Páginas';
$txt['TeamPage_page_pages_desc'] = 'En esta sección podrás configurar las páginas personalizables del Equipo.';
$txt['TeamPage_page_page_edit'] = 'Editando  página';
$txt['TeamPage_page_groups'] = 'Configurando grupos';
$txt['TeamPage_page_mods'] = 'Configurando moderadores';
$txt['TeamPage_page_Groups_desc'] = 'Puedes arrastrar y soltar cada grupo en la posición y orden deseado, se guardará automáticamente. Para borrar un grupo simplemente suéltalo en los grupos del foro.';
$txt['TeamPage_page_Mods_desc'] = 'Aquí puedes seleccionar el estilo para la página de moderadores y los foros que quieres incluir.';

// Permissions
$txt['TeamPage_permissions'] = 'Permisos para la página de equipo';
$txt['permissiongroup_teampage_canAccess'] = 'Permisos para la página de equipo';
$txt['permissionname_teampage_canAccess'] = 'Ver Página de Equipo';
$txt['groups_teampage_canAccess'] = 'Ver Página de Equipo';
$txt['permissionhelp_teampage_canAccess'] = 'Si el usuario puede acceder a la página de equipo.';
$txt['permissionhelp_groups_teampage_canAccess'] = 'Si el usuario puede acceder a la página de equipo.';
$txt['cannot_teampage_canAccess'] = ' No tienes permiso para ver la página de equipo';

// Errors
$txt['TeamPage_error_disabled'] = 'La página de equipo está actualmente deshabilitada.';
$txt['TeamPage_error_title_sub'] = 'Tienes que introducir un título y una sub-acción para la página';
$txt['TeamPage_error_alnum_sub'] = 'La sub-acción debe contener solamente caracteres alfanuméricos.';
$txt['TeamPage_error_already_sub'] = 'Ya existe una página con esa sub-acción.';
$txt['TeamPage_page_noexist'] = 'No se encontró esa página.';

// Pages
$txt['TeamPage_page_title'] = 'Título de la página';
$txt['TeamPage_page_subaction'] = 'Sub-acción de la página';
$txt['TeamPage_page_subaction_desc'] = 'Recuerda que solo <strong>puedes</strong> usar caracteres alfanuméricos.';
$txt['TeamPage_page_details'] = 'Detalles';
$txt['TeamPage_page_id'] = 'ID de Página';
$txt['TeamPage_page_type'] = 'Tipo de Página';
$txt['TeamPage_page_type_select'] = 'Selecciona el tipo de página';
$txt['TeamPage_page_delete'] = 'Eliminar Página';
$txt['TeamPage_page_delete_short'] = 'Eliminar';
$txt['TeamPage_page_order'] = 'Orden de Página';
$txt['TeamPage_page_modify'] = 'Modificar Página';
$txt['TeamPage_page_modify_short'] = 'Modificar';
$txt['TeamPage_add_page'] = 'Agregar Página';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_mods'] = 'Moderatores';
$txt['TeamPage_page_type_groups'] = 'Grupos';
$txt['TeamPage_no_pages'] = 'Actualmente no has creado ninguna página personalizable.';
$txt['TeamPage_pages_added'] = 'Página fue agregada exitosamente';
$txt['TeamPage_pages_updated'] = 'Page fue actualizada exitosamente';
$txt['TeamPage_pages_deleted'] = 'Página fue eliminada exitosamente';
$txt['TeamPage_pages_editing_page'] = 'Editando la página %s';
$txt['TeamPage_page_modify_body'] = 'Contenido de página';
$txt['TeamPage_page_save_order'] = 'Guardar orden';

// Groups strings
$txt['TeamPage_list_all_groups'] = 'Group ID listing';
$txt['TeamPage_groups_left'] = 'Grupos Izquierda';
$txt['TeamPage_groups_right'] = 'Grupos Derecha';
$txt['TeamPage_groups_bottom'] = 'Grupos Abajo';
$txt['TeamPage_groups_forum'] = 'Grupos del Foro';
$txt['TeamPage_groups_id'] = 'ID Grupo';
$txt['TeamPage_groups_name'] = 'Nombre de Grupo';

// Moderators
$txt['TeamPage_mods_type'] = 'Tipo de lista';
$txt['TeamPage_mods_type_select'] = 'Selecciona tipo de lista';
$txt['TeamPage_mods_type_user'] = 'Basada en usuarios';
$txt['TeamPage_mods_type_board'] = 'Basada en foros';
$txt['TeamPage_mods_boards'] = 'Seleccionar foros';

// Settings.
$txt['TeamPage_enable'] = 'Activar Página de Equipo';
$txt['TeamPage_show_badges'] = 'Mostrar placas/iconos de grupo';
$txt['TeamPage_show_avatars'] = 'Mostrar avatares para cada usuario';
$txt['TeamPage_avatars_width'] = 'Ancho de avatares';
$txt['TeamPage_avatars_height'] = 'Alto de avatares';
$txt['TeamPage_addinfo_desc'] = 'Solo disponible para los bloques de izquierda y derecha';
$txt['TeamPage_show_personal'] = 'Mostrar texto personal';
$txt['TeamPage_show_custom'] = 'Mostrar título personalizado';
$txt['TeamPage_show_description'] = 'Mostrar descripciones de grupo';
$txt['TeamPage_show_posts'] = 'Mostrar conteo de mensajes';
$txt['TeamPage_show_website'] = 'Mostrar website';
$txt['TeamPage_show_login'] = 'mostrar última vez activo';
$txt['TeamPage_show_registered'] = 'Mostrar fecha de registro';
$txt['TeamPage_manage_Groups'] = 'Configurar grupos';
$txt['TeamPage_manage_Mods'] = 'Manage the moderators';
$txt['TeamPage_manage_editor'] = 'Configurar el contenido de la página';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'viendo la página de <a href="' . $scripturl . '?action=team">Equipo</a>.';
$txt['whoallow_teampage'] = 'configurando la página de <a href="' . $scripturl . '?action=team">Equipo</a>.';
$txt['TeamPage_pm'] = 'Enviar mensaje personal';
$txt['TeamPage_showm'] = 'Ver mensajes recientes';
$txt['TeamPage_last_login'] = 'Última vez activo';
$txt['TeamPage_date_registered'] = 'Miembro desde';
$txt['TeamPage_website'] = 'Website';
$txt['TeamPages_boards_moderating'] = 'Foros que modera';
