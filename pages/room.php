<?php

class room extends __page
{
  public function add()
  {
    $this->set_title("Добавление комнаты на аренду");
    $this->insert_script("//api-maps.yandex.ru/2.1/?lang=ru_RU");
    $this->insert_style("/template/css/object_manager.css", time());
    $this->insert_script("/template/js/object_manager.js", time());
    $this->insert_script("/template/js/room_add.js", time());
    $this->set_template("room_add");
  }

  public function view()
  {
    $db = __database::get_instance();

    $object_id = __data::get("id", "u");
    $object_exists = 0;
    $q = "select count(*) `count` from `rooms` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $object_id);
    $stmt->execute() or die($db->error);;
    $stmt->bind_result($object_exists);
    $stmt->fetch();
    $stmt->close();

    $this->set_title("Просмотр комнаты на аренду");
    if ($object_exists)
    {
      $this->insert_script("//api-maps.yandex.ru/2.1/?lang=ru_RU");
      $this->insert_style("/template/css/object_view.css", time());
      $this->insert_script("/template/js/object_view.js", time());
      $this->set_template("room_view");
    }
    else
    {
      $this->set_template("404");
    }
  }

  public function view_list()
  {
    $this->set_title("Аренда комнат в Чите");
    $this->insert_style("/template/ion.rangeSlider-2.1.7/css/ion.rangeSlider.css", time());
    $this->insert_script("/template/ion.rangeSlider-2.1.7/js/ion.rangeSlider.min.js");
    $this->insert_style("/template/css/ui.css", time());
    $this->insert_style("/template/css/object_view_list.css", time());
    $this->insert_script("/template/js/object_view_list.js", time());
    $this->set_template("room_view_list");
  }
}