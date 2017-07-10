<?php

class flat extends __page
{
  public function view_list()
  {
    $this->insert_style("/template/ion.rangeSlider-2.1.7/css/ion.rangeSlider.css", time());
    $this->insert_script("/template/ion.rangeSlider-2.1.7/js/ion.rangeSlider.min.js");
    $this->insert_style("/template/css/ui.css", time());
    $this->insert_style("/template/css/object_view_list.css", time());
    $this->insert_script("/template/js/object_view_list.js", time());
    $this->set_template("flat_view_list");
  }

  public function add()
  {
    $this->set_title("Добавление квартиры на аренду");
    $this->insert_script("//api-maps.yandex.ru/2.1/?lang=ru_RU");
    $this->insert_style("/template/css/object_manager.css", time());
    $this->insert_script("/template/js/object_manager.js", time());
    $this->insert_script("/template/js/object_add.js", time());
    $this->set_template("flat_add");
  }

  public function view()
  {
    $db = __database::get_instance();

    $object_id = __data::get("id", "u");
    $object_exists = 0;
    $q = "select count(*) `count` from `flats` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $object_id);
    $stmt->execute() or die($db->error);;
    $stmt->bind_result($object_exists);
    $stmt->fetch();
    $stmt->close();

    $this->set_title("Просмотр квартиры");
    if ($object_exists)
    {
      $this->insert_script("//api-maps.yandex.ru/2.1/?lang=ru_RU");
      $this->insert_style("/template/css/object_view.css", time());
      $this->insert_script("/template/js/object_view.js", time());
      $this->set_template("flat_view");
    }
    else
    {
      $this->set_template("404");
    }
  }

  public function edit()
  {
    $db = __database::get_instance();

    $object_id = __data::get("id", "u");
    $object_exists = 0;
    $q = "select count(*) `count` from `flats` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $object_id);
    $stmt->execute() or die($db->error);;
    $stmt->bind_result($object_exists);
    $stmt->fetch();
    $stmt->close();

    $this->set_title("Редактирование квартиры");
    if ($object_exists)
    {
      $this->insert_script("//api-maps.yandex.ru/2.1/?lang=ru_RU");
      $this->insert_style("/template/css/object_manager.css", time());
      $this->insert_script("/template/js/object_manager.js", time());
      $this->insert_script("/template/js/object_edit.js", time());
      $this->set_template("flat_edit");
    }
    else
    {
      $this->set_template("404");
    }
  }
};