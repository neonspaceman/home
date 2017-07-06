<?php

require_once "../sys/__init.php";

/**
 * Аредодатели
 * Class Landlord
 */
class Landlord
{
  private $query = "";

  /**
   * Генерация запроса из полченных данных
   * Landlord constructor.
   */
  function __construct()
  {
    foreach (__data::post("landlord", "s[]") as $command)
      $this->query .= $command;
  }

  /**
   * Проверка телефонов на дубликаты
   * @return bool
   */
  public function has_duplicates()
  {
    $db = __database::get_instance();

    $phones = array(null);
    preg_match_all("/\/phone\/(\d{11}|\d{6})/", $this->query, $matches);
    foreach($matches[1] as $phone)
      $phones[] = $phone;

    $count = 0;
    $placeholders = implode(array_fill(0, count($phones), "?"), ",");
    $mask = str_repeat("s", count($phones));
    $q = "select count(*) `count` from `phones` where `phone` in (" . $placeholders . ")";
    $stmt = $db->prepare($q) or die($db->error);
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($mask), array_map(function(&$item){ return $item; }, $phones)));
    $stmt->execute() or die($db->error);
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
  }

  /**
   * Добавление телефонов
   * @param $id_landlord - id арендодателя
   * @param $phones - строка с телефонами
   * @return bool - результат добавления номеров телефонов
   */
  public function exec_phones($id_landlord, $phones)
  {
    $success = true;
    $db = __database::get_instance();
    preg_match_all("/\/phone\/(\d{11}|\d{6})/", $phones, $phones);
    $q = "insert into `phones` (`id_landlord`, `phone`) values (?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    foreach($phones[1] as $phone)
    {
      $stmt->bind_param("is", $id_landlord, $phone);
      $stmt->execute() or die($db->error);
      if ($stmt->affected_rows < 1)
        $success = false;
    }
    $stmt->close();
    return $success;
  }

  /**
   * Обработка арендодателей
   * @return array|bool
   */
  public function exec()
  {
    $db = __database::get_instance();
    $response = __response::get_instance();

    if (empty($this->query))
    {
      $response->error("landlord is empty");
      return false;
    }

    if (!preg_match_all("/(?:\/new\/.*?(?:\/phone\/(?:\d{11}|\d{6}))+|\/id\/\d+(?:\/phone\/(?:\d{11}|\d{6}))*)/", $this->query, $commands))
    {
      $response->error("landlord has incorrect syntax");
      return false;
    }

    if ($this->has_duplicates())
    {
      $response->error("landlord's phones has duplicates");
      return false;
    }

    $landlords = array();
    $errors = array(
      "empty_name" => false,
      "not_create" => false,
      "incorrect_id" => false,
      "not_add_phones" => false
    );
    foreach($commands[0] as $command)
    {
      // прикрепление старого арендодателя
      if (preg_match("/^\/id\/([0-9]+)((?:\/phone\/(?:\d{11}|\d{6}))*)$/", $command, $matches))
      {
        $id = $matches[1];
        $count = 0;
        $q = "select count(*) `count` from `landlords` where `id` = ? limit 1";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("i", $id);
        $stmt->execute() or die($db->error);
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if (!$count)
        {
          $errors["incorrect_id"] = true;
          continue;
        }
        $phones = $matches[2];
        if (!$this->exec_phones($id, $phones))
        {
          $errors["not_add_phones"] = true;
          continue;
        }
        $landlords[] = $id;
      }
      // добаление нового арендодателя
      if (preg_match("/^\/new\/(.*?)((?:\/phone\/(?:\d{11}|\d{6}))+)$/", $command, $matches))
      {
        $name = $matches[1];
        if (empty($name))
        {
          $errors["empty_name"] = true;
          continue;
        }
        $q = "insert into `landlords` (`name`, `description`, `emails`) values (?, '', '')";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("s", $name);
        $stmt->execute() or die($db->error);
        $id = $stmt->insert_id;
        $stmt->close();
        if (!$id)
        {
          $errors["not_create"] = true;
          continue;
        }
        $phones = $matches[2];
        if (!$this->exec_phones($id, $phones))
        {
          $errors["not_add_phones"] = true;
          continue;
        }
        $landlords[] = $id;
      }
    }

    if ($errors["incorrect_id"])
    {
      $response->error("landlord's id is incorrect");
      return false;
    }
    if ($errors["empty_name"])
    {
      $response->error("landlord's name is empty");
      return false;
    }
    if ($errors["not_create"])
    {
      $response->error("landlord doesn't create");
      return false;
    }
    if ($errors["not_add_phones"])
    {
      $response->error("some of phones don't create");
      return false;
    }

    return $landlords;
  }
};

/**
 * Добавление квартир
 */
function flat_add()
{
  $db = __database::get_instance();
  $response = __response::get_instance();
  $db->autocommit(false);

  $source = __data::post("source", "u");
  $exclusive = __data::post("exclusive", "u");
  $quickly = __data::post("quickly", "u");

  $landlord = new Landlord();
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
  $type_object = "flat";
  if ($response->is_success())
  {
    $q = "insert into `flats`
            (`source`, `exclusive`, `quickly`, `id_region`, `id_street`, `house`, `flat`, `guide`, `lon`, `lat`, `count_rooms`, `related_rooms`, 
            `count_sleeps`, `floor`, `floors`, `square_general`, `square_living`, `square_kitchen`, `state`, `heating`, `hot_water`, 
            `wc`, `window`, `furniture`, `count_balcony`, `type_balcony`, `multimedia`, `comfort`, `additionally`, `date_rent`, `prepayment`, 
            `for_whom`, `description`, `date_price`, `price`, `guaranty`, `price_additionally`, `service_mark`, `time_create`, `visibility`) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param(
      "iiiiisssddiiiiidddiiiiiiiiiiiiiisiiiisii",
      $source, $exclusive, $quickly, $id_region, $id_street, $house, $flat, $guide, $lon, $lat, $count_rooms, $related_rooms,
      $count_sleeps, $floor, $floors, $square_general, $square_living, $square_kitchen, $state, $heating, $hot_water,
      $wc, $window, $furniture, $count_balcony, $type_balcony, $multimedia, $comfort, $additionally, $date_rent, $prepayment,
      $for_whom, $description, $date_price, $price, $guaranty, $price_additionally, $service_mark, $time_create, $visibility
    );
    $stmt->execute() or die($db->error);
    $id_object = $stmt->insert_id;
    $stmt->close();
    if (!$id_object)
      $response->error("flat doesn't create");
  }

  // связывание клиентов с объектом
  if ($response->is_success())
  {
    $q = "insert into `landlords_flats` (`id_landlord`, `id_flat`) values (?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    foreach ($ids_landlord as $id)
    {
      $stmt->bind_param("ii", $id, $id_object);
      $stmt->execute() or die($db->error);
      if ($stmt->affected_rows < 1)
        $response->error("landlord doesn't bind with flat");
    }
    $stmt->close();
  }

  // связываем изображения
  if ($response->is_success())
  {
    $hash = __data::post("hash", "s");
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

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act"))
{
case "flat_add":
  flat_add();
  break;
}

$response->send();
$core->close();