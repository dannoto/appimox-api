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

$route['register'] = 'api/User/register';
$route['login'] = 'api/User/login';
$route['logout'] = 'api/User/logout';
$route['recovery'] = 'api/User/recovery';


$route['preferences'] = 'api/User/preferences';
$route['check_preferences_init'] = 'api/User/check_preferences_init';
$route['check_creci_init'] = 'api/User/check_creci_init';
$route['check_init_preferences'] = 'api/User/check_init_preferences';

// Propertys
$route['broker_propertys'] = 'api/Propertys/broker_propriety';
$route['search_broker_propertys'] = 'api/Propertys/search_broker_propertys';
$route['add_broker_property'] = 'api/Propertys/add_broker_property';
$route['update_broker_property'] = 'api/Propertys/update_broker_property';
$route['add_broker_property_others_images'] = 'api/Propertys/add_broker_property_others_images';
$route['get_broker_property_data'] = 'api/Propertys/get_broker_property_data';
$route['delete_broker_property'] = 'api/Propertys/delete_broker_property';
// Property

// Locations
$route['get_locations'] = 'api/Locations/get_locations';

// Locations


