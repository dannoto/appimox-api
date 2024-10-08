<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = '';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* API */
// $route['product'] = 'api/Product';
// $route['product/(:any)'] = 'api/Product/$1';
// $route['product/(:num)']['PUT'] = 'api/Product/$1';
// $route['product/(:num)']['DELETE'] = 'api/Product/$1';
$route['reGenToken'] = 'api/Token/reGenToken';
$route['check_session'] = 'api/User/check_session';


$route['register'] = 'api/User/register';
$route['login'] = 'api/User/login';
$route['logout'] = 'api/User/logout';
$route['recovery'] = 'api/User/recovery';



$route['set_user_type'] = 'api/User/set_user_type';

$route['preferences'] = 'api/User/preferences';
$route['check_preferences_init'] = 'api/User/check_preferences_init';
$route['check_creci_init'] = 'api/User/check_creci_init';
$route['check_init_preferences'] = 'api/User/check_init_preferences';

// creci
$route['check_creci_validation'] = 'api/User/check_creci_validation';


$route['update_client_location'] = 'api/User/update_client_location';

// creci

$route['inactive'] = 'api/User/inactive';

$route['add_notification_token'] = 'api/User/add_notification_token';



$route['nf_new_favorit'] = 'api/Notification/nf_new_favorit';

// Propertys Broker
$route['broker_propertys'] = 'api/Propertys/broker_propriety';
$route['broker_propertys_partner'] = 'api/Propertys/broker_propertys_partner';

$route['search_broker_propertys'] = 'api/Propertys/search_broker_propertys';
$route['add_broker_property'] = 'api/Propertys/add_broker_property';
$route['update_broker_property'] = 'api/Propertys/update_broker_property';
$route['add_broker_property_others_images'] = 'api/Propertys/add_broker_property_others_images';
$route['get_broker_property_data'] = 'api/Propertys/get_broker_property_data';
$route['delete_broker_property'] = 'api/Propertys/delete_broker_property';
$route['get_broker_property_home'] = 'api/Propertys/get_broker_property_home';

$route['get_property_new'] = 'api/Propertys/get_property_new';

// delete imagenes

$route['delete_property_image'] = 'api/Propertys/delete_property_image';

// Client


// Property Broker

// Locations
$route['get_locations'] = 'api/Locations/get_locations';

$route['get_propertys_by_range'] = 'api/Propertys/get_propertys_by_range';
$route['get_propertys_by_range_filter'] = 'api/Propertys/get_propertys_by_range_filter';

$route['get_broker_by_range'] = 'api/Propertys/get_broker_by_range';
$route['get_broker_by_range_filter'] = 'api/Propertys/get_broker_by_range_filter';

$route['get_broker_associate_properties'] = 'api/Propertys/get_broker_associate_properties';



// Locations
$route['update_password'] = 'api/User/update_password';


// Favorits
$route['add_favorit'] = 'api/User/add_favorit';
$route['get_favorits'] = 'api/User/get_favorits';
$route['search_get_favorits'] = 'api/User/search_get_favorits';

$route['delete_favorit'] = 'api/User/delete_favorit';
$route['check_favorit'] = 'api/User/check_favorit';

// Favorits
$route['get_config'] = 'api/User/get_config';

$route['get_current_plan_config'] = 'api/User/get_current_plan_config';

// Profile
$route['get_user_profile_data'] = 'api/User/get_user_profile_data';

$route['update_broker_profile'] = 'api/User/update_broker_profile';
$route['update_client_profile'] = 'api/User/update_client_profile';
// Profile

// schedule
$route['add_schedule'] = 'api/Schedule/add_schedule';
$route['get_broker_schedules'] = 'api/Schedule/get_broker_schedules';

$route['search_broker_schedules'] = 'api/Schedule/search_broker_schedules';
$route['update_schedule_broker_date'] = 'api/Schedule/update_schedule_broker_date';
$route['broker_cancel_schedule'] = 'api/Schedule/broker_cancel_schedule';
$route['search_broker_schedules'] = 'api/Schedule/search_broker_schedules';
$route['filter_broker_schedules'] = 'api/Schedule/filter_broker_schedules';



$route['filter_client_schedules'] = 'api/Schedule/filter_client_schedules';

$route['delete_client_schedule'] = 'api/Schedule/delete_client_schedule';
$route['delete_broker_schedule'] = 'api/Schedule/delete_broker_schedule';

// --------- client ----------

$route['get_client_schedules'] = 'api/Schedule/get_client_schedules';
$route['search_client_schedules'] = 'api/Schedule/search_client_schedules';
$route['update_schedule_client_date'] = 'api/Schedule/update_schedule_client_date';
$route['client_cancel_schedule'] = 'api/Schedule/client_cancel_schedule';



