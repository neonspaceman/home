<?php

require_once "../sys/__init.php";

define("CODE_SIZE", 6);

/**
 * Генерация соли
 * @param int $n - длина соли
 * @return string
 */
function gen_salt($n)
{
  $key = "";
  $pattern = "123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $counter = mb_strlen($pattern) - 1;
  for($i = 0; $i < $n; ++$i)
    $key .= $pattern{rand(0, $counter)};
  return $key;
}

/**
 * Генерация кода (генерация через id, чтобы исключит коллизий в кодах)
 * @param $id
 * @return string
 */
function gen_code($id)
{
  return $id . "-" . gen_salt(CODE_SIZE);
}

/**
 * Добавление клиента
 */
function add()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $name = __data::post("name", "s");
  if (empty($name))
  {
    $response->error("name is empty");
    return;
  }

  $db->autocommit(false);

  $code = "0000";
  $now = time();
  $q = "insert into `users` (`name`, `time_create`, `code`) values (?, ?, ?)";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("sis", $name, $now, $code);
  $stmt->execute() or die($db->error);
  $id = $stmt->insert_id;
  if (!$id)
  {
    $response->error("user doesn't add");
    $db->rollback();
    return;
  }
  $stmt->close();

  $code = gen_code($id);
  $q = "update `users` set `code` = ? where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("si", $code, $id);
  $stmt->execute() or die($db->error);
  if ($stmt->affected_rows < 1)
  {
    $response->error("code doesn't create");
    $db->rollback();
    return;
  }
  $stmt->close();

  $db->commit();
}

/**
 * Редактирование клиента
 */
function edit()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $id = __data::post("id", "u");
  $name = __data::post("name", "s");
  if (empty($name))
  {
    $response->error("name is empty");
    return;
  }

  $q = "update `users` set `name` = ? where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("si", $name, $id);
  $stmt->execute() or die($db->error);
  $stmt->close();
}

/**
 * Удалить клиента
 */
function remove()
{
  $db = __database::get_instance();

  $id = __data::post("id", "u");

  $q = "delete from `users` where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("i", $id);
  $stmt->execute() or die($db->error);
  $stmt->close();
}

/**
 * Сгенерировать новый код
 */
function new_code()
{
  $db = __database::get_instance();

  $id = __data::post("id", "u");

  $code = gen_code($id);
  $q = "update `users` set `code` = ?, hash = '' where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("si", $code, $id);
  $stmt->execute() or die($db->error);
  $stmt->close();
}

/**
 * Авторизация
 */
function login()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $code = __data::post("code", "s");
  if (empty($code))
  {
    $response->error("code is empty");
    return;
  }

  $id = 0;
  $q = "select `id` from `users` where `code` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("s", $code);
  $stmt->execute() or die($db->error);
  $stmt->bind_result($id);
  $stmt->fetch();
  $stmt->close();
  if (!$id)
  {
    $response->error("incorrect code");
    return;
  }

  $cookie_hash = md5(gen_salt(10) . time());
  $q = "update `users` set `hash` = ? where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("si", $cookie_hash, $id);
  $stmt->execute() or die($db->error);
  $stmt->close();

  setcookie("id", $id, 0, "/");
  setcookie("hash", $cookie_hash, 0, "/");
}

/**
 * Выход из системы
 */
function logout()
{
  header("Location: /");
  setcookie("id", "", time() - 3600, "/");
  setcookie("hash", "", time() - 3600, "/");
  exit;
}

$core = __core::get_instance();
$core->open();

switch(__data::get("act"))
{
case "add":
  add();
  break;
case "edit":
  edit();
  break;
case "remove":
  remove();
  break;
case "new_code":
  new_code();
  break;
case "login":
  login();
  break;
case "logout":
  logout();
  break;
}

$response = __response::get_instance();
$response->send();
$core->close();