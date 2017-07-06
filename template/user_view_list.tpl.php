<?php
$db = __database::get_instance();

$count_users = 0;
$q = "select count(*) `count` from `users`";
$stmt = $db->prepare($q) or die($db->error);
$stmt->execute() or die($db->error);
$stmt->bind_result($count_users);
$stmt->fetch();
$stmt->close();

$offset = get_offset(RECORDS_ON_PAGE, $count_users);
?>
<!DOCTYPE html>
<html>

<head>
  <?php require_once "head.php" ?>
  <script>
    var params = {
      offset: <?= json_encode($offset) ?>,
      recordsOnPage: <?= json_encode(RECORDS_ON_PAGE) ?>,
      countRecords: <?= json_encode($count_users) ?>
    };
  </script>
</head>

<body>

<div class="wrapper">

  <?php require_once "header.php" ?>

  <div class="page_title">
    <h1>Список клиентов</h1>
    <div class="action">
      <div class="count"><?= number_format($count_users, 0, "", " ") ?>&nbsp;<?= get_num_ending($count_users, array("клиент", "клиента", "клиентов")) ?></div>
      <div id="pagination" class="pagination js_loading"></div>
      <div class="buttons">
        <button onclick="Users.add()" class="button" title="Добавить нового клиента"><i class="fa fa-plus"></i></button>
      </div>
    </div>
  </div>

	<main class="content">
    <table class="table">
      <tr>
        <th>ФИО</th>
        <th>Код доступа</th>
        <th>Дата добавления</th>
        <th>Online</th>
        <th>Управление</th>
      </tr>
      <?php
      $now = time();
      $from = $now - 60 * 5;
      $to = $now + 60 * 5;
      $records_on_page = RECORDS_ON_PAGE;
      $q = "select `id`, `name`, `code`, `time_create`, `time_last_update` from `users` order by `time_create` desc limit ?, ?";
      $stmt = $db->prepare($q) or die($db->error);
      $stmt->bind_param("ii", $offset, $records_on_page);
      $stmt->execute() or die($db->error);
      $res = $stmt->get_result();
      while($row = $res->fetch_assoc()):
      ?>
      <tr>
        <td><a onclick="Users.edit(<?= $row["id"] ?>)"><?php print_t($row["name"]) ?></a></td>
        <td><?php print_t($row["code"]) ?></td>
        <td><span data-timestamp="<?= $row["time_create"] ?>"</td>
        <td>
          <?php
          if ($from <= $row["time_last_update"] && $row["time_last_update"] <= $to)
            echo "Online";
          else if ($row["time_last_update"])
            echo "<span data-timestamp='" . $row["time_last_update"] . "'></span>";
          else
            echo "&mdash;";
          ?>
        </td>
        <td class="action">
          <a onclick="Users.newCode('<?php print_t($row["name"]) ?>', <?= $row["id"] ?>)">Новый код доступа</a>
          <a onclick="Users.remove('<?php print_t($row["name"]) ?>', <?= $row["id"] ?>)">Удалить</a>
        </td>
      </tr>
      <?php
      endwhile;
      $res->close();
      $stmt->close();
      ?>
    </table>
  </main><!-- .content -->

  <?php require_once "footer.php" ?>

</div><!-- .wrapper -->

</body>
</html>