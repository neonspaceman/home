<?php

require_once "sys/__init.php";

$core = __core::get_instance();
$core->open();

$user = __user::get_instance();
$user->exec();

$route = __route::get_instance();
$route->insert_rule(array("regexp" => "flats", "page" => "flat", "method" => "view_list"));
$route->insert_rule(array("regexp" => "flat", "page" => "flat", "method" => "view"));
$route->insert_rule(array("regexp" => "rooms", "page" => "room", "method" => "view_list"));
$route->insert_rule(array("regexp" => "room", "page" => "room", "method" => "view"));
$route->insert_rule(array("regexp" => "homes", "page" => "home", "method" => "view_list"));
$route->insert_rule(array("regexp" => "home", "page" => "home", "method" => "view"));
$route->insert_rule(array("regexp" => "users\/login", "page" => "user", "method" => "login_popup"));
/* admin */
$route->insert_rule(array("regexp" => "flat\/add", "page" => "flat", "method" => "add"));
$route->insert_rule(array("regexp" => "flat\/edit", "page" => "flat", "method" => "edit"));
$route->insert_rule(array("regexp" => "room\/add", "page" => "room", "method" => "add"));
$route->insert_rule(array("regexp" => "home\/add", "page" => "home", "method" => "add"));
$route->insert_rule(array("regexp" => "users", "page" => "user", "method" => "view_list"));
$route->insert_rule(array("regexp" => "users\/add", "page" => "user", "method" => "add_popup"));
$route->insert_rule(array("regexp" => "users\/edit", "page" => "user", "method" => "edit_popup"));
$route->exec();

$page = __page::get_instance();
$page->render();

$core->close();