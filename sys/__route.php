<?php

/**
 * Роутинг
 */
class __route
{
  private static $instance = null;
  public static function get_instance()
  {
    if (self::$instance === null)
      self::$instance = new self;
    return self::$instance;
  }

  /**
   * Правила для роутинга
   * @var array
   */
  private $rules = array();
  /**
   * Страница по умолчанию
   * @var \array
   */
  private $default = array("regexp" => "[default]", "page" => "pages", "method" => "__page404");
  /**
   * Передаваемый url
   * @var string
   */
  private $url = false;
  /**
   * Текущее правило
   * @var \array
   */
  private $current = false;

  /**
   * Добавить правило
   * @param $options
   */
  public function insert_rule($options)
  {
    $this->rules[] = array_merge(
      array("regexp" => "", "page" => "pages", "method" => "__index"),
      $options
    );
  }

  /**
   * Установить правило для дефолта
   * @param $options
   */
  public function set_default($options)
  {
    $this->default = array_merge($this->default, $options);
  }

  /**
   * Выполнить роутинг
   */
  public function exec()
  {
    // подготовка url
    $this->url = ltrim($_SERVER["REQUEST_URI"], "/");
    $pos_que = mb_strpos($this->url, "?");
    if ($pos_que !== false)
      $this->url = mb_substr($this->url, 0, $pos_que);

    // поиск совпдаения
    foreach($this->rules as $rule)
    {
      if (preg_match("/^{$rule["regexp"]}$/", $this->url, $matches))
      {
        $this->current = array_merge($rule, array("matches" => $matches));
        break;
      }
    }
    if ($this->current === false)
      $this->current = array_merge($this->default, array("matches" => array()));

    // запуск секции страницы
    $page = __page::get_instance($this->current["page"]);
    $page->exec($this->current["method"]);
  }

  /**
   * Текущая страница
   * @return array
   */
  public function get_current()
  {
    return $this->current;
  }

  /**
   * Получить все совпадения по реуглярному выражению
   * @return array
   */
  public function get_matches()
  {
    return $this->current["matches"];
  }

  /**
   * Текущий url
   * @return string
   */
  public function get_url()
  {
    return $this->url;
  }
}