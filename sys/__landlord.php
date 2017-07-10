<?php

/**
 * Аредодатели
 * Class Landlord
 */
class __landlord
{
  private $query = "";

  /**
   * Генерация запроса из полченных данных
   * Landlord constructor.
   */
  function __construct()
  {
    foreach (__data::post("landlord", "s[]") as $command)
      $this->query .= $command;
  }

  /**
   * Проверка телефонов на дубликаты
   * @return bool
   */
  public function has_duplicates()
  {
    $db = __database::get_instance();

    $phones = array(null);
    preg_match_all("/\/phone\/(\d{11}|\d{6})/", $this->query, $matches);
    foreach($matches[1] as $phone)
      $phones[] = $phone;

    $count = 0;
    $placeholders = implode(array_fill(0, count($phones), "?"), ",");
    $mask = str_repeat("s", count($phones));
    $q = "select count(*) `count` from `phones` where `phone` in (" . $placeholders . ")";
    $stmt = $db->prepare($q) or die($db->error);
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($mask), array_map(function(&$item){ return $item; }, $phones)));
    $stmt->execute() or die($db->error);
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
  }

  /**
   * Добавление телефонов
   * @param $id_landlord - id арендодателя
   * @param $phones - строка с телефонами
   * @return bool - результат добавления номеров телефонов
   */
  public function exec_phones($id_landlord, $phones)
  {
    $success = true;
    $db = __database::get_instance();
    preg_match_all("/\/phone\/(\d{11}|\d{6})/", $phones, $phones);
    $q = "insert into `phones` (`id_landlord`, `phone`) values (?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    foreach($phones[1] as $phone)
    {
      $stmt->bind_param("is", $id_landlord, $phone);
      $stmt->execute() or die($db->error);
      if ($stmt->affected_rows < 1)
        $success = false;
    }
    $stmt->close();
    return $success;
  }

  /**
   * Обработка арендодателей
   * @return array|bool
   */
  public function exec()
  {
    $db = __database::get_instance();
    $response = __response::get_instance();

    if (empty($this->query))
    {
      $response->error("landlord is empty");
      return false;
    }

    if (!preg_match_all("/(?:\/new\/.*?(?:\/phone\/(?:\d{11}|\d{6}))+|\/id\/\d+(?:\/phone\/(?:\d{11}|\d{6}))*)/", $this->query, $commands))
    {
      $response->error("landlord has incorrect syntax");
      return false;
    }

    if ($this->has_duplicates())
    {
      $response->error("landlord's phones has duplicates");
      return false;
    }

    $landlords = array();
    $errors = array(
      "empty_name" => false,
      "not_create" => false,
      "incorrect_id" => false,
      "not_add_phones" => false
    );
    foreach($commands[0] as $command)
    {
      // прикрепление старого арендодателя
      if (preg_match("/^\/id\/([0-9]+)((?:\/phone\/(?:\d{11}|\d{6}))*)$/", $command, $matches))
      {
        $id = $matches[1];
        $count = 0;
        $q = "select count(*) `count` from `landlords` where `id` = ? limit 1";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("i", $id);
        $stmt->execute() or die($db->error);
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if (!$count)
        {
          $errors["incorrect_id"] = true;
          continue;
        }
        $phones = $matches[2];
        if (!$this->exec_phones($id, $phones))
        {
          $errors["not_add_phones"] = true;
          continue;
        }
        $landlords[] = $id;
      }
      // добаление нового арендодателя
      if (preg_match("/^\/new\/(.*?)((?:\/phone\/(?:\d{11}|\d{6}))+)$/", $command, $matches))
      {
        $name = $matches[1];
        if (empty($name))
        {
          $errors["empty_name"] = true;
          continue;
        }
        $q = "insert into `landlords` (`name`, `description`, `emails`) values (?, '', '')";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("s", $name);
        $stmt->execute() or die($db->error);
        $id = $stmt->insert_id;
        $stmt->close();
        if (!$id)
        {
          $errors["not_create"] = true;
          continue;
        }
        $phones = $matches[2];
        if (!$this->exec_phones($id, $phones))
        {
          $errors["not_add_phones"] = true;
          continue;
        }
        $landlords[] = $id;
      }
    }

    if ($errors["incorrect_id"])
    {
      $response->error("landlord's id is incorrect");
      return false;
    }
    if ($errors["empty_name"])
    {
      $response->error("landlord's name is empty");
      return false;
    }
    if ($errors["not_create"])
    {
      $response->error("landlord doesn't create");
      return false;
    }
    if ($errors["not_add_phones"])
    {
      $response->error("some of phones don't create");
      return false;
    }

    return $landlords;
  }
}