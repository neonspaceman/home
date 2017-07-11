<?php

/**
 * Работа с фильтрами
 * Class __filter
 */
class __filter
{
  private static $database;

  /**
   * Пришедшие фильтры
   * @var array
   */
  private static $detect = array();

  /**
   * Установленные фильтры
   * @var array
   */
  private static $filters = array();

  /**
   * Ограничения
   * @var array
   */
  private static $limits = array();

  public static function init($database)
  {
    self::$database = "`" . $database . "`";
  }

  /**
   * Добавление нового фильтра
   * @param $name
   * @param $type
   * @param $data
   */
  public static function add($name, $type, $data = null)
  {
    self::$filters[$name] = array("type" => $type, "data" => $data);
  }

  /**
   * Парсинг и выбор лимитов
   */
  public static function exec()
  {
    $db = __database::get_instance();

    $q = "select 
        max(`floor`) `max_floor`, min(`floor`) `min_floor`, 
        max(`price`) `max_price`, min(`price`) `min_price`,
        max(`square_general`) `max_square_general`, min(`square_general`) `min_square_general`
      from " .
        self::$database;
    $res = $db->query($q) or die($db->error);
    $row = $res->fetch_assoc();
    foreach($row as $key => $value)
    {
      if (preg_match("/^(min|max)_(.+)$/", $key, $matches))
      {
        $type = $matches[1];
        $name = $matches[2];
        switch($type)
        {
        case "min":
          self::$limits[ $name ][ $type ] = floor($value);
          break;
        case "max":
          self::$limits[ $name ][ $type ] = ceil($value);
        }
      }
    }
    $res->close();

    $query = explode(";", __data::get("filter"));
    foreach($query as $item)
    {
      $item = explode(":", $item);
      if (count($item) != 2)
        continue;

      $var = array("name" => $item[0], "value" => $item[1]);

      if (!isset(self::$filters[ $var["name"] ]))
        continue;

      $type = self::$filters[ $var["name"] ]["type"];
      switch($type)
      {
      case "Region":
      case "Select":
        $var["value"] = explode(",", $var["value"]);
        $var["value"] = __data::get_from_arr($var, "value", "i[]");
        $indexes = __ui::get_indexes(self::$filters[ $var["name"] ]["data"], $var["value"]);
        if (!empty($indexes))
          self::$detect[ $var["name"] ] = array($var["value"], $indexes);
        break;
      case "Counter":
        $var["value"] = explode("-", $var["value"]);
        $var["value"] = __data::get_from_arr($var, "value", "i[]");
        if (count($var["value"]) != 2)
          continue;
        if ($var["value"][0] > $var["value"][1])
          continue;
        self::$detect[ $var["name"] ] = $var["value"];
        break;
      }
    }
  }

  /**
   * Возращает все полученные от пользователя фильтры
   * @return array
   */
  public static function get_detect_filters()
  {
    return self::$detect;
  }

  /**
   * Получение типа фильтра
   * @param $name
   * @return string|bool
   */
  public static function get_type_by_name($name)
  {
    return isset(self::$filters[ $name ]) ? self::$filters[ $name ]["type"] : false;
  }

  /**
   * Получение данных о фильтре по именеи
   * @param $name
   * @return bool|mixed
   */
  public static function get_value_by_name($name)
  {
    $ret = false;
    $value = isset(self::$detect[ $name ]) ? self::$detect[ $name ] : false;
    if ($value !== false)
    {
      switch(self::$filters[ $name ]["type"])
      {
      case "Region":
      case "Select":
        $ret = $value[1];
        break;
      case "Counter":
        $ret = $value;
        break;
      }
    }
    return $ret;
  }

  /**
   * Ограничения
   * @param $name
   * @return array|bool
   */
  public static function get_limit_by_name($name)
  {
    $ret = false;
    $value = isset(self::$limits[ $name ]) ? self::$limits[ $name ] : false;
    if ($value !== false)
    {
      switch(self::$filters[ $name ]["type"])
      {
      case "Counter":
        $ret = array($value["min"], $value["max"]);
        break;
      }
    }
    return $ret;
  }
}