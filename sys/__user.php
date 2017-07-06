<?php

class __user
{
  private static $instance = null;
  public static function get_instance()
  {
    if (self::$instance === null)
      self::$instance = new self;
    return self::$instance;
  }

  private $info = array(
    "logged" => false
  );

  /**
   * Получаем информацию о пользователе
   */
  public function exec()
  {
    $db = __database::get_instance();
    $id = __data::cookie("id", "s");
    $hash = __data::cookie("hash", "s");
    if ($id && $hash)
    {
      $q = "select `name` from `users` where `id` = ? and `hash` = ? limit 1";
      $stmt = $db->prepare($q) or die($db->error);
      $stmt->bind_param("is", $id, $hash);
      $stmt->execute() or die($db->error);
      $res = $stmt->get_result();
      $row = $res->fetch_assoc();
      $res->close();
      $stmt->close();
      if ($row)
      {
        $this->info = array(
          "logged" => true,
          "id" => $id,
          "name" => $row["name"],
          "privacy" => 0,
        );
        $time = time();
        $q = "update `users` set `time_last_update` = ? where `id` = ? limit 1";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("ii", $time, $id);
        $stmt->execute() or die($db->error);
        $stmt->close();
      }
    }
  }

  /**
   * Полчить информацию по имени
   * @param bool $name
   * @return array|mixed
   */
  public function get($name = false)
  {
    return $name == false ? $this->info : $this->info[$name];
  }
};