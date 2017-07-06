<?php

mb_internal_encoding("utf-8");
error_reporting(E_ALL);

header("X-Powered-By: Dmitry Mitskus, https://vk.me/neonspaceman");

require_once "__config.php";
require_once "__help.php";
require_once "__autoload_class.php";

spl_autoload_register("__autoload_class");