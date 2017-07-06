<?php

/**
 * Хелп функции для работы с ui элементами
 * Class __ui
 */
class __ui
{
  /**
   * Получаем порядковый номер выбранных элементов
   * @param $arr
   * @param $select_ids
   * @return array
   * @internal param $select
   */
  public static function get_indexes($arr, $select_ids)
  {
    $indexes = array();
    foreach($select_ids as $select_id)
    {
      for($i = 0, $size = count($arr); $i < $size; ++$i)
      {
        if ($arr[ $i ]["id"] == $select_id)
        {
          $indexes[] = $i;
          break;
        }
      }
    }
    return $indexes;
  }

  /**
   * Получение значений выбранных элементовы
   * @param $arr
   * @param $select_ids
   * @return array
   */
  public static function get_values($arr, $select_ids)
  {
    $values = array();
    foreach($select_ids as $select_id)
    {
      for($i = 0, $size = count($arr); $i < $size; ++$i)
      {
        if ($arr[ $i ]["id"] == $select_id)
        {
          $values[] = $arr[ $i ]["caption"];
          break;
        }
      }
    }
    return $values;
  }

  /**
   * Получение значений выбранных элементовы по маске
   * @param $arr
   * @param $mask
   * @return array
   */
  public static function get_values_by_mask($arr, $mask)
  {
    $values = array();
    for($i = 0, $size = count($arr); $i < $size; ++$i)
      if ($arr[ $i ]["id"] & $mask)
        $values[] = $arr[ $i ]["caption"];
    return $values;
  }

  /**
   * Получаем порядковый номер выбранных элементов
   * @param $arr
   * @param $mask
   * @return array
   */
  public static function get_indexes_by_mask($arr, $mask)
  {
    $indexes = array();
    for($i = 0, $size = count($arr); $i < $size; ++$i)
      if ($arr[ $i ]["id"] & $mask)
        $indexes[] = $i;
    return $indexes;
  }

  /**
   * Получение значений выбраного элемента
   * @param $arr
   * @param $select_id
   * @return bool|string
   */
  public static function get_value($arr, $select_id)
  {
    $value = false;
    for($i = 0, $size = count($arr); $i < $size; ++$i)
    {
      if ($arr[ $i ]["id"] == $select_id)
      {
        $value = $arr[ $i ]["caption"];
        break;
      }
    }
    return $value;
  }

  /**
   * Получение индекса выбраного элемента
   * @param $arr
   * @param $select_id
   * @return bool|string
   */
  public static function get_index($arr, $select_id)
  {
    $value = false;
    for($i = 0, $size = count($arr); $i < $size; ++$i)
    {
      if ($arr[ $i ]["id"] == $select_id)
      {
        $value = $i;
        break;
      }
    }
    return $value;
  }
}