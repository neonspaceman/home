<?php

require_once "../sys/__init.php";

$core = __core::get_instance();
$db = __database::get_instance();
$core->open();

$response = __response::get_instance();

$object_id = __data::post("object", "u");
$object_hash = __data::post("hash", "s");
$photo_id = __data::post("id", "u");

$offset = 0;
$photos = array();

$q = "select `id`, `fullsize`, `fw`, `fh` from `images` where `id_object` = ? or `hash` = ?";
$stmt = $db->prepare($q) or die($db->error);
$stmt->bind_param("is", $object_id, $object_hash);
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
  if ($row["id"] == $photo_id)
    $offset = $i;
  $i++;
}
$res->close();
$stmt->close();

$response->set_value("photos", $photos);
$response->set_value("offset", $offset);

$response->send();
$core->close();