$route['get_restrict_schedule'] = 'api/Schedule/get_restrict_schedule';
$route['add_resctrict_schedule'] = 'api/Schedule/add_resctrict_schedule';
$route['delete_restrict_schedule'] = 'api/Schedule/delete_restrict_schedule';


// schedule


// Chats
$route['add_chat'] = 'api/Chat/add_chat';
$route['add_chat_partner'] = 'api/Chat/add_chat_partner';

$route['get_broker_chat'] = 'api/Chat/get_broker_chat';
$route['search_broker_chat'] = 'api/Chat/search_broker_chat';
$route['add_chat_message'] = 'api/Chat/add_chat_message';
$route['get_chat_messages'] = 'api/Chat/get_chat_messages';

// -------------------------
$route['get_client_chat'] = 'api/Chat/get_client_chat';
$route['search_client_chat'] = 'api/Chat/search_client_chat';


// Chats


// Cidades
$route['get_cidades_by_estado'] = 'api/User/get_cidades_by_estado';
$route['get_estados'] = 'api/User/get_estados';
$route['get_estados_client'] = 'api/User/get_estados_client';

// Cidades

// avaliacoes
$route['get_ratings'] = 'api/Rating/get_ratings';
$route['add_rating'] = 'api/Rating/add_rating';


$route['check_rating_partner'] = 'api/Rating/check_rating_partner';
$route['add_rating_partner'] = 'api/Rating/add_rating_partner';


// avaliacoes

// seguidores
$route['check_follow'] = 'api/Followers/check_follow';
$route['to_follow'] = 'api/Followers/to_follow';
$route['to_unfollow'] = 'api/Followers/to_unfollow';

$route['get_client_following'] = 'api/Followers/get_client_following';
$route['search_client_following'] = 'api/Followers/search_client_following';

$route['get_broker_followers'] = 'api/Followers/get_broker_followers';
$route['search_broker_followers'] = 'api/Followers/search_broker_followers';
// seguidores



// Client dashboard
$route['get_suggest_client_propertys'] = 'api/User/get_suggest_client_propertys';
$route['get_suggest_client_brokers'] = 'api/User/get_suggest_client_brokers';
$route['get_client_feed'] = 'api/User/get_client_feed';
// Cliente Dashboard


// Partner
$route['add_partner_portfolio'] = 'api/Partner/add_partner_portfolio';
$route['add_partner_property'] = 'api/Partner/add_partner_property';
$route['add_partner_portfolio'] = 'api/Partner/add_partner_portfolio';

$route['update_partner_status'] = 'api/Partner/update_partner_status';
$route['add_partner_propertys'] = 'api/Partner/add_partner_propertys';

$route['add_partner_property_portfolio'] = 'api/Partner/add_partner_property_portfolio';

$route['get_property'] = 'api/Propertys/get_property';

$route['add_partner_action'] = 'api/Partner/add_partner_action';
$route['add_partner_finish_action'] = 'api/Partner/add_partner_finish_action';

$route['accept_action'] = 'api/Partner/accept_action';
$route['reject_action'] = 'api/Partner/reject_action';
$route['contra_action'] = 'api/Partner/contra_action';
$route['restart_action'] = 'api/Partner/restart_action';

$route['finish_action'] = 'api/Partner/finish_action';

$route['add_reject_restart'] = 'api/Partner/add_reject_restart';
$route['add_accept_restart'] = 'api/Partner/add_accept_restart';

$route['get_partners_by_property'] = 'api/Partner/get_partners_by_property';
$route['get_partners_by_broker'] = 'api/Partner/get_partners_by_broker';

$route['check_exist_partner'] = 'api/Partner/check_exist_partner';



$route['get_partner'] = 'api/Partner/get_partner';
$route['get_partner_actions'] = 'api/Partner/get_partner_actions';
$route['get_partner_propertys'] = 'api/Partner/get_partner_propertys';

$route['get_partners'] = 'api/Partner/get_partners';
$route['get_partners_by_user'] = 'api/Partner/get_partners_by_user';


$route['web_get_propretys'] = 'api/Propertys/web_get_propretys';
$route['web_search_propretys'] = 'api/Propertys/web_search_propretys';



// Parnet

// Planos
$route['get_planos'] = 'api/Plans/get_planos';
// Planos

// Suporte
$route['get_support_categorias'] = 'api/Support/get_support_categorias';
$route['get_support_artigo'] = 'api/Support/get_support_artigo';
$route['get_support_categorias_search'] = 'api/Support/get_support_categorias_search';


// Suporte

// Termos
$route['get_terms'] = 'api/Terms/get_terms';
// Termos


$route['get_leads'] = 'api/User/get_leads';


// web ============

$route['web_process_property_main_image'] = 'api/Propertys/web_process_property_main_image';
$route['web_process_profile_image'] = 'api/Propertys/web_process_profile_image';
