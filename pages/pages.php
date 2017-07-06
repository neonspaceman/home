<?php

class pages extends __page
{
  public function __page404()
  {
    $this->set_title("Страница не найдена");
    $this->set_template("404");
  }
};