<?php

require_once "../sys/__init.php";

/**
 * Добавление квартир
 */
function room_add()
{
  $db = __database::get_instance();
  $response = __response::get_instance();
  $db->autocommit(false);

  $hash = __data::post("hash", "s");

  $source = __data::post("source", "u");
  $exclusive = __data::post("exclusive", "u");
  $quickly = __data::post("quickly", "u");

  $landlord = new __landlord();
  $ids_landlord = $landlord->exec();

  $id_region = __data::post("region", "u");
  if (empty($id_region))
  {
    $response->error("region is empty");
  }
  else
  {
    $id_subregion = __data::post("subregion", "i");
    if ($id_subregion != -1)
      $id_region = $id_subregion;
    if (!empty($id_region))
    {
      $count = 0;
      $q = "select count(*) `count` from `regions` where `id` = ? limit 1";
      $stmt = $db->prepare($q) or die($db->error);
      $stmt->bind_param("i", $id_region);
      $stmt->execute() or die($db->error);
      $stmt->bind_result($count);
      $stmt->fetch();
      $stmt->close();
      if (!$count)
        $response->error("region not found");
    }
  }

  $id_street = __data::post("street", "u");
  if (empty($id_street))
  {
    $response->error("street is empty");
  }
  else
  {
    $count = 0;
    $q = "select count(*) `count` from `streets` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $id_street);
    $stmt->execute() or die($db->error);
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if (!$count)
      $response->error("street not found");
  }
  $house = __data::post("house", "s");
  if (empty($house))
    $response->error("house is empty");
  $flat = __data::post("flat", "s");
  $guide = __data::post("guide", "s");
  $lat = __data::post("lat", "f");
  $lon = __data::post("lon", "f");

  $count_rooms = __data::post("count_rooms", "u");
  $related_rooms = $count_rooms > 2 ? __data::post("related_room", "u") : 0;
  $type_of_room = __data::post("type_of_room", "u");
  if (!$type_of_room)
    $response->error("type of room doesn't select");
  $square_general = __data::post("square_general", "f");
  $square_living = __data::post("square_living", "f");
  $square_kitchen = __data::post("square_kitchen", "f");
  $furniture = __data::post("furniture", "mask");
  $floor = __data::post("floor", "u");
  $floors = __data::post("floors", "u");
  $count_sleeps = __data::post("count_sleeps", "u");
  $state = __data::post("state", "u");
  $multimedia = __data::post("multimedia", "mask");
  $comfort = __data::post("comfort", "mask");
  $additionally = __data::post("additionally", "mask");
  $wc = __data::post("wc", "u");
  $heating = __data::post("heating", "u");
  $hot_water = __data::post("hot_water", "u");
  $window = __data::post("window", "u");
  $count_balcony = __data::post("count_balcony", "u");
  $type_balcony = $count_balcony > 1 ? __data::post("type_balcony", "u") : 0;
  $description = __data::post("description", "s");
  $service_mark = __data::post("service_mark", "s");
  $date_price = __data::post("date_price", "date");
  $date_rent = __data::post("date_rent", "date");
  $price = __data::post("price", "u");
  if (empty($price))
    $response->error("price is empty");
  $guaranty = __data::post("guaranty", "u");
  $prepayment = __data::post("prepayment", "u");
  $price_additionally = __data::post("price_additionally", "mask");
  $for_whom = __data::post("for_whom", "mask");
  $time_create = time();
  $visibility = 1;

  // добавление квартиры
  $id_object = 0;
  $type_object = "room";
  if ($response->is_success())
  {
    $q = "insert into `rooms`
            (`source`, `exclusive`, `quickly`, `id_region`, `id_street`, `house`, `flat`, `guide`, `lon`, `lat`, `count_rooms`, `related_rooms`, 
            `count_sleeps`, `floor`, `floors`, `square_general`, `square_living`, `square_kitchen`, `state`, `heating`, `hot_water`, 
            `wc`, `window`, `furniture`, `count_balcony`, `type_balcony`, `multimedia`, `comfort`, `additionally`, `date_rent`, `prepayment`, 
            `for_whom`, `description`, `date_price`, `price`, `guaranty`, `price_additionally`, `service_mark`, `time_create`, `visibility`,
            `type_of_room`) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param(
      "iiiiisssddiiiiidddiiiiiiiiiiiiiisiiiisiii",
      $source, $exclusive, $quickly, $id_region, $id_street, $house, $flat, $guide, $lon, $lat, $count_rooms, $related_rooms,
      $count_sleeps, $floor, $floors, $square_general, $square_living, $square_kitchen, $state, $heating, $hot_water,
      $wc, $window, $furniture, $count_balcony, $type_balcony, $multimedia, $comfort, $additionally, $date_rent, $prepayment,
      $for_whom, $description, $date_price, $price, $guaranty, $price_additionally, $service_mark, $time_create, $visibility,
      $type_of_room
    );
    $stmt->execute() or die($db->error);
    $id_object = $stmt->insert_id;
    $stmt->close();
    if (!$id_object)
      $response->error("room doesn't create");
  }

  // связывание клиентов с объектом
  if ($response->is_success())
  {
    $q = "insert into `landlords_objects` (`id_landlord`, `id_object`, `type_object`) values (?, ?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    foreach ($ids_landlord as $id)
    {
      $stmt->bind_param("iis", $id, $id_object, $type_object);
      $stmt->execute() or die($db->error);
      if ($stmt->affected_rows < 1)
        $response->error("landlord doesn't bind with object");
    }
    $stmt->close();
  }

  // связываем изображения
  if ($response->is_success())
  {
    $ids_image = array(null);
    foreach(__data::post("uploader", "u[]") as $image)
      $ids_image[] = $image;

    $mask = array("is" . str_repeat("s", count($ids_image)) . "s");
    $placeholders = implode(array_fill(0, count($ids_image), "?"), ",");
    $q = "update `images` set `id_object` = ?, `type_object` = ?, `hash` = null where `id` in (" . $placeholders . ") and `hash` = ?";
    $stmt = $db->prepare($q) or die($db->error);
    call_user_func_array(
      array($stmt, "bind_param"),
      array_merge($mask, array(&$id_object, &$type_object), array_map(function(&$item){ return $item; }, $ids_image), array(&$hash))
    );
    $stmt->execute() or die($db->error);
    $stmt->close();
  }


  if ($response->is_success())
  {
    $db->commit();
    $response->set_value("id", $id_object);
  }
  else
  {
    $db->rollback();
  }
}

