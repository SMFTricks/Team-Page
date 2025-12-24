<?php

/**
 * TeamPage.english
 *
 * @package Team Page
 * @version 5.4
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $scripturl, $settings, $txt, $context;

// Buttons, sections, etc.
$txt['TeamPage'] = 'Pagina Echipei';
$txt['TeamPage_button'] = 'Pagina Echipei';
$txt['TeamPage_main_button'] = 'Echipa';
$txt['TeamPage_page_main'] = 'Principal';
$txt['TeamPage_page_settings'] = 'Setări';
$txt['TeamPage_page_settings_desc'] = 'Principalele setări pentru pagina echipei, permisiuni și alte opțiuni.';
$txt['TeamPage_page_settings_layout'] = 'Aspect';
$txt['TeamPage_page_pages'] = 'Pagini';
$txt['TeamPage_page_pages_desc'] = 'În această secțiune vei putea gestiona paginile personalizate ale paginii Echipei.';
$txt['TeamPage_page_page_edit'] = 'Editare pagină';
$txt['TeamPage_page_groups'] = 'Gestionare grupuri';
$txt['TeamPage_page_mods'] = 'Administrare moderatori';
$txt['TeamPage_page_Groups_desc'] = 'Poți trage și plasa fiecare grup în poziția și ordinea dorite, acesta se va salva automat. Pentru a șterge un grup trebuie doar să-l plasați în grupurile forumului.';
$txt['TeamPage_page_Mods_desc'] = 'Aici poți selecta stilul pentru pagina de moderatori și secțiunile pe care dorești să le incluzi.';

// Permissions
$txt['TeamPage_permissions'] = 'Permisiuni pentru pagina Echipei';
$txt['permissiongroup_teampage_canAccess'] = 'Permisiuni pagina echipei';
$txt['permissionname_teampage_canAccess'] = 'Accesează pagina Echipei';
$txt['groups_teampage_canAccess'] = 'Accesează pagina echipei';
$txt['permissionhelp_teampage_canAccess'] = 'Dacă utilizatorul poate accesa pagina echipei.';
$txt['permissionhelp_groups_teampage_canAccess'] = 'Dacă utilizatorul poate accesa pagina echipei.';
$txt['cannot_teampage_canAccess'] = ' Nu ai permisiunea să accesezi pagina echipei.';

// Errors
$txt['TeamPage_error_disabled'] = 'Pagina Echipei este momentan dezactivată.';
$txt['TeamPage_error_title_sub'] = 'Trebuie să introduci un titlu și o sub acțiune pentru pagina nouă.';
$txt['TeamPage_error_alnum_sub'] = 'Sub acțiunea trebuie să conțină doar caractere alfanumerice.';
$txt['TeamPage_error_already_sub'] = 'Există deja o pagină cu acea sub acțiune.';
$txt['TeamPage_page_noexist'] = 'Nu s-a găsit o pagină personalizată.';
$txt['TeamPage_groups_empty'] = 'Grupurile sunt goale sau nici un utilizator nu a fost găsit.';

// Pages
$txt['TeamPage_page_title'] = 'Titlu pagină';
$txt['TeamPage_page_subaction'] = 'Sub acțiune pagină';
$txt['TeamPage_page_subaction_desc'] = 'Amintește-ți că poți folosi <strong>doar</strong> caractere alfanumerice.';
$txt['TeamPage_page_details'] = 'Detalii';
$txt['TeamPage_page_id'] = 'ID Pagină';
$txt['TeamPage_page_type'] = 'Tip Pagină';
$txt['TeamPage_page_type_select'] = 'Selectează tipul paginii';
$txt['TeamPage_page_delete'] = 'Șterge Pagină';
$txt['TeamPage_page_delete_short'] = 'Șterge';
$txt['TeamPage_page_order'] = 'Ordinea paginilor';
$txt['TeamPage_page_modify'] = 'Modifică pagina';
$txt['TeamPage_page_modify_short'] = 'Modifică';
$txt['TeamPage_add_page'] = 'Adaugă pagină';
$txt['TeamPage_page_type_bbc'] = 'BBC';
$txt['TeamPage_page_type_html'] = 'HTML';
$txt['TeamPage_page_type_mods'] = 'Moderatori';
$txt['TeamPage_page_type_groups'] = 'Grupuri';
$txt['TeamPage_no_pages'] = 'Momentan nu ai creat nicio pagină personalizată.';
$txt['TeamPage_pages_added'] = 'Pagina a fost adăugată cu succes';
$txt['TeamPage_pages_updated'] = 'Pagină actualizată cu succes';
$txt['TeamPage_pages_deleted'] = 'Pagini șterse cu succes';
$txt['TeamPage_pages_editing_page'] = 'Editare %s pagină';
$txt['TeamPage_page_modify_body'] = 'Conținut pagină';
$txt['TeamPage_page_save_order'] = 'Salvează ordinea';

// Groups strings
$txt['TeamPage_list_all_groups'] = 'Grup ID listă';
$txt['TeamPage_groups_left'] = 'Grupuri stânga';
$txt['TeamPage_groups_right'] = 'Grupuri derapta';
$txt['TeamPage_groups_bottom'] = 'Grupuri jos';
$txt['TeamPage_groups_forum'] = 'Grupuri Forum';
$txt['TeamPage_groups_id'] = 'ID Grup';
$txt['TeamPage_groups_name'] = 'Nume grup';

// Moderators
$txt['TeamPage_mods_type'] = 'Tipul de listă';
$txt['TeamPage_mods_type_select'] = 'Selectează tipul de listă';
$txt['TeamPage_mods_type_user'] = 'Bazat pe utilizatori';
$txt['TeamPage_mods_type_board'] = 'Bazat pe secțiuni';
$txt['TeamPage_mods_boards'] = 'Selectează secțiuni';
$txt['TeamPage_mods_boards_description'] = 'Aceștia sunt moderatorii forumului.';

// Settings.
$txt['TeamPage_enable'] = 'Activezi pagina echipei?';
$txt['TeamPage_show_badges'] = 'Afișează insignele/pictogramele grupului?';
$txt['TeamPage_show_avatars'] = 'Afișează avatare pentru fiecare membru?';
$txt['TeamPage_avatars_width'] = 'Lățimea avatarelor';
$txt['TeamPage_avatars_height'] = 'Înălțimea avatarelor';
$txt['TeamPage_addinfo_desc'] = 'Disponibil doar pentru blocuri stânga și dreapta';
$txt['TeamPage_show_personal'] = 'Afișează textul personal';
$txt['TeamPage_show_custom'] = 'Afișează titlu personalizat';
$txt['TeamPage_show_description'] = 'Afișează descrierea grupului?';
$txt['TeamPage_show_posts'] = 'Afișează numărul de postări';
$txt['TeamPage_show_website'] = 'Afișează site-ul';
$txt['TeamPage_show_login'] = 'Afișează ultima conectare';
$txt['TeamPage_show_registered'] = 'Afișează data înregistrării';
$txt['TeamPage_manage_Groups'] = 'Gestionează grupurile';
$txt['TeamPage_manage_Mods'] = 'Gestionează moderatorii';
$txt['TeamPage_manage_editor'] = 'Gestionează conținutul paginii';
$txt['TeamPage_show_members_ag'] = 'Afișează membrii în grupuri adiționale';

// Outside strings
$txt['TeamPage_whoall_teampage'] = 'Vizualizează secțiunea <a href="' . $scripturl . '?action=team">Echipa</a>.';
$txt['whoallow_teampage'] = 'Gestionează pagina <a href="' . $scripturl . '?action=team">Echipa</a>.';
$txt['TeamPage_pm'] = 'Trimite un mesaj personal';
$txt['TeamPage_showm'] = 'Arată ultimele mesaje';
$txt['TeamPage_last_login'] = 'Ultima dată activ';
$txt['TeamPage_date_registered'] = 'Membru din';
$txt['TeamPage_website'] = 'Website';
$txt['TeamPages_boards_moderating'] = 'Moderarea Secțiunilor';

// Custom Fields
$txt['TeamPage_show_custom_fields'] = 'Arată câmpurile de profil personalizate';

// Sorting
$txt['TeamPage_sort_by'] = 'Sortează după';
$txt['TeamPage_sort_by_id'] = 'ID';
$txt['TeamPage_sort_by_name'] = 'Nume';
$txt['TeamPage_sort_by_desc'] = 'Selectează ordinea implicită pentru utilizatorii din toate paginile.<br>Membrii din grupuri suplimentare sunt sortați separat sub membrii principali.';