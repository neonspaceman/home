<?php

require_once "sys/__init.php";

$core = __core::get_instance();
$core->open();

$user = __user::get_instance();
$user->exec();

$route = __route::get_instance();
$route->insert_rule(array("regexp" => "", "page" => "flat", "method" => "view_list"));
$route->insert_rule(array("regexp" => "flat\/view", "page" => "flat", "method" => "view"));
$route->insert_rule(array("regexp" => "users\/login", "page" => "user", "method" => "login_popup"));
/* admin */
$route->insert_rule(array("regexp" => "flat\/add", "page" => "flat", "method" => "add"));
$route->insert_rule(array("regexp" => "flat\/edit", "page" => "flat", "method" => "edit"));
$route->insert_rule(array("regexp" => "users", "page" => "user", "method" => "view_list"));
$route->insert_rule(array("regexp" => "users\/add", "page" => "user", "method" => "add_popup"));
$route->insert_rule(array("regexp" => "users\/edit", "page" => "user", "method" => "edit_popup"));
$route->exec();

$page = __page::get_instance();
$page->render();

$core->close();