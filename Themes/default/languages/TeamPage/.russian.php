<?php

/**
 * TeamPage.english
 *
 * @package Team Page
 * @version 5.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 * Translation and localization by WYLEK
 * @Russification website - https://wylek.ru/
 */

global $scripturl, $settings, $txt, $context;

// Buttons, sections, etc.
$txt['TeamPage'] = 'TeamPage';
$txt['TeamPage_button'] = 'Team Page';
$txt['TeamPage_main_button'] = 'Команда';
$txt['TeamPage_page_main'] = 'Главная';
$txt['TeamPage_page_settings'] = 'Настройки';
$txt['TeamPage_page_settings_desc'] = 'Основные настройки страницы команды, разрешения и другие параметры.';
$txt['TeamPage_page_settings_layout'] = 'Оформление';
$txt['TeamPage_page_pages'] = 'Страницы';
$txt['TeamPage_page_pages_desc'] = 'В этом разделе вы сможете управлять пользовательскими страницами команды.';
$txt['TeamPage_page_page_edit'] = 'Редактирование страницы';
$txt['TeamPage_page_groups'] = 'Управление группами';
$txt['TeamPage_page_mods'] = 'Управление модераторами';
$txt['TeamPage_page_Groups_desc'] = 'Вы можете перетащить каждую группу в нужное расположение и в любом порядке, она будет сохранена автоматически. Чтобы удалить группу, просто поместите ее в группы форума.';
$txt['TeamPage_page_Mods_desc'] = 'Здесь вы можете выбрать стиль для страницы модераторов и разделов, которые вы хотите включить.';

// Permissions
$txt['TeamPage_permissions'] = 'Разрешения для страницы команды';
$txt['permissiongroup_teampage_canAccess'] = 'Разрешения';
$txt['permissionname_teampage_canAccess'] = 'Доступ';
$txt['groups_teampage_canAccess'] = 'Доступ к странице команды';
$txt['permissionhelp_teampage_canAccess'] = 'Предоставление этого разрешения позволяет группе получить доступ к странице команды.';
$txt['permissionhelp_groups_teampage_canAccess'] = 'Предоставление этого разрешения позволяет группе получить доступ к странице команды.';
$txt['cannot_teampage_canAccess'] = 'Доступ к странице команды не разрешен.';

// Errors
$txt['TeamPage_error_disabled'] = 'Страница команды сейчас отключена.';
$txt['TeamPage_error_title_sub'] = 'Вы должны ввести название и подраздел для своей новой страницы.';
$txt['TeamPage_error_alnum_sub'] = 'Подраздел должен содержать только буквенно-цифровые символы.';
$txt['TeamPage_error_already_sub'] = 'Там уже есть страница с этим подразделом.';
$txt['TeamPage_page_noexist'] = 'Не удается найти пользовательскую страницу.';

// Pages
$txt['TeamPage_page_title'] = 'Название';
$txt['TeamPage_page_subaction'] = 'Подраздел';
$txt['TeamPage_page_subaction_desc'] = 'Помните, что вы можете использовать <strong>только</strong> буквенно-цифровые символы.';
$txt['TeamPage_page_details'] = 'Детали';
$txt['TeamPage_page_id'] = 'Идентификатор';
$txt['TeamPage_page_type'] = 'Тип';
$txt['TeamPage_page_type_select'] = 'Выберите тип страницы';
$txt['TeamPage_page_delete'] = 'Удаление страницы';
$txt['TeamPage_page_delete_short'] = 'Удалить';
$txt['TeamPage_page_order'] = 'Порядок страниц';
$txt['TeamPage_page_modify'] = 'Редактирование';
$txt['TeamPage_page_modify_short'] = 'Изменить';
$txt['TeamPage_add_page'] = 'Добавить страницу';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_mods'] = 'Модераторы';
$txt['TeamPage_page_type_groups'] = 'Группы';
$txt['TeamPage_no_pages'] = 'Вы еще не создали никаких пользовательских страниц.';
$txt['TeamPage_pages_added'] = 'Страница была успешно добавлена';
$txt['TeamPage_pages_updated'] = 'Страница успешно обновлена';
$txt['TeamPage_pages_deleted'] = 'Страницы успешно удалены';
$txt['TeamPage_pages_editing_page'] = 'Редактирование страницы %s';
$txt['TeamPage_page_modify_body'] = 'Содержимое страницы';
$txt['TeamPage_page_save_order'] = 'Сохранить';

// Groups strings
$txt['TeamPage_list_all_groups'] = 'Список идентификаторов группы';
$txt['TeamPage_groups_left'] = 'Группы слева';
$txt['TeamPage_groups_right'] = 'Группы справа';
$txt['TeamPage_groups_bottom'] = 'Группы снизу';
$txt['TeamPage_groups_forum'] = 'Группы форума';
$txt['TeamPage_groups_id'] = 'Идентификатор группы';
$txt['TeamPage_groups_name'] = 'Название группы';

// Moderators
$txt['TeamPage_mods_type'] = 'Тип списка';
$txt['TeamPage_mods_type_select'] = 'Выберите тип списка';
$txt['TeamPage_mods_type_user'] = 'Основанный на пользователях';
$txt['TeamPage_mods_type_board'] = 'Основанный на разделах';
$txt['TeamPage_mods_boards'] = 'Выберите разделы';

// Settings.
$txt['TeamPage_enable'] = 'Включить страницу команды?';
$txt['TeamPage_show_badges'] = 'Показать значки группы/иконки?';
$txt['TeamPage_show_avatars'] = 'Показать аватары для каждого участника?';
$txt['TeamPage_avatars_width'] = 'Ширина аватаров';
$txt['TeamPage_avatars_height'] = 'Высота аватаров';
$txt['TeamPage_addinfo_desc'] = 'Доступно только для левого и правого блоков';
$txt['TeamPage_show_personal'] = 'Показать надпись под аватаром';
$txt['TeamPage_show_custom'] = 'Показать подпись над аватаром';
$txt['TeamPage_show_description'] = 'Показать описания групп?';
$txt['TeamPage_show_posts'] = 'Показать количество сообщений';
$txt['TeamPage_show_website'] = 'Показать веб-сайт';
$txt['TeamPage_show_login'] = 'Показать последний вход';
$txt['TeamPage_show_registered'] = 'Показать дату регистрации';
$txt['TeamPage_manage_Groups'] = 'Управление группами';
$txt['TeamPage_manage_Mods'] = 'Управление модераторами';
$txt['TeamPage_manage_editor'] = 'Управление содержанием страницы';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'Просмотр страницы <a href="' . $scripturl . '?action=team">команды форума</a>.';
$txt['whoallow_teampage'] = 'Управление страницей <a href="' . $scripturl . '?action=team">команды форума</a>';
$txt['TeamPage_pm'] = 'Отправить личное сообщение';
$txt['TeamPage_showm'] = 'Показать последние сообщения';
$txt['TeamPage_last_login'] = 'Был здесь';
$txt['TeamPage_date_registered'] = 'Регистрация';
$txt['TeamPage_website'] = 'Веб-сайт';
$txt['TeamPages_boards_moderating'] = 'Приписан';
