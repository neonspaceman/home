<?php

require_once ROOT . "/resource/object_values.php";

$db = __database::get_instance();
$user = __user::get_instance();

$object_id = __data::get("id", "u");
$object_type = "room";
$q = "select
        `rooms`.`id`, `source`, `exclusive`, `quickly`, `id_region`, `id_street`, `house`, `flat`, `guide`, `lon`, `lat`,
        `count_rooms`, `related_rooms`, `type_of_room`, `count_sleeps`, `floor`, `floors`, `square_general`, `square_living`, 
        `square_kitchen`, `state`, `heating`, `hot_water`, `wc`, `window`, `furniture`, `count_balcony`, `type_balcony`,
        `multimedia`, `comfort`, `additionally`, `date_rent`, `prepayment`, `for_whom`, `description`,
        `date_price`, `price`, `guaranty`, `price_additionally`, `service_mark`, `time_create`, `visibility`,
        `streets`.`name` `name_street`, `regions`.`name` `name_region`, `regions`.`parent` `id_parent_region`
      from 
        `rooms` 
        left join `streets` on `streets`.`id` = `rooms`.`id_street`
        left join `regions` on `regions`.`id` = `rooms`.`id_region`
      where 
        `rooms`.`id` = ?
      limit 1";
$stmt = $db->prepare($q) or die($db->error);
$stmt->bind_param("i", $object_id);
$stmt->execute() or die($db->error);
$res = $stmt->get_result();
$object = $res->fetch_assoc();
$res->close();
$stmt->close();

$object["source"] = __ui::get_value(OBJECT_SOURCE, $object["source"]);

$object["floor"] = $object["floor"] ? $object["floor"] . ($object["floors"] ? " из " . $object["floors"] : "") : false;
$object["count_rooms"] = __ui::get_value(OBJECT_COUNT_ROOMS, $object["count_rooms"]);
$object["type_of_room"] = __ui::get_value(ROOM_TYPE_OF_ROOM, $object["type_of_room"]);
$object["count_sleeps"] = $object["count_sleeps"]
  ? ($object["count_sleeps"] . " " . get_num_ending($object["count_sleeps"], array("спальное место", "спальных места", "спальных мест")))
  : false;
$object["square_general"] = $object["square_general"] ? $object["square_general"] . " м<sup>2</sup>" : false;
$object["square_kitchen"] = $object["square_kitchen"] ? $object["square_kitchen"] . " м<sup>2</sup>" : false;
$object["square_living"] = $object["square_living"] ? $object["square_living"] . " м<sup>2</sup>" : false;
$object["wc"] = __ui::get_value(OBJECT_WC, $object["wc"]);
$object["heating"] = __ui::get_value(OBJECT_HEATING, $object["heating"]);
$object["hot_water"] = __ui::get_value(OBJECT_HOT_WATER, $object["hot_water"]);
$object["window"] = __ui::get_value(OBJECT_WINDOW, $object["window"]);
$object["state"] = __ui::get_value(OBJECT_STATE, $object["state"]);
$object["type_balcony"] = __ui::get_value(OBJECT_TYPE_BALCONY, $object["type_balcony"]);
$object["balcony"] = $object["count_balcony"]
  ? $object["count_balcony"] . ($object["type_balcony"] ? ", " . $object["type_balcony"] : "")
  : false;
$object["furniture"] = __ui::get_values_by_mask(OBJECT_FURNITURE, $object["furniture"]);
$object["multimedia"] = __ui::get_values_by_mask(OBJECT_MULTIMEDIA, $object["multimedia"]);
$object["comfort"] = __ui::get_values_by_mask(OBJECT_COMFORT, $object["comfort"]);
$object["additionally"] = __ui::get_values_by_mask(OBJECT_ADDITIONALLY, $object["additionally"]);
$object["for_whom"] = __ui::get_values_by_mask(OBJECT_FOR_WHOM, $object["for_whom"]);

