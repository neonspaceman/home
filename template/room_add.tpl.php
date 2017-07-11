<?php
require_once ROOT . "/resource/object_values.php";
$hash = md5(microtime());
?>
<!DOCTYPE html>
<html>

<head>
  <?php require_once "head.php" ?>
  <script>
    var params = {
      hash: <?= json_encode($hash) ?>,
      source: <?= json_encode(to_select(OBJECT_SOURCE)) ?>,
      exclusive: <?= json_encode(OBJECT_EXCLUSIVE) ?>,
      quickly: <?= json_encode(OBJECT_QUICKLY) ?>,
      region: <?= json_encode(to_select(get_regions(), true)) ?>,
      countRooms: <?= json_encode(to_select(OBJECT_COUNT_ROOMS)) ?>,
      relatedRooms: <?= json_encode(OBJECT_RELATIVE_ROOMS) ?>,
      typeOfRoom: <?= json_encode(ROOM_TYPE_OF_ROOM) ?>,
      furniture: <?= json_encode(OBJECT_FURNITURE) ?>,
      multimedia: <?= json_encode(OBJECT_MULTIMEDIA) ?>,
      comfort: <?= json_encode(OBJECT_COMFORT) ?>,
      additionally: <?= json_encode(OBJECT_ADDITIONALLY) ?>,
      wc: <?= json_encode(to_select(OBJECT_WC)) ?>,
      heating: <?= json_encode(to_select(OBJECT_HEATING)) ?>,
      hotWater: <?= json_encode(to_select(OBJECT_HOT_WATER)) ?>,
      window: <?= json_encode(to_select(OBJECT_WINDOW)) ?>,
      state: <?= json_encode(to_select(OBJECT_STATE)) ?>,
      typeBalcony: <?= json_encode(to_select(OBJECT_TYPE_BALCONY)) ?>,
      priceAdditionally: <?= json_encode(OBJECT_PRICE_ADDITIONALLY) ?>,
      forWhom: <?= json_encode(OBJECT_FOR_WHOM) ?>
    };
  </script>
</head>

<body>

