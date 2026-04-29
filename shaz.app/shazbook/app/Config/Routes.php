<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Auth (public)
$routes->get ('/',             'Auth::login');
$routes->get ('auth/login',    'Auth::login');
$routes->get ('auth/redirect', 'Auth::googleRedirect');
$routes->get ('auth/callback', 'Auth::callback');
$routes->get ('auth/logout',   'Auth::logout');

// Protected — require login
$routes->group('', ['filter' => 'auth'], function ($routes) {

   // Feed
   $routes->get ('feed',              'Feed::index');
   $routes->post('feed/create',       'Feed::create');
   $routes->post('feed/like/(:num)',   'Feed::like/$1');
   $routes->get ('feed/likers/(:num)', 'Feed::likers/$1');
   $routes->post('feed/delete/(:num)','Feed::delete/$1');

   // Comments
   $routes->post('comment/create/(:num)', 'Comment::create/$1');
   $routes->post('comment/delete/(:num)', 'Comment::delete/$1');

   // Profile
   $routes->get ('profile',           'Profile::index');
   $routes->get ('profile/(:num)',    'Profile::index/$1');
   $routes->get ('profile/edit',      'Profile::edit');
   $routes->post('profile/update',    'Profile::update');

   // Friends
   $routes->get ('friends',                 'Friends::index');
   $routes->post('friends/request/(:num)',  'Friends::request/$1');
   $routes->post('friends/accept/(:num)',   'Friends::accept/$1');
   $routes->post('friends/decline/(:num)',  'Friends::decline/$1');
   $routes->post('friends/unfriend/(:num)', 'Friends::unfriend/$1');
});