$object["price"] = number_format($object["price"], 0, false, " ") . "&nbsp;<i class=\"fa fa-rub\" aria-hidden=\"true\"></i>";
$object["guaranty"] = $object["guaranty"]
  ? number_format($object["guaranty"], 0, false, " ") . "&nbsp;<i class=\"fa fa-rub\" aria-hidden=\"true\"></i>"
  : false;
$object["prepayment"] = $object["prepayment"]
  ? "за " . $object["prepayment"] . " " . get_num_ending($object["prepayment"], array("месяц", "месяца", "месяцев"))
  : false;
$object["price_additionally"] = __ui::get_values_by_mask(OBJECT_PRICE_ADDITIONALLY, $object["price_additionally"]);

?>
<!DOCTYPE html>
<html>

<head>
  <?php require_once "head.php" ?>
  <script>
    var params = {
      coords: <?= json_encode(array($object["lat"], $object["lon"])) ?>,
      zoom: 16
    };
  </script>
</head>

<body>

<div class="wrapper">

  <?php require_once "header.php" ?>

  <div class="page_title">
    <h1>Просмотр комнаты</h1>
  </div>

	<main class="content">

    <div class="dynamic_columns">
      <div class="dynamic_column"><?php if ($object["source"] || $object["exclusive"] || $object["quickly"]): ?>
        <div class="group_options column">
          <div class="option">
            <div class="option_label">Источник:</div>
            <div class="option_value"><?= $object["source"] ? $object["source"] : "&mdash;" ?></div>
          </div>
          <?php if ($object["exclusive"] || $object["quickly"]): ?>
          <div class="option">
            <div class="option_value">
              <?= $object["exclusive"] ? "<i class=\"ico fa fa-star\" title=\"Эксклюзив\" aria-hidden=\"true\"></i>" : "" ?>
              <?= $object["quickly"] ? "<i class=\"ico fa fa-bolt\" title=\"Срочно\" aria-hidden=\"true\"></i>" : "" ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php
        endif;
        if ($object["floor"] || $object["count_rooms"] || $object["type_of_room"] || $object["count_sleeps"] ||
            $object["square_general"] || $object["square_kitchen"] || $object["square_living"] ||
            $object["wc"] || $object["heating"] || $object["hot_water"] || $object["window"] ||
            $object["state"] || $object["balcony"]):
        ?>
        <ul class="group_options column">
          <?php if ($object["floor"]): ?>
          <li class="option">
            <span class="option_label">Этаж:</span>
            <span class="option_value"><?= $object["floor"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["count_rooms"]): ?>
          <li class="option">
            <span class="option_label">Кол-во комнат:</span>
            <span class="option_value"><?= $object["count_rooms"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["type_of_room"]): ?>
            <li class="option">
              <span class="option_label">Проживание:</span>
              <span class="option_value"><?= $object["type_of_room"] ?></span>
            </li>
          <?php endif; ?>
          <?php if ($object["count_sleeps"]): ?>
          <li class="option">
            <span class="option_label">Спальные места:</span>
            <span class="option_value"><?= $object["count_sleeps"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["square_general"]): ?>
          <li class="option">
            <span class="option_label">Общая площадь:</span>
            <span class="option_value"><?= $object["square_general"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["square_kitchen"]): ?>
          <li class="option">
            <span class="option_label">Площадь кухни:</span>
            <span class="option_value"><?= $object["square_kitchen"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["square_living"]): ?>
          <li class="option">
            <span class="option_label">Жилая площадь:</span>
            <span class="option_value"><?= $object["square_living"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["wc"]): ?>
          <li class="option">
            <span class="option_label">Сан. узел:</span>
            <span class="option_value"><?= $object["wc"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["heating"]): ?>
          <li class="option">
            <span class="option_label">Отопление:</span>
            <span class="option_value"><?= $object["heating"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["hot_water"]): ?>
          <li class="option">
            <span class="option_label">Горячая вода:</span>
            <span class="option_value"><?= $object["hot_water"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["window"]): ?>
          <li class="option">
            <span class="option_label">Окна:</span>
            <span class="option_value"><?= $object["window"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["state"]): ?>
          <li class="option">
            <span class="option_label">Состояние:</span>
            <span class="option_value"><?= $object["state"] ?></span>
          </li>
          <?php endif; ?>
          <?php if ($object["balcony"]): ?>
          <li class="option">
            <span class="option_label">Балкон / лоджия:</span>
            <span class="option_value"><?= $object["count_rooms"] ?></span>
          </li>
          <?php endif; ?>
        </ul>
        <?php
        endif;
        if (!empty($object["furniture"]) || !empty($object["multimedia"]) || !empty($object["comfort"]) ||
                  !empty($object["additionally"]) || !empty($object["for_whom"])): ?>
        <ul class="group_options column">
          <?php if (!empty($object["furniture"])): ?>
          <li class="option">
            <div class="option_label">Мебель:</div>
            <div class="option_values">
              <?php foreach($object["furniture"] as $furniture): ?>
                <div class="option_value">&mdash;&nbsp;<?= $furniture ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
          <?php if (!empty($object["multimedia"])): ?>
          <li class="option">
            <div class="option_label">Мультимедия:</div>
            <div class="option_values">
              <?php foreach($object["multimedia"] as $multimedia): ?>
                <div class="option_value">&mdash;&nbsp;<?= $multimedia ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
          <?php if (!empty($object["comfort"])): ?>
          <li class="option">
            <div class="option_label">Бытовая техника:</div>
            <div class="option_values">
              <?php foreach($object["comfort"] as $comfort): ?>
                <div class="option_value">&mdash;&nbsp;<?= $comfort ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
          <?php if (!empty($object["additionally"])): ?>
          <li class="option">
            <div class="option_label">Дополнительно:</div>
            <div class="option_values">
              <?php foreach($object["additionally"] as $additionally): ?>
                <div class="option_value">&mdash;&nbsp;<?= $additionally ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
          <?php if (!empty($object["for_whom"])): ?>
          <li class="option">
            <div class="option_label">Кого желательно:</div>
            <div class="option_values">
              <?php foreach($object["for_whom"] as $for_whom): ?>
                <div class="option_value">&mdash;&nbsp;<?= $for_whom ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
        </ul>
        <?php
        endif;
        if (!empty($object["description"])): ?>
        <div class="group_options">
          <div class="option">
            <div class="option_label">Дополнительное описание:</div>
            <div class="option_value"><?= text($object["description"]) ?></div>
          </div>
        </div>
        <?php
        endif;
        if (!empty($object["service_mark"])): ?>
        <div class="group_options">
          <div class="option">
            <div class="option_label">Служебные пометки:</div>
            <div class="option_value"><?= text($object["service_mark"]) ?></div>
            </div>
          </div>
        <?php
        endif;
        ?></div>
      <div class="dynamic_column">
        <div class="group_options landlord">
          <?php
          $q = "select
            `landlords`.`name`, GROUP_CONCAT(`phones`.`phone`) `phone`
          from
            `landlords_objects`
            left join `landlords` on `landlords`.`id` = `landlords_objects`.`id_landlord`
            left join `phones` on `phones`.`id_landlord` = `landlords_objects`.`id_landlord`
          where
            `landlords_objects`.`id_object` = ? and `landlords_objects`.`type_object` = ?
          group by
            `landlords_objects`.`id_landlord`";
          $stmt = $db->prepare($q) or die($db->error);
          $stmt->bind_param("is", $object_id, $object_type);
          $stmt->execute() or die($db->error);
          $res = $stmt->get_result();
          while($landlord = $res->fetch_assoc()):
          ?>
          <div class="option">
            <span class="option_label"><?= text($landlord["name"]) ?></span>
            <span class="option_value">
            <?php
            $phones = explode(",", $landlord["phone"]);
            $str_phones = "";
            if ($user->get("logged"))
            {
              foreach($phones as $phone)
              {
                if ($str_phones)
                  $str_phones .= ", ";
                $phone = preg_replace("/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/", "+$1 $2 $3 $4 $5", $phone);
                $phone = preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$1 $2 $3", $phone);
                $str_phones .= "<a href='tel:" . preg_replace("/\s/", "", $phone) . "'>" . $phone . "</a>";
              }
            }
            else
            {
              $str_phones = "<a onclick='User.openLoginPopup()'>Показать номер</a>";
            }
            echo $str_phones;
            ?>
            </span>
          </div>
          <?php
          endwhile;
          $res->close();
          $stmt->close();
          ?>
        </div>
        <ul class="group_options column pay">
          <li class="option">
            <div class="option_label">Цена:</div>
            <div class="option_value"><?= $object["price"] ?></div>
          </li>
          <?php if ($object["guaranty"]): ?>
          <li class="option">
            <div class="option_label">Залог:</div>
            <div class="option_value"><?= $object["guaranty"] ?></div>
          </li>
          <?php endif; ?>
          <?php if ($object["prepayment"]): ?>
          <li class="option">
            <div class="option_label">Предоплата:</div>
            <div class="option_value"><?= $object["prepayment"] ?></div>
          </li>
          <?php endif; ?>
          <?php if (!empty($object["price_additionally"])): ?>
          <li class="option additionally_pay">
            <div class="option_label">Дополнительная оплата:</div>
            <div class="option_values">
              <?php foreach($object["price_additionally"] as $price_additionally): ?>
                <div class="option_value">&mdash;&nbsp;<?= $price_additionally ?></div>
              <?php endforeach; ?>
            </div>
          </li>
          <?php endif; ?>
        </ul>
        <div class="group_options address">
        <?php
        if (!is_null($object["id_parent_region"]))
        {
          $name_parent_region = "";
          $q = "select `name` from `regions` where `id` = ? limit 1";
          $stmt = $db->prepare($q) or die($db->error);
          $stmt->bind_param("i", $object["id_parent_region"]);
          $stmt->execute() or die($db->error);
          $stmt->bind_result($name_parent_region);
          $stmt->fetch();
          $stmt->close();
          $object["name_region"] .= " (" . $name_parent_region . ")";
        }
        $address = array(
          "region" => array("prefix" => "р-н.", $object["name_region"]),
          "street" => array("prefix" => "ул.", $object["name_street"]),
          "house" => array("prefix" => "", $object["house"]),
          "flat" => array("prefix" => "кв.", $object["flat"])
        );
        $str_address = "";
        foreach($address as $item)
        {
          if (!empty($item[0]))
          {
            if ($str_address)
              $str_address .= ", ";
            $str_address .= $item["prefix"] . " " . $item[0];
          }
        }
        ?>
          <div class="option">
            <span class="option_label">Адрес:</span>
            <span class="option_value"><?= text($str_address) ?></span>
          </div>
          <?php if (!empty($object["guide"])): ?>
          <div class="option">
            <span class="option_label">Ориентир:</span>
            <span class="option_value"><?= text($object["guide"]) ?></span>
          </div>
          <?php endif; ?>
          <div id="map" class="option map"><div class="map_loading"><span>Загрузка</span></div></div>
        </div>
        <?php
        $q = "select `id`, `thumb` from `images` where `id_object` = ? and `type_object` = 'room'";
        $stmt = $db->prepare($q) or die($db->error);
        $stmt->bind_param("i", $object_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0):
        ?>
        <div class="group_options photo">
        <?php while($image = $res->fetch_assoc()): ?>
          <div class="cover"><img onclick="PhotoViewer.show({ id: <?= $image["id"] ?>, object: <?= $object_id ?>, type: '<?= $object_type ?>'})" src="<?= $image["thumb"] ?>" /></div>
        <?php endwhile; ?>
        </div>
        <?php
        endif;
        $res->close();
        $stmt->close();
        ?>
      </div>
    </div>

  </main><!-- .content -->

  <?php require_once "footer.php" ?>

</div><!-- .wrapper -->

</body>
</html>