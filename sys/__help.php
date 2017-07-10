<?php

/**
 * Склонение по числам
 * @param int $num
 * @param array $end
 * @return mixed
 */
function get_num_ending($num, $end)
{
  $ret = $end[2];
  $num %= 100;
  if (!($num >= 11 && $num <= 19)) {
    switch ($num % 10)
    {
    case 1:
      $ret = $end[0];
      break;
    case 2:
    case 3:
    case 4:
      $ret = $end[1];
      break;
    }
  }
  return $ret;
}

/**
 * Получить регионы
 * @return array
 */
function get_regions()
{
  static $ret = array();
  if (empty($ret))
  {
    $regions = array();
    $db = __database::get_instance();
    $q = "SELECT `id`, `name`, `parent` FROM `regions` ORDER BY `parent`";
    $res = $db->query($q) or die($db->error);
    while($row = $res->fetch_assoc())
    {
      if (is_null($row["parent"]))
        $regions[$row["id"]] = array($row["name"], array());
      else
        $regions[$row["parent"]][1][$row["id"]] = $row["name"];
    }
    $res->close();

    $ret = array();
    foreach($regions as $key_region => $region)
    {
      $arr = array("id" => $key_region, "caption" => $region[0], "sub" => []);
      foreach($region[1] as $key_sub => $sub)
        $arr["sub"][] = array("id" => $key_sub, "caption" => $sub);
      $ret[] = $arr;
    }
  }
  return $ret;
}

/**
 * Возвращает корректную сортировку
 * @param $orders
 * @return mixed
 */
function get_order($orders)
{
  $order = __data::get("order_by", "s");
  $test = false;
  foreach($orders as $item)
  {
    if ($order == $item[0])
    {
      $test = true;
      break;
    }
  }
  return $test ? $order : $orders[0][0];
}

/**
 * Возвращает коректный offset
 */
function get_offset($records_on_page, $count)
{
  $offset = __data::get("offset", "u");
  $count_pages = ceil($count / $records_on_page );
  if ($offset >= $count_pages)
    $offset = $count_pages - 1;
  return $offset;
}

/**
 * Вывод текста
 * @param $string
 * @return string
 */
function text($string)
{
  return htmlspecialchars($string);
}

/**
 * Возвращает массив с данными съедобный для элемента <select>
 * @param array $data
 * @param bool $extension
 * @return array
 */
function to_select($data, $extension = false)
{
  $first_item = array("id" => 0, "caption" => "Не выбрано");
  if ($extension)
    $first_item["sub"] = array();
  array_unshift($data, $first_item);
  return $data;
}