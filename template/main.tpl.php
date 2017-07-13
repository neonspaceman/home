<?php
  require_once ROOT . "/resource/object_values.php";
  $db = __database::get_instance();
?>
<!DOCTYPE html>
<html>

<head>
  <?php require_once "head.php" ?>
</head>

<body>

<div class="wrapper">

  <?php require_once "header.php" ?>

  <div class="page_title">
    <h1>Добро пожаловать</h1>
  </div>

	<main class="content">

    <?php
    $count_flats = 0;
    $q = "select count(*) `count` from `flats`";
    $res = $db->query($q) or die($db->error);
    $row = $res->fetch_assoc();
    $count_flats = $row["count"];
    $res->close();
    ?>
    <div class="object_category">
      <div class="object_title">
        <h2><a href="/flats"><?= number_format($count_flats, 0, "", " ") . "&nbsp;" . get_num_ending($count_flats, array("квартира", "квартиры", "квартир")) ?> на аренду</a></h2>
        <div class="action"><a href="/flats">Посмотреть все квартиры</a></div>
        <div class="clear"></div>
      </div>
      <ul class="objects">
        <?php
        $q = "select
                `flats`.`id`, `streets`.`name` `name_street`, `flats`.`house`, `flats`.`count_rooms`,
                `flats`.`square_general`,
                (select `thumb` from `images` where `images`.`id_object` = `flats`.`id` and `images`.`type_object` = 'flat' limit 1) `thumb`
              from
                `flats`
                left join `streets` on `streets`.`id` = `flats`.`id_street`
              order by `flats`.`time_create` desc
              limit 5";
        $res = $db->query($q) or die($db->error);
        while($row = $res->fetch_assoc()):
          if (is_null($row["thumb"]))
            $row["thumb"] = "/template/images/camera_100.png";
          $row["desc"] = "";
          if ($row["count_rooms"])
          {
            $row["desc"] .= __ui::get_value(OBJECT_COUNT_ROOMS, $row["count_rooms"]);
            if ($row["square_general"])
              $row["desc"] .= ", " . $row["square_general"] . "&nbsp;м<sup>2</sup>";
          }
          $row["address"] = $row["name_street"] . ($row["house"] ? ", " . $row["house"] : "");
        ?>
        <li class="object_item">
          <a href="/flat?id=<?= $row["id"] ?>">
            <div class="cover" style="background-image: url(<?= $row["thumb"] ?>);"></div>
            <div class="address"><?= $row["address"] ?></div>
            <div class="description"><?= $row["desc"] ?></div>
          </a>
        </li>
        <?php
        endwhile;
        $res->close();
        ?>
      </ul>
    </div>


    <?php
    $count_rooms = 0;
    $q = "select count(*) `count` from `rooms`";
    $res = $db->query($q) or die($db->error);
    $row = $res->fetch_assoc();
    $count_rooms = $row["count"];
    $res->close();
    ?>
    <div class="object_category">
      <div class="object_title">
        <h2><a href="/rooms"><?= number_format($count_rooms, 0, "", " ") . "&nbsp;" . get_num_ending($count_rooms, array("комната", "комнаты", "комнат")) ?> на аренду</a></h2>
        <div class="action"><a href="/flats">Посмотреть все комнаты</a></div>
        <div class="clear"></div>
      </div>
      <ul class="objects">
        <?php
        $q = "select
                `rooms`.`id`, `streets`.`name` `name_street`, `rooms`.`house`, `rooms`.`count_rooms`,
                `rooms`.`square_general`,
                (select `thumb` from `images` where `images`.`id_object` = `rooms`.`id` and `images`.`type_object` = 'room' limit 1) `thumb`
              from
                `rooms`
                left join `streets` on `streets`.`id` = `rooms`.`id_street`
              order by `rooms`.`time_create` desc
              limit 5";
        $res = $db->query($q) or die($db->error);
        while($row = $res->fetch_assoc()):
          if (is_null($row["thumb"]))
            $row["thumb"] = "/template/images/camera_100.png";
          $row["desc"] = "";
          if ($row["count_rooms"])
          {
            $row["desc"] .= __ui::get_value(OBJECT_COUNT_ROOMS, $row["count_rooms"]);
            if ($row["square_general"])
              $row["desc"] .= ", " . $row["square_general"] . "&nbsp;м<sup>2</sup>";
          }
          $row["address"] = $row["name_street"] . ($row["house"] ? ", " . $row["house"] : "");
          ?>
          <li class="object_item">
            <a href="/room?id=<?= $row["id"] ?>">
              <div class="cover" style="background-image: url(<?= $row["thumb"] ?>);"></div>
              <div class="address"><?= $row["address"] ?></div>
              <div class="description"><?= $row["desc"] ?></div>
            </a>
          </li>
          <?php
        endwhile;
        $res->close();
        ?>
      </ul>
    </div>

    <?php
    $count_homes = 0;
    $q = "select count(*) `count` from `homes`";
    $res = $db->query($q) or die($db->error);
    $row = $res->fetch_assoc();
    $count_homes = $row["count"];
    $res->close();
    ?>
    <div class="object_category">
      <div class="object_title">
        <h2><a href="/homes"><?= number_format($count_homes, 0, "", " ") . "&nbsp;" . get_num_ending($count_homes, array("дом", "дома", "домов")) ?> на аренду</a></h2>
        <div class="action"><a href="/homes">Посмотреть все квартиры</a></div>
        <div class="clear"></div>
      </div>
      <ul class="objects">
        <?php
        $q = "select
                `homes`.`id`, `streets`.`name` `name_street`, `homes`.`house`, `homes`.`count_rooms`,
                `homes`.`square_general`,
                (select `thumb` from `images` where `images`.`id_object` = `homes`.`id` and `images`.`type_object` = 'home' limit 1) `thumb`
              from
                `homes`
                left join `streets` on `streets`.`id` = `homes`.`id_street`
              order by `homes`.`time_create` desc
              limit 5";
        $res = $db->query($q) or die($db->error);
        while($row = $res->fetch_assoc()):
          if (is_null($row["thumb"]))
            $row["thumb"] = "/template/images/camera_100.png";
          $row["desc"] = "";
          if ($row["count_rooms"])
          {
            $row["desc"] .= __ui::get_value(OBJECT_COUNT_ROOMS, $row["count_rooms"]);
            if ($row["square_general"])
              $row["desc"] .= ", " . $row["square_general"] . "&nbsp;м<sup>2</sup>";
          }
          $row["address"] = $row["name_street"] . ($row["house"] ? ", " . $row["house"] : "");
          ?>
          <li class="object_item">
            <a href="/home?id=<?= $row["id"] ?>">
              <div class="cover" style="background-image: url(<?= $row["thumb"] ?>);"></div>
              <div class="address"><?= $row["address"] ?></div>
              <div class="description"><?= $row["desc"] ?></div>
            </a>
          </li>
          <?php
        endwhile;
        $res->close();
        ?>
      </ul>
    </div>

  </main><!-- .content -->

  <?php require_once "footer.php" ?>

</div><!-- .wrapper -->

</body>
</html>