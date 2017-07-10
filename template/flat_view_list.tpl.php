<?php

require_once ROOT . "/resource/flat_values.php";

$db = __database::get_instance();
$user = __user::get_instance();

// фильтры
__filter::add("id_region", "Region", get_regions());
__filter::add("floor", "Counter");
__filter::add("count_rooms", "Select", FLAT_COUNT_ROOMS);
__filter::add("square_general", "Counter");
__filter::add("price", "Counter");
__filter::exec();

// кол-во объектов
$count_flats = 0;
$stmt = __flats::get_count_stmt();
$stmt->execute() or die($db->error);
$stmt->bind_result($count_flats);
$stmt->fetch();
$stmt->close();

$order_by = get_order(FLAT_ORDERS);
$offset = get_offset(RECORDS_ON_PAGE, $count_flats);

?>
<!DOCTYPE html>
<html>

<head>
  <?php require_once "head.php" ?>
  <script>
    var params = {
      orders: <?= json_encode(FLAT_ORDERS) ?>,
      orderBy: <?= json_encode($order_by) ?>,
      offset: <?= json_encode($offset) ?>,
      recordsOnPage: <?= json_encode(RECORDS_ON_PAGE) ?>,
      countRecords: <?= json_encode($count_flats) ?>,
      region: {
        data: <?= json_encode(get_regions()) ?>,
        select: <?= json_encode(__filter::get_value_by_name("id_region")) ?>
      },
      floor: {
        data: <?= json_encode(array_merge(__filter::get_limit_by_name("floor"), array(1))) ?>,
        select: <?= json_encode(__filter::get_value_by_name("floor")) ?>
      },
      countRooms: {
        data: <?= json_encode(FLAT_COUNT_ROOMS) ?>,
        select: <?= json_encode(__filter::get_value_by_name("count_rooms")) ?>
      },
      squareGeneral: {
        data: <?= json_encode(array_merge(__filter::get_limit_by_name("square_general"), array(1))) ?>,
        select: <?= json_encode(__filter::get_value_by_name("square_general")) ?>
      },
      price: {
        data: <?= json_encode(array_merge(__filter::get_limit_by_name("price"), array(1000))) ?>,
        select: <?= json_encode(__filter::get_value_by_name("price")) ?>
      }
    };
  </script>
</head>

<body>

