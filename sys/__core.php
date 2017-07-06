<?php

/**
 * Ядро
 */
class __core
{
  private static $instance = null;
  public static function get_instance()
  {
    if (self::$instance === null)
      self::$instance = new self;
    return self::$instance;
  }

  /**
   * инициализация ядра(открытие соединений)
   */
  public function open()
  {
    $db = __database::get_instance();
    $db->real_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_BASE);
  }

  /**
   * закрытие всех соединений
   */
  public function close()
  {
    $db = __database::get_instance();
    $db->close();
  }
}