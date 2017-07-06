<?php

require_once "../sys/__init.php";

/**
 * Загрузка изображения
 * @param $method - метод загрузки (file, base64)
 */
function image_upload($method)
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $hash = __data::get("hash", "s");

  list($path_full, $path_thumb) = __files::create_unique_path(array(UPLOAD_DIR . "{file_name}.jpg", UPLOAD_DIR . "{file_name}.jpg"));
  switch($method)
  {
  case "file":
    $buf = file_get_contents("php://input");
    file_put_contents(ROOT . $path_full, $buf);
    break;
  case "base64":
    $in = __data::post("image", "s");
    $in = str_replace(array("data:image/jpeg;base64,", " "), array("", "+"), $in);
    $buf = base64_decode($in);
    file_put_contents(ROOT . $path_full, $buf);
    break;
  }

  if (!file_exists(ROOT . $path_full))
  {
    $response->error("file doesn't create");
    return;
  }

  if(($info = getimagesize(ROOT . $path_full)) === false)
  {
    $response->error("file is not image");
    return;
  }

  $format = strtolower(substr($info['mime'], strpos($info['mime'], '/') + 1));
  $icfunc = "imagecreatefrom" . $format;
  if(!function_exists($icfunc))
  {
    $response->error("file is not image");
    return;
  }

  if ($info[0] > UPLOAD_IMG_MAX_WIDTH || $info[1] > UPLOAD_IMG_MAX_HEIGHT)
  {
    $response->error("image is large");
    return;
  }

  $info_full = __files::image_copy(ROOT . $path_full, ROOT . $path_full, MAX_FULLSIZE_WIDTH, MAX_FULLSIZE_HEIGHT, 75);
  $info_thumb = __files::image_copy(ROOT . $path_full, ROOT . $path_thumb, MAX_THUMB_WIDTH, MAX_THUMB_HEIGHT, 90);
  $q = "insert into `images` (`fullsize`, `fw`, `fh`, `thumb`, `tw`, `th`, `hash`) values (?, ?, ?, ?, ?, ?, ?)";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param(
    "siisiis",
    $path_full, $info_full["width"], $info_full["height"],
    $path_thumb, $info_thumb["width"], $info_thumb["height"],
    $hash
  );
  $stmt->execute() or die($db->error);
  if ($stmt->insert_id)
  {
    $response->set_value("id", $stmt->insert_id);
    $response->set_value("thumb", $path_thumb);
  }
  $stmt->close();
}

/**
 * Удаление изображения из базы
 */
function remove_image()
{
  $db = __database::get_instance();
  $id = __data::post("id", "u");

  $q = "select `fullsize`, `thumb` from `images` where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("i", $id);
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc())
  {
    unlink(ROOT . $row["fullsize"]);
    unlink(ROOT . $row["thumb"]);
  }
  $res->close();
  $stmt->close();

  $q = "delete from `images` where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("i", $id);
  $stmt->execute() or die($db->error);
  $stmt->close();
}

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act", "s"))
{
case "upload_image_file":
  image_upload('file');
  break;
case "upload_image_base64":
  image_upload('base64');
  break;
case "remove_image":
  remove_image();
  break;
}

$response->send();
$core->close();