<div class="wrapper">

  <?php require_once "header.php" ?>

  <div class="page_title">
    <h1>Добавление комнаты на аренду</h1>
  </div>

	<main class="content">

    <form id="form_room_add" action="/act/room.php?act=room_add" method="post">
      <input type="hidden" name="hash" value="<?= $hash ?>" />
      <div class="popup_content">
        <table class="column">
          <tr>
            <td class="left">
              <h2>Источник</h2>
              <table class="column">
                <tr>
                  <td class="left">
                    <input class="js_loading" name="source" type="text" />
                  </td>
                  <td style="width: 100px; padding-top: 5px;" class="middle">
                    <input class="js_loading" name="exclusive" type="text" />
                  </td>
                  <td style="width: 100px; padding-top: 5px;" class="right">
                    <input class="js_loading" name="quickly" type="text" />
                  </td>
                </tr>
              </table>
              <h2>Арендодатель</h2>
              <div id="landlord" class="js_loading"></div>
              <h2>Адрес</h2>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Город</label>
                    <input class="input" type="text" name="city" value="Чита" disabled="true" />
                    <label>Улица</label>
                    <input class="js_loading" name="street" type="text" />
                    <table class="column">
                      <tr>
                        <td class="left">
                          <label>Номер дома</label>
                          <input class="input" name="house" type="text" />
                        </td>
                        <td class="right">
                          <label>Номер квартиры</label>
                          <input class="input" name="flat" type="text" />
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td class="right">
                    <label>Район</label>
                    <input class="js_loading" name="region" type="text" />
                    <label>Ориентир</label>
                    <textarea class="input textarea_guide" name="guide"></textarea>
                  </td>
                </tr>
              </table>
            </td>
            <td class="right">
              <h2>Карта</h2>
              <div id="object_map" class="js_loading"></div>
            </td>
          </tr>
        </table>
        <h2>Сведения</h2>
        <table class="column">
          <tr>
            <td class="left">
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Команты</label>
                    <input class="js_loading" name="count_rooms" type="text" />
                  </td>
                  <td class="middle" style="padding-top: 25px;">
                    <input class="js_loading" name="related_rooms" type="text" />
                  </td>
                  <td class="right">&nbsp;</td>
                </tr>
              </table>
              <div>
                <label>Тип комнаты</label>
                <input class="js_loading" name="type_of_room" type="text" />
              </div>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Общая, м<sup>2</sup></label>
                    <div class="counter_normal"><input class="js_loading" name="square_general" type="text" /></div>
                  </td>
                  <td class="middle">
                    <label>Жилая, м<sup>2</sup></label>
                    <div class="counter_normal"><input class="js_loading" name="square_living" type="text" /></div>
                  </td>
                  <td class="middle">
                    <label>Кухня, м<sup>2</sup></label>
                    <div class="counter_normal"><input class="js_loading" name="square_kitchen" type="text" /></div>
                  </td>
                  <td class="middle">
                    <label>Этаж</label>
                    <input class="js_loading" name="floor" type="text" />
                  </td>
                  <td class="middle">
                    <label>из</label>
                    <input class="js_loading" name="floors" type="text" />
                  </td>
                  <td class="right">
                    <label title="Спальные места">Сп. места</label>
                    <input class="js_loading" name="count_sleeps" type="text" />
                  </td>
                </tr>
              </table>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Сан. узел</label>
                    <input class="js_loading" name="wc" type="text" />
                  </td>
                  <td class="middle">
                    <label>Отопление</label>
                    <input class="js_loading" name="heating" type="text" />
                  </td>
                  <td class="right">
                    <label>Горячая вода</label>
                    <input class="js_loading" name="hot_water" type="text" />
                  </td>
                </tr>
              </table>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Окна</label>
                    <input class="js_loading" name="window" type="text" />
                  </td>
                  <td class="middle">
                    <label>Состояние</label>
                    <input class="js_loading" name="state" type="text" />
                  </td>
                  <td class="right">
                    <label>Балкон / лоджия</label>
                    <input class="js_loading" name="count_balcony" type="text" />
                    <input class="js_loading" name="type_balcony" type="text" />
                  </td>
                </tr>
              </table>
            </td>
            <td class="right">
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Мебель</label>
                    <input class="js_loading" name="furniture" type="text" />
                    <label>Мультимедиа</label>
                    <input class="js_loading" name="multimedia" type="text" />
                  </td>
                  <td class="middle">
                    <label>Бытовая техника</label>
                    <input class="js_loading" name="comfort" type="text" />
                  </td>
                  <td class="right">
                    <label>Дополнительно</label>
                    <input class="js_loading" name="additionally" type="text" />
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <table class="column">
          <tr>
            <td class="left">
              <label>Дополнительное описание</label>
              <textarea class="input" name="description"></textarea>
            </td>
            <td class="right">
              <label>Служебные пометки</label>
              <textarea class="input" name="service_mark"></textarea>
            </td>
          </tr>
        </table>
        <table class="column">
          <tr>
            <td class="left">
              <h2>Условия сдачи</h2>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Дата установки цены</label>
                    <input class="js_loading" name="date_price" type="text" />
                  </td>
                  <td class="right">
                    <label>Дата сдачи</label>
                    <input class="js_loading" name="date_rent" type="text" />
                  </td>
                </tr>
              </table>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Цена</label>
                    <table>
                      <tr>
                        <td class="price_counter"><input class="js_loading" name="price" type="text" /></td>
                        <td>руб.</td>
                      </tr>
                    </table>
                  </td>
                  <td class="middle">
                    <label>Залог</label>
                    <table>
                      <tr>
                        <td class="price_counter"><input class="js_loading" name="guaranty" type="text" /></td>
                        <td>руб.</td>
                      </tr>
                    </table>
                  </td>
                  <td class="right">
                    <label>Предоплата</label>
                    <table>
                      <tr>
                        <td class="price_counter"><input class="js_loading" name="prepayment" type="text" /></td>
                        <td id="name_month">месяцев</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <table class="column">
                <tr>
                  <td class="left">
                    <label>Дополнительная оплата</label>
                    <input class="js_loading" name="price_additionally" type="text" />
                  </td>
                  <td class="right">
                    <label>Кого желательно</label>
                    <input class="js_loading" name="for_whom" type="text" />
                  </td>
                </tr>
              </table>
            </td>
            <td class="right">
              <h2>Фотографии<span id="find_photos_loading">Поиск фотографий</span></h2>
              <div id="uploader" class="js_loading"></div>
              <div class="clear"></div>
            </td>
          </tr>
        </table>
        <button class="button" type="submit"><span class="button_caption">Создать</span></button>
      </div>
    </form>

  </main><!-- .content -->

  <?php require_once "footer.php" ?>

</div><!-- .wrapper -->

</body>
</html>