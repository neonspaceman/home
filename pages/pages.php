<?php

class pages extends __page
{
  public function __index()
  {
    $this->insert_style("/template/css/main.css", time());
    $this->set_template("main");
  }

  public function __page404()
  {
    $this->set_title("Страница не найдена");
    $this->set_template("404");
  }
};