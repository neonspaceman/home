<?php

/**
 * Функции для вывод объектов по фильтрам
 * Class __flat
 */
class __object
{
  /**
   * Таблица с которой работаем
   * @var
   */
  private static $database;

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
        $statement .= "(`regions`.`parent` in (" . implode(array_fill(0, count($data[0]), "?"), ",") . ") or `regions`.`id` in (" . implode(array_fill(0, count($data[0]), "?"), ",") . "))";
        $mask .= str_repeat("i", count($data[0]) * 2);
        $params = array_merge($params, $data[0], $data[0]);
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
    $q = "select count(*) `count` from " . self::$database . " left join `regions` on `regions`.`id` = " . self::$database . ".`id_region`";
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
              " . self::$database . ".`id`, `streets`.`name` `street_name`, " . self::$database . ".`house`, " . self::$database . ".`price`,
              " . self::$database . ".`lat`, " . self::$database . ".`lon`, " . self::$database . ".`count_rooms`, 
              " . (self::$database != "`homes`" ? self::$database . ".`floor`" : self::$database . ".`floors`") . ",
              " . self::$database . ".`square_general`, " . self::$database . ".`furniture`, " . self::$database . ".`multimedia`, 
              " . self::$database . ".`comfort`, " . self::$database . ".time_create
            from
              " . self::$database . "
              left join `streets` on `streets`.`id` = " . self::$database . ".`id_street`
              left join `regions` on `regions`.`id` = " . self::$database . ".`id_region`";

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

  /**
   * Иницализация
   * @param $database - база данных
   */
  public static function init($database)
  {
    self::$database = "`" . $database . "`";
  }
}