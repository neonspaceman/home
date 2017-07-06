<?php

/**
 * Функции для вывод объектов по фильтрам
 * Class __flat
 */
class __flats
{
  /**
   * Создание условий по фильтрам
   * @param $q
   * @param $mask
   * @param $params
   */
  private static function create_where_statement(&$q, &$mask, &$params)
  {
    $statement = "";
    $mask = "";
    $params = array();
    foreach(__filter::get_detect_filters() as $name => $data)
    {
      if ($statement)
        $statement .= " and ";
      switch(__filter::get_type_by_name($name))
      {
      case "Region":
        $statement .= "`regions`.`parent` in (" . implode(array_fill(0, count($data[0]), "?"), ",") . ")";
        $mask .= str_repeat("i", count($data[0]));
        $params = array_merge($params, $data[0]);
        break;
      case "Select":
        $statement .= "`" . $name . "` in (" . implode(array_fill(0, count($data[0]), "?"), ",") . ")";
        $mask .= str_repeat("i", count($data[0]));
        $params = array_merge($params, $data[0]);
        break;
      case "Counter":
        $statement .= "? <= `" . $name . "` and `" . $name . "` <= ?";
        $mask .= "ii";
        $params = array_merge($params, $data);
        break;
      }
    }
    if ($statement)
      $q .= " where " . $statement;
  }

  /**
   * Выполнение запроса
   * @param $q
   * @param $mask
   * @param $params
   * @return mysqli_stmt
   */
  private static function execute_statement($q, $mask, $params)
  {
    $db = __database::get_instance();
    $stmt = $db->prepare($q) or die($db->error);
    if (!empty($params))
      call_user_func_array(array($stmt, "bind_param"), array_merge(array($mask), array_map(function(&$item){ return $item; }, $params)));
    return $stmt;
  }

  /**
   * Выражение для кол-ва квартир
   * @return mysqli_stmt
   */
  public static function get_count_stmt()
  {
    $q = "select count(*) `count` from `flats` left join `regions` on `regions`.`id` = `flats`.`id_region`";
    self::create_where_statement($q, $mask, $params);
    return self::execute_statement($q, $mask, $params);
  }

  /**
   * Выражение для вывода квартир
   * @return mysqli_stmt
   */
  public static function get_stmt($order_by, $offset)
  {
    $q = "select
              `flats`.`id`, `streets`.`name` `street_name`, `flats`.`house`, `flats`.`price`,
              `flats`.`lat`, `flats`.`lon`, `flats`.`count_rooms`, `flats`.`floor`, `flats`.`square_general`,
              `flats`.`furniture`, `flats`.`multimedia`, `flats`.`comfort`, `flats`.time_create
            from
              `flats`
              left join `streets` on `streets`.`id` = `flats`.`id_street`
              left join `regions` on `regions`.`id` = `flats`.`id_region`";

    self::create_where_statement($q, $mask, $params);

    switch($order_by)
    {
    case "time":
      $q .= " order by `time_create` desc ";
      break;
    case "price":
      $q .= " order by `price` desc ";
      break;
    }

    $q .= " limit ?, ?";
    $mask .= "ii";
    $params[] = $offset;
    $params[] = RECORDS_ON_PAGE;

    return self::execute_statement($q, $mask, $params);
  }
}