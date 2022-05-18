<?php

/**
 * TeamPage.italian
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
$txt['TeamPage_page_main'] = 'Principale';
$txt['TeamPage_page_settings'] = 'Impostazioni';
$txt['TeamPage_page_settings_desc'] = 'Impostazioni principali per la pagina del Team, permessi e altre opzioni.';
$txt['TeamPage_page_settings_layout'] = 'Layout';
$txt['TeamPage_page_pages'] = 'Pagine';
$txt['TeamPage_page_pages_desc'] = 'In questa sezione è possibile gestire le pagine personalizzate di Team Page.';
$txt['TeamPage_page_page_edit'] = 'Modifica pagina';
$txt['TeamPage_page_groups'] = 'Configura gruppi';
$txt['TeamPage_page_mods'] = 'Configura moderatori';
$txt['TeamPage_page_Groups_desc'] = 'È possibile trascinare ogni gruppo nell\'ordine desiderato, che verrà salvato automaticamente. Per eliminare un gruppo, è sufficiente trascinarlo nei gruppi del forum.';
$txt['TeamPage_page_Mods_desc'] = 'Qui è possibile selezionare lo stile della pagina dei moderatori e le sezioni che si desidera includere.';

// Permissions
$txt['TeamPage_permissions'] = 'Permessi per Team Page';
$txt['permissiongroup_teampage_canAccess'] = 'Permessi di Team Page';
$txt['permissionname_teampage_canAccess'] = 'Visualizza la pagina del Team';
$txt['groups_teampage_canAccess'] = 'Visualizza la pagina del Team';
$txt['permissionhelp_teampage_canAccess'] = 'Se l\'utente può accedere alla pagina del Team.';
$txt['permissionhelp_groups_teampage_canAccess'] = 'Se l\'utente può accedere alla pagina del Team.';
$txt['cannot_teampage_canAccess'] = 'Non sei autorizzato ad accedere alla pagina del Team.';

// Errors
$txt['TeamPage_error_disabled'] = 'Team Page è attualmente disattivato.';
$txt['TeamPage_error_title_sub'] = 'Devi inserire un titolo e una sottoazione per la tua nuova pagina.';
$txt['TeamPage_error_alnum_sub'] = 'La sottoazione deve contenere soltanto caratteri alfanumerici.';
$txt['TeamPage_error_already_sub'] = 'Esiste già una pagina con quella sottoazione.';
$txt['TeamPage_page_noexist'] = 'Nessuna pagina trovata.';
$txt['TeamPage_groups_empty'] = 'I gruppi sono vuoti o non sono stati trovati utenti.';

// Pages
$txt['TeamPage_page_title'] = 'Titolo pagina';
$txt['TeamPage_page_subaction'] = 'Sottoazione della pagina';
$txt['TeamPage_page_subaction_desc'] = 'Ricorda che è possibile utilizzare <strong>solo</strong> caratteri alfanumerici.';
$txt['TeamPage_page_details'] = 'Dettagli';
$txt['TeamPage_page_id'] = 'ID pagina';
$txt['TeamPage_page_type'] = 'Tipo pagina';
$txt['TeamPage_page_type_select'] = 'Seleziona il tipo di pagina';
$txt['TeamPage_page_delete'] = 'Rimuovi pagina';
$txt['TeamPage_page_delete_short'] = 'Rimuovi';
$txt['TeamPage_page_order'] = 'Ordine di pagina';
$txt['TeamPage_page_modify'] = 'Modifica pagina';
$txt['TeamPage_page_modify_short'] = 'Modifica';
$txt['TeamPage_add_page'] = 'Aggiungi pagina';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_mods'] = 'Moderatori';
$txt['TeamPage_page_type_groups'] = 'Gruppi';
$txt['TeamPage_no_pages'] = 'Attualmente non hai creato nessuna pagina.';
$txt['TeamPage_pages_added'] = 'La pagina è stata aggiunta con successo';
$txt['TeamPage_pages_updated'] = 'Pagina aggiornata con successo';
$txt['TeamPage_pages_deleted'] = 'Pagine eliminate con successo';
$txt['TeamPage_pages_editing_page'] = 'Modifica della pagina %s';
$txt['TeamPage_page_modify_body'] = 'Contenuto della pagina';
$txt['TeamPage_page_save_order'] = 'Salva';

// Groups strings
$txt['TeamPage_list_all_groups'] = 'Elenco ID gruppo';
$txt['TeamPage_groups_left'] = 'Gruppi di sinistra';
$txt['TeamPage_groups_right'] = 'Gruppi di destra';
$txt['TeamPage_groups_bottom'] = 'Gruppi in basso';
$txt['TeamPage_groups_forum'] = 'Gruppi del forum';
$txt['TeamPage_groups_id'] = 'ID gruppo';
$txt['TeamPage_groups_name'] = 'Nome del gruppo';

// Moderators
$txt['TeamPage_mods_type'] = 'Tipo di elenco';
$txt['TeamPage_mods_type_select'] = 'Selezionare il tipo di elenco';
$txt['TeamPage_mods_type_user'] = 'Basato sugli utenti';
$txt['TeamPage_mods_type_board'] = 'Basato sulle sezioni';
$txt['TeamPage_mods_boards'] = 'Seleziona sezioni';

// Settings.
$txt['TeamPage_enable'] = 'Attivare la pagina del Team?';
$txt['TeamPage_show_badges'] = 'Mostrare i badge dei gruppi nella pagina?';
$txt['TeamPage_show_avatars'] = 'Mostrare gli avatar per ciascun utente?';
$txt['TeamPage_avatars_width'] = 'Larghezza degli avatar';
$txt['TeamPage_avatars_height'] = 'Altezza degli avatar';
$txt['TeamPage_addinfo_desc'] = 'Disponibile solo per i blocchi destro e sinistro';
$txt['TeamPage_show_personal'] = 'Mostra il testo personale';
$txt['TeamPage_show_custom'] = 'Mostra il titolo personalizzato';
$txt['TeamPage_show_description'] = 'Mostrare le descrizioni dei gruppi?';
$txt['TeamPage_show_posts'] = 'Mostra il numero dei post';
$txt['TeamPage_show_website'] = 'Mostra sito web';
$txt['TeamPage_show_login'] = 'Mostra ultimo accesso';
$txt['TeamPage_show_registered'] = 'Mostra data di registrazione';
$txt['TeamPage_manage_Groups'] = 'Configura i gruppi';
$txt['TeamPage_manage_Mods'] = 'Configura i moderatori';
$txt['TeamPage_manage_editor'] = 'Configura il contenuto della pagina';
$txt['TeamPage_show_members_ag'] = 'Mostra gruppi secondari';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'Sta visualizzando la pagina del <a href="' . $scripturl . '?action=team">Team</a>.';
$txt['whoallow_teampage'] = 'Sta configurando la pagina del <a href="' . $scripturl . '?action=team">Team</a>.';
$txt['TeamPage_pm'] = 'Invia un messaggio privato';
$txt['TeamPage_showm'] = 'Mostra i messaggi recenti';
$txt['TeamPage_last_login'] = 'Ultimo accesso';
$txt['TeamPage_date_registered'] = 'Data di registrazione';
$txt['TeamPage_website'] = 'Sito web';
$txt['TeamPages_boards_moderating'] = 'Moderazione sezioni';