<div class="wrapper">

  <?php require_once "header.php" ?>

  <div class="page_title">
    <h1>Аренда квартир в Чите</h1>
    <div class="action">
      <div class="count"><?= number_format($count_flats, 0, "", " ") ?>&nbsp;<?= get_num_ending($count_flats, array("квартира отсортирована", "квартиры отсортированы", "квартир отсортированы")) ?></div>
      <div id="order_by" class="order_by js_loading"></div>
      <div id="pagination" class="pagination js_loading"></div>
    </div>
  </div>

	<main class="content">

    <div class="left_col">

      <div id="filter" class="js_loading"></div>

    </div>

    <div class="main_col objects">

      <?php

      // stmt image
      $q = "select `thumb` from `images` where `id_object` = ? order by `id`";
      $stmt_image = $db->prepare($q) or die($db->error);

      // stmt count phones
      $q = "select count(*) `count` from `phones` where `id_landlord` in (select `id_landlord` from `landlords_flats` where `id_flat` = ?)";
      $stmt_count_phones = $db->prepare($q) or die($db->error);

      // stmt landlord
      $q = "select
              `landlords`.`name`, `phones`.`phone`
            from
              `landlords_flats`
              left join `landlords` on `landlords`.`id` = `landlords_flats`.`id_landlord`
              left join `phones` on `phones`.`id_landlord` = `landlords_flats`.`id_landlord`
            where
              `landlords_flats`.`id_flat` = ?
            limit 1";
      $stmt_landlord = $db->prepare($q) or die($db->error);

      $stmt = __flats::get_stmt($order_by, $offset);
      $stmt->execute() or die($db->error);
      $res_object = $stmt->get_result();
      while($object = $res_object->fetch_assoc()):

        $object["count_rooms"] = __ui::get_value(FLAT_COUNT_ROOMS, $object["count_rooms"]);

        $count_images = 0;
        $cover = null;
        $stmt_image->bind_param("i", $object["id"]);
        $stmt_image->execute() or die($db->error);
        $stmt_image->bind_result($cover);
        $stmt_image->store_result();
        $count_images = $stmt_image->num_rows;
        $stmt_image->fetch();
        if (is_null($cover))
          $cover = "/template/images/camera_100.png";

        $count_phones = 0;
        $stmt_count_phones->bind_param("i", $object["id"]);
        $stmt_count_phones->execute() or die($db->error);
        $stmt_count_phones->bind_result($count_phones);
        $stmt_count_phones->store_result();
        $stmt_count_phones->fetch();

        $landlord = array("name" => null, "phone" => null);
        $stmt_landlord->bind_param("i", $object["id"]);
        $stmt_landlord->execute() or die($db->error);
        $stmt_landlord->bind_result($landlord["name"], $landlord["phone"]);
        $stmt_landlord->store_result();
        $stmt_landlord->fetch();
        $landlord["phone"] = preg_replace("/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/", "+$1 $2 $3 $4 $5", $landlord["phone"]);
        $landlord["phone"] = preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$1 $2 $3", $landlord["phone"]);
      ?>

      <div class="object_item">
        <a href="/flat/view?id=<?= $object["id"] ?>" class="cover" style="background-image: url(<?= $cover ?>);">
          <div class="count"><i class="fa fa-camera" aria-hidden="true"></i><?= $count_images ?></div>
        </a>
        <div class="general">
          <a href="/flat/view?id=<?= $object["id"] ?>" class="address">ул. <?= text($object["street_name"]) ?>, <?= text($object["house"]) ?></a>
          <a href="/flat/view?id=<?= $object["id"] ?>" class="view_on_map"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp;Посмотреть на карте</a>
          <div class="short">
            <?php if ($object["count_rooms"]): ?><span><?= $object["count_rooms"] ?></span><?php endif; ?>
            <?php if ($object["floor"]): ?><span><?= $object["floor"] ?>&nbsp;этаж</span><?php endif; ?>
            <?php if ($object["square_general"]): ?><span><?= $object["square_general"] ?>&nbsp;м<sup>2</sup></span><?php endif; ?>
          </div>
          <div class="price"><?= number_format($object["price"], 0, "", "&nbsp;") ?>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></div>
        </div>
        <table class="info">
          <tr>
            <td>
              <?php if ($object["furniture"] > 0): ?>
              <div class="furniture">
                <div class="title">Мебель</div>
                <ul class="icons">
                  <?php
                  foreach(FLAT_FURNITURE as $furniture)
                    if ($furniture["id"] & $object["furniture"])
                      echo "<li title=\"" . $furniture["caption"] . "\" class=\"" . $furniture["class"] . "\"></li>";
                  ?>
                </ul>
              </div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($object["multimedia"] > 0): ?>
              <div class="multimedia">
                <div class="title">Мультимедия</div>
                <ul class="icons">
                  <?php
                  foreach(FLAT_MULTIMEDIA as $multimedia)
                    if ($multimedia["id"] & $object["multimedia"])
                      echo "<li title=\"" . $multimedia["caption"] . "\" class=\"" . $multimedia["class"] . "\"></li>";
                  ?>
                </ul>
              </div>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <?php if ($object["comfort"] > 0): ?>
              <div class="comfort">
                <div class="title">Бытовая техника</div>
                <ul class="icons">
                  <?php
                  foreach(FLAT_COMFORT as $comfort)
                    if ($comfort["id"] & $object["comfort"])
                      echo "<li title=\"" . $comfort["caption"] . "\" class=\"" . $comfort["class"] . "\"></li>";
                  ?>
                </ul>
              </div>
              <?php endif; ?>
            </td>
          </tr>
        </table>
        <div class="action">
          <div class="landlord">
            <div class="name"><?= text($landlord["name"]) ?></div>
            <?php if ($user->get("logged")): ?>
            <div class="phone"><?= $landlord["phone"] ?></div>
              <?php if ($count_phones > 1): ?>
              <a href="/flat/view?id=<?= $object["id"] ?>" class="more">ещё&nbsp;<?= $count_phones - 1 ?>&nbsp;<?= get_num_ending($count_phones - 1, array("номер", "номера", "номеров")) ?>...</a>
              <?php endif; ?>
            <?php else: ?>
            <a onclick="loginPopup.show();" class="more">показать номер</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="time" data-timestamp="<?= $object["time_create"] ?>"></div>
      </div>

      <?php
      endwhile;
      $res_object->close();
      $stmt->close();
      $stmt_landlord->close();
      $stmt_image->close();
      ?>

    </div>

    <div class="clear"></div>

  </main><!-- .content -->

  <?php require_once "footer.php" ?>

</div><!-- .wrapper -->

</body>
</html>