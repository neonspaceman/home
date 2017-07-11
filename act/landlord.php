<?php

require_once "../sys/__init.php";

/**
 * Данные о арендодателе по номеру телефону
 */
function find_by_phone()
{
  $response = __response::get_instance();
  $db = __database::get_instance();

  $phone = __data::post("phone", "s");
  if (!preg_match("/^(\d{11}|\d{6})$/", $phone))
    $response->error("Phone is not correct");

  if ($response->is_success())
  {
    $landlord = array(
      "id" => 0,
      "name" => false,
      "phones" => array($phone)
    );
    $q = "select `landlords`.`id`, `landlords`.`name`, `phones`.`phone`
            from `landlords`
            inner join `phones`
                on `phones`.id_landlord = `landlords`.id
            where `landlords`.`id` =
                  (select `id_landlord` from `phones` where `phone` = ?)";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows)
      $landlord["phones"] = array();
    while($row = $res->fetch_assoc())
    {
      $landlord["id"] = $row["id"];
      $landlord["name"] = text($row["name"]);
      $landlord["phones"][] = $row["phone"];
    }
    $res->close();
    $stmt->close();
    $response->set_value("landlord", $landlord);
  }
}

/**
 * Данные о арендодателях
 */
function get_by_id()
{
  $response = __response::get_instance();
  $db = __database::get_instance();

  $object_id = __data::post("id", "u");
  $object_type = __data::post("type", "s");

  $landlord = array();
  $q = "select
            `landlords`.`id`, `landlords`.`name`, GROUP_CONCAT(`phones`.`phone`) `phone`
          from
            `landlords_objects`
            left join `landlords` on `landlords`.`id` = `landlords_objects`.`id_landlord`
            left join `phones` on `phones`.`id_landlord` = `landlords_objects`.`id_landlord`
          where
            `landlords_objects`.`id_object` = ? and `landlords_objects`.`type_object` = ?
          group by
            `landlords_objects`.`id_landlord`";
  $stmt = $db->prepare($q) or die($db->error);
  $stmt->bind_param("is", $object_id, $object_type);
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  while($row = $res->fetch_assoc())
  {
    $landlord[] = array(
      "id" => $row["id"],
      "name" => text($row["name"]),
      "phones" => explode(",", $row["phone"])
    );
  }
  $res->close();
  $stmt->close();

  $response->set_value("landlord", $landlord);
}

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act"))
{
case "find_by_phone":
  find_by_phone();
  break;
case "get_by_id":
  get_by_id();
  break;
}

$response->send();
$core->close();