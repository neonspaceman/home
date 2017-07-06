<?php

class user extends __page
{
  public function view_list()
  {
    $this->insert_style("/template/css/user_view_list.css", time());
    $this->insert_script("/template/js/user_view_list.js", time());
    $this->set_template("user_view_list");
  }

  public function add_popup()
  {
    $this->set_template("popup/user_add");
  }

  public function edit_popup()
  {
    $db = __database::get_instance();
    $id = __data::get("id", "u");

    $user_exists = 0;
    $q = "select count(*) `count` from `users` where `id` = ? limit 1";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $id);
    $stmt->execute() or die($db->error);
    $stmt->bind_result($user_exists);
    $stmt->fetch();
    $stmt->close();

    if ($user_exists)
      $this->set_template("popup/user_edit");
    else
      $this->set_template("popup/404");
  }

  public function login_popup()
  {
    $this->set_template("popup/user_login");
  }
};