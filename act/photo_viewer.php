<?php

require_once "../sys/__init.php";

$core = __core::get_instance();
$db = __database::get_instance();
$core->open();

$response = __response::get_instance();

$id_photo = __data::post("id_photo", "u");
$id_object = 0;
$hash_object = 0;
$q = "select `id_object`, `hash` from `images` where `id` = ? limit 1";
$stmt = $db->prepare($q) or die($db->error);
$stmt->bind_param("i", $id_photo);
$stmt->execute() or die($db->error);
$stmt->bind_result($id_object, $hash_object);
$stmt->fetch();
$stmt->close();
if (is_null($id_object))
  $id_object = 0;
if (is_null($hash_object))
  $hash_object = 0;

if ($id_object || $hash_object)
{
  $offset = 0;
  $photos = array();

  $q = "select `id`, `fullsize`, `fw`, `fh` from `images` where `id_object` = ? or `hash` = ?";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("is", $id_object, $hash_object);
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  $i = 0;
  while($row = $res->fetch_assoc())
  {
    $photos[] = array(
      "source" => $row["fullsize"],
      "width" => $row["fw"],
      "height" => $row["fh"]
    );
    if ($row["id"] == $id_photo)
      $offset = $i;
    $i++;
  }
  $res->close();
  $stmt->close();

  $response->set_value("photos", $photos);
  $response->set_value("offset", $offset);
}
else
{
  $response->error("doesn't find photo");
}

$response->send();
$core->close();