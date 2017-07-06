<?php

/**
 * Страница
 */
class __page
{
  private static $instance = null;
  /**
   * Получить объект страницы
   * @param string|bool $page_name
   * @return \__page
   */
  public static function get_instance($page_name = false)
  {
    if (self::$instance === null)
    {
      $path = ROOT . "/pages/" . $page_name . ".php";
      if (!file_exists($path))
        return false;
      require_once $path;
      if (!class_exists($page_name))
        return false;
      self::$instance = new $page_name($page_name);
    }
    return self::$instance;
  }

  /**
   * Текущая страница
   * @var null
   */
  private $class_name = null;
  /**
   * Разметка
   * @var string
   */
  private $tpl = null;
  /*
   * Заголовок
   * @var string
   */
  private $title = DEFAULT_TITLE;
  /**
   * Ключевые слова
   * @var string
   */
  private $keywords = DEFAULT_KEYWORDS;
  /**
   * Описание
   * @var string
   */
  private $description = DEFAULT_DESCRIPTION;
  /**
   * Иконка
   * @var \stdClass
   */
  private $icon = null;
  /**
   * Стили
   * @var array
   */
  private $styles = array();
  /**
   * Скрипты
   * @var array
   */
  private $scripts = array();
  /**
   * Мета данные
   */
  private $meta = array();

  public function __construct($class_name)
  {
    $this->class_name = $class_name;
    $this->icon = new stdClass;
    $this->icon->url = DEFAULT_ICON_URL;
    $this->icon->ver = DEFAULT_ICON_VER;
  }

  /**
   * Установка шаблона
   * @param string $tpl
   */
  public function set_template($tpl)
  {
    $this->tpl = $tpl;
  }

  /**
   * Установка заголовка
   * @param string $title
   */
  public function set_title($title)
  {
    $this->title = $title;
  }

  /**
   * Вывод загоовка
   */
  public function render_title()
  {
    if ($this->title !== false)
      echo "<title>" . $this->title . "</title>";
  }

  /**
   * Установка ключевых слов
   * @param string $keywords
   */
  public function set_keywords($keywords)
  {
    $this->keywords = $keywords;
  }

  /**
   * Вывод ключевых слов
   */
  public function render_keywords()
  {
    if ($this->keywords !== false)
      echo "<meta name=\"keywords\" content=\"" . $this->keywords . "\" />";
  }

  /**
   * Вывод описания
   * @param string $desc
   */
  public function set_description($desc)
  {
    $this->description = $desc;
  }

  /**
   * Вывод описания
   */
  public function render_description()
  {
    if ($this->description !== false)
      echo "<meta name=\"description\" content=\"" . $this->description . "\" />";
  }

  /**
   * Устанвока иконки
   * @param string $icon
   * @param number $ver
   */
  public function set_icon($icon, $ver)
  {
    $this->icon->url = $icon;
    $this->icon->ver = $ver;
  }

  /**
   * Вывод иконки
   */
  public function render_icon()
  {
    if ($this->icon->url !== false)
      echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"" . $this->icon->url . ($this->icon->ver ? "?v=" . $this->icon->ver : "") . "\" >";
  }

  /**
   * Вывод страницы
   */
  public function render()
  {
    require_once ROOT . "/template/" . $this->tpl . ".tpl.php";
  }

  /**
   * Добавить стиль
   * @param $url
   * @param bool $ver
   */
  public function insert_style($url, $ver = false)
  {
    $this->styles[] = array(
      "url" => $url,
      "ver" => $ver
    );
  }

  /**
   * Вывод стилей
   */
  public function render_styles()
  {
    foreach($this->styles as $style)
      echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $style["url"] . ($style["ver"] ? "?v=" . $style["ver"] : "") . "\">";
  }

  /**
   * Добавить скрипт
   * @param $url
   * @param bool $ver
   */
  public function insert_script($url, $ver = false)
  {
    $this->scripts[] = array(
      "url" => $url,
      "ver" => $ver
    );
  }

  /**
   * Вывод скриптов
   */
  public function render_scripts()
  {
    foreach($this->scripts as $script)
      echo "<script src=\"" . $script["url"] . ($script["ver"] ? "?v=" . $script["ver"] : "") . "\"></script>";
  }

  /**
   * Вставить meta тег
   * @param $name
   * @param $content
   */
  public function insert_meta($name, $content)
  {
    $this->meta[] = array(
      "name" => $name,
      "content" => $content
    );
  }

  /**
   * Вывод мета тегов
   */
  public function render_meta()
  {
    foreach($this->meta as $meta)
      echo "<meta name=\"" . $meta["name"] . "\" content=\"" . $meta["content"] . "\" />";
  }

  /**
   * Запуск метода
   * @param string $method - метод запуска
   */
  public function exec($method = "__index")
  {
    if (method_exists($this, $method))
      $this->{$method}();
  }
}