/**
 * Добавление квартир
 */
function room_edit()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $id_object = __data::post("id", "u");
  $type_object = "room";
  $hash = __data::post("hash", "s");

  $object_exists = 0;
  $q = "select count(*) `count` from `rooms` where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("i", $id_object);
  $stmt->execute() or die($db->error);
  $stmt->bind_result($object_exists);
  $stmt->fetch();
  $stmt->close();
  if (!$object_exists)
  {
    $response->error("room doesn't exist");
    return;
  }

  $db->autocommit(false);

  // развязываем изображения и арендодателя
  $q = "update `images` set `id_object` = null, `type_object` = '', `hash` = ? where `id_object` = ?";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("si", $hash, $id_object);
  $stmt->execute() or die($db->error);
  $stmt->close();
  $q = "delete from `landlords_objects` where `id_object` = ? and `type_object` = ?";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("is", $id_object, $type_object);
  $stmt->execute() or die($db->error);
  $stmt->close();

  $landlord = new __landlord();
  $ids_landlord = $landlord->exec();
  $source = __data::post("source", "u");
  $exclusive = __data::post("exclusive", "u");
  $quickly = __data::post("quickly", "u");
  $id_region = __data::post("region", "u");
  if (empty($id_region))
  {
    $response->error("region is empty");
  }
  else
  {
    $id_subregion = __data::post("subregion", "i");
    if ($id_subregion != -1)
      $id_region = $id_subregion;
    if (!empty($id_region))
    {
      $count = 0;
      $q = "select count(*) `count` from `regions` where `id` = ? limit 1";
      $stmt = $db->prepare($q) or die($db->error);
      $stmt->bind_param("i", $id_region);
      $stmt->execute() or die($db->error);
      $stmt->bind_result($count);
      $stmt->fetch();
      $stmt->close();
      if (!$count)
        $response->error("region not found");
    }
  }
  $id_street = __data::post("street", "u");
  if (empty($id_street))
  {
    $response->error("street is empty");
  }
  else
  {
    $count = 0;
    $q = "select count(*) `count` from `streets` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $id_street);
    $stmt->execute() or die($db->error);
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if (!$count)
      $response->error("street not found");
  }
  $house = __data::post("house", "s");
  if (empty($house))
    $response->error("house is empty");
  $flat = __data::post("flat", "s");
  $guide = __data::post("guide", "s");
  $lat = __data::post("lat", "f");
  $lon = __data::post("lon", "f");

  $count_rooms = __data::post("count_rooms", "u");
  $related_rooms = $count_rooms > 2 ? __data::post("related_room", "u") : 0;
  $type_of_room = __data::post("type_of_room", "u");
  if (!$type_of_room)
    $response->error("type of room doesn't select");
  $square_general = __data::post("square_general", "f");
  $square_living = __data::post("square_living", "f");
  $square_kitchen = __data::post("square_kitchen", "f");
  $furniture = __data::post("furniture", "mask");
  $floor = __data::post("floor", "u");
  $floors = __data::post("floors", "u");
  $count_sleeps = __data::post("count_sleeps", "u");
  $state = __data::post("state", "u");
  $multimedia = __data::post("multimedia", "mask");
  $comfort = __data::post("comfort", "mask");
  $additionally = __data::post("additionally", "mask");
  $wc = __data::post("wc", "u");
  $heating = __data::post("heating", "u");
  $hot_water = __data::post("hot_water", "u");
  $window = __data::post("window", "u");
  $count_balcony = __data::post("count_balcony", "u");
  $type_balcony = $count_balcony > 1 ? __data::post("type_balcony", "u") : 0;
  $description = __data::post("description", "s");
  $service_mark = __data::post("service_mark", "s");
  $date_price = __data::post("date_price", "date");
  $date_rent = __data::post("date_rent", "date");
  $price = __data::post("price", "u");
  if (empty($price))
    $response->error("price is empty");
  $guaranty = __data::post("guaranty", "u");
  $prepayment = __data::post("prepayment", "u");
  $price_additionally = __data::post("price_additionally", "mask");
  $for_whom = __data::post("for_whom", "mask");

  // сохранение изменений
  $type_object = "room";
  if ($response->is_success())
  {
    $q = "update 
            `rooms` 
          set
            `source` = ?, `exclusive` = ?, `quickly` = ?, `id_region` = ?, `id_street` = ?, `house` = ?, `flat` = ?, 
            `guide` = ?, `lon` = ?, `lat` = ?, `count_rooms` = ?, `related_rooms` = ?, `count_sleeps` = ?, 
            `floor` = ?, `floors` = ?, `square_general` = ?, `square_living` = ?, `square_kitchen` = ?, `state` = ?, 
            `heating` = ?, `hot_water` = ?, `wc` = ?, `window` = ?, `furniture` = ?, `count_balcony` = ?, 
            `type_balcony` = ?, `multimedia` = ?, `comfort` = ?, `additionally` = ?, `date_rent` = ?, `prepayment` = ?, 
            `for_whom` = ?, `description` = ?, `date_price` = ?, `price` = ?, `guaranty` = ?, `price_additionally` = ?, 
            `service_mark` = ?, `type_of_room` = ? 
          where
            `id` = ?
          limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param(
      "iiiiisssddiiiiidddiiiiiiiiiiiiiisiiiisi",
      $source, $exclusive, $quickly, $id_region, $id_street, $house, $flat, $guide, $lon, $lat, $count_rooms, $related_rooms,
      $count_sleeps, $floor, $floors, $square_general, $square_living, $square_kitchen, $state, $heating, $hot_water,
      $wc, $window, $furniture, $count_balcony, $type_balcony, $multimedia, $comfort, $additionally, $date_rent, $prepayment,
      $for_whom, $description, $date_price, $price, $guaranty, $price_additionally, $service_mark, $type_of_room, $id_object
    );
    $stmt->execute() or die($db->error);
    $stmt->close();
  }

  // связывание клиентов с объектом
  if ($response->is_success())
  {
    $q = "insert into `landlords_objects` (`id_landlord`, `id_object`, `type_object`) values (?, ?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    foreach ($ids_landlord as $id)
    {
      $stmt->bind_param("iis", $id, $id_object, $type_object);
      $stmt->execute() or die($db->error);
      if ($stmt->affected_rows < 1)
        $response->error("landlord doesn't bind with object");
    }
    $stmt->close();
  }

  // связываем изображения
  if ($response->is_success())
  {
    $ids_image = array(null);
    foreach(__data::post("uploader", "u[]") as $image)
      $ids_image[] = $image;

    $mask = array("is" . str_repeat("s", count($ids_image)) . "s");
    $placeholders = implode(array_fill(0, count($ids_image), "?"), ",");
    $q = "update `images` set `id_object` = ?, `type_object` = ?, `hash` = null where `id` in (" . $placeholders . ") and `hash` = ?";
    $stmt = $db->prepare($q) or die($db->error);
    call_user_func_array(
      array($stmt, "bind_param"),
      array_merge($mask, array(&$id_object, &$type_object), array_map(function(&$item){ return $item; }, $ids_image), array(&$hash))
    );
    $stmt->execute() or die($db->error);
    $stmt->close();
  }


  if ($response->is_success())
  {
    $db->commit();
    $response->set_value("id", $id_object);
  }
  else
  {
    $db->rollback();
  }
}

function room_toggle_archive()
{
  $db = __database::get_instance();
  $response = __response::get_instance();

  $object_id = __data::post("id", "u");

  $q = "select `visibility` from `rooms` where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("i", $object_id);
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $res->close();
  $stmt->close();

  if (!$row)
  {
    $response->error("room doesn't find");
    return;
  }

  $visibility = !$row["visibility"];
  $q = "update `rooms` set `visibility` = ? where `id` = ? limit 1";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("ii", $visibility, $object_id);
  $stmt->execute() or die($db->error);
  if ($stmt->affected_rows < 1)
  {
    $response->error("room's visibility doesn't change");
    return;
  }

  $response->set_value("visibility", $visibility);
}

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act"))
{
case "room_add":
  room_add();
  break;
case "room_edit":
  room_edit();
  break;
case "room_toggle_archive":
  room_toggle_archive();
  break;
}

$response->send();
$core->close();