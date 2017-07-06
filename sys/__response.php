<?php

/**
 * Ответ от backend
 * Class __response
 */
class __response
{
  private static $instance = null;
  public static function get_instance()
  {
    if (self::$instance === null)
      self::$instance = new self;
    return self::$instance;
  }

  public $response = array("status" => "success");

  /**
   * Проверка на успешность
   * @return boolean
   */
  public function is_success()
  {
    return $this->response["status"] == "success";
  }

  /**
   * Проверка на ошибки
   * @return bool
   */
  public function is_error()
  {
    return $this->response["status"] != "success";
  }

  /**
   * Установить ошибку
   * @param string $message
   */
  public function error($message)
  {
    $this->response["status"] = "error";
    if (!isset($this->response["message"]))
      $this->response["message"] = array();
    $this->response["message"][] = $message;
  }

  /**
   * Установить значение
   * @param string $key
   * @param mixed $value
   */
  public function set_value($key, $value)
  {
    $this->response[$key] = $value;
  }

  /**
   * Отправка ответа
   */
  public function send()
  {
    echo json_encode($this->response);
  }
}