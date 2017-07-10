<?php
$db = __database::get_instance();
$id = __data::get("id", "u");
$name = false;

$q = "select `name` from `users` where `id` = ? limit 1";
$stmt = $db->prepare($q) or die($db->error);
$stmt->bind_param("i", $id);
$stmt->execute() or die($db->error);
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

?>
<div class="popup_html">
  <div class="popup_body">
    <div class="popup_header">
      <div class="popup_header_inner">Редактирование <?php text($name) ?></div>
      <a class="popup_close">закрыть</a>
    </div>
    <div class="popup_content">
      <form action="/act/user.php?act=edit" method="post">
        <label>ФИО</label>
        <input class="input" type="text" name="name" />
        <input type="hidden" name="id" value="<?= $id ?>" />
        <div class="button_wrap">
          <button class="button">Изменить</button>
        </div>
      </form>
    </div>
  </div>
</div>