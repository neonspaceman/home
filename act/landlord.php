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
      $landlord["name"] = $row["name"];
      $landlord["phones"][] = $row["phone"];
    }
    $res->close();
    $stmt->close();
    $response->set_value("landlord", $landlord);
  }
}

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act"))
{
case "find_by_phone":
  find_by_phone();
  break;
}

$response->send();
$core->close();