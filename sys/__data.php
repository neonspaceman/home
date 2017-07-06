<?php

/**
 * Класс для работы с переменными
 */
class __data
{
  /**
   * Преобразование к числу
   * @param $num
   * @return int
   */
  public static function to_int($num)
  {
    return (int)$num;
  }
  /**
   * Преобразование к натуральному числу
   * @param $num
   * @return int
   */
  public static function to_uint($num)
  {
    $num = (int)$num;
    return $num < 0 ? 0 : $num;
  }
  /**
   * Преобразование к вещественному числу
   * @param $num
   * @return float
   */
  public static function to_float($num)
  {
    return (float)$num;
  }
  /**
   * Преобразование к строке
   * @param $str
   * @return string
   */
  public static function to_string($str)
  {
    return trim($str);
  }

  /**
   * Получение данных
   * @param $arr - массив
   * @param $name - имя
   * @param $modify - модификаторы i, u, f, s, i[], u[], f[], s[]
   * @return array|bool|float|int|string
   */
  public static function get_from_arr($arr, $name, $modify = false)
  {
    $var = isset($arr[$name]) ? $arr[$name] : false;
    switch ($modify)
    {
    case "i":
      $var = self::to_int($var);
      break;
    case "u":
      $var = self::to_uint($var);
      break;
    case "f":
      $var = self::to_float($var);
      break;
    case "s":
      $var = self::to_string($var);
      break;
    case "i[]":
      $tmp = $var;
      $var = array();
      if (is_array($tmp))
        foreach ($tmp as $value)
          $var[] = self::to_int($value);
      break;
    case "u[]":
      $tmp = $var;
      $var = array();
      if (is_array($tmp))
        foreach ($tmp as $value)
          $var[] = self::to_uint($value);
      break;
    case "f[]":
      $tmp = $var;
      $var = array();
      if (is_array($tmp))
        foreach ($tmp as $value)
          $var[] = self::to_float($value);
      break;
    case "s[]":
      $tmp = $var;
      $var = array();
      if (is_array($tmp))
        foreach ($tmp as $value)
          $var[] = self::to_string($value);
      break;
    case "mask":
      $tmp = $var;
      $var = 0;
      if (is_array($tmp))
      {
        foreach ($tmp as $value)
          $var |= self::to_uint($value);
      }
      break;
    case "date":
      $var = self::to_string($var);
      if (strtotime($var) <= 0)
        $var = date("Ymd", 0);
      break;
    }
    return $var;
  }
  /**
   * Получение из массива get
   * @param $name
   * @param $modify
   * @return mixed
   */
  public static function get($name, $modify = false)
  {
    return self::get_from_arr($_GET, $name, $modify);
  }

  /**
   * Получение из массива post
   * @param $name
   * @param $modify
   * @return mixed
   */
  public static function post($name, $modify = false)
  {
    return self::get_from_arr($_POST, $name, $modify);
  }

  /**
   * Получение из массива session
   * @param $name
   * @param $modify
   * @return mixed
   */
  public static function session($name, $modify = false)
  {
    return self::get_from_arr($_SESSION, $name, $modify);
  }

  /**
   * Получение из массива cookie
   * @param $name
   * @param $modify
   * @return mixed
   */
  public static function cookie($name, $modify = false)
  {
    return self::get_from_arr($_COOKIE, $name, $modify);
  }
}