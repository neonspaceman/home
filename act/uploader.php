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
// function remove_image()
// {
//   $db = __database::get_instance();
//
//   $object_id = __data::post("object", "u");
//   $object_hash = __data::post("hash", "s");
//   $photo_id = __data::post("photo", "u");
//
//   $q = "select `fullsize`, `thumb` from `images` where `id` = ? and (`id_object` = ? or `hash` = ?) limit 1";
//   $stmt = $db->prepare($q) or die($db->error);
//   $stmt->bind_param("iis", $photo_id, $object_id, $object_hash);
//   $stmt->execute() or die($db->error);
//   $res = $stmt->get_result();
//   if ($row = $res->fetch_assoc())
//   {
//     unlink(ROOT . $row["fullsize"]);
//     unlink(ROOT . $row["thumb"]);
//   }
//   $res->close();
//   $stmt->close();
//
//   $q = "delete from `images` where `id` = ? and (`id_object` = ? or `hash` = ?) limit 1";
//   $stmt = $db->prepare($q) or die($db->error);
//   $stmt->bind_param("iis", $photo_id, $object_id, $object_hash);
//   $stmt->execute() or die($db->error);
//   $stmt->close();
// }

function load_images_by_id()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $id_object = __data::post("id", "u");
  $type_object = __data::post("type", "s");
  $images = array();

  $q = "select `id`, `thumb` from `images` where `id_object` = ? and `type_object` = ?";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("is", $id_object, $type_object);
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  while($row = $res->fetch_assoc())
  {
    $images[] = array(
      "id" => $row["id"],
      "thumb" => $row["thumb"]
    );
  }
  $res->close();
  $stmt->close();

  $response->set_value("images", $images);
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
case "load_images_by_id":
  load_images_by_id();
  break;
// case "remove_image":
//   remove_image();
//   break;
}

$response->send();
$core->close();