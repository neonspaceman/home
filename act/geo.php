<?php

require_once "../sys/__init.php";

/**
 * Вывод найденных улиц
 */
function street_find()
{
  $db = __database::get_instance();
  $response = __response::get_instance();
  $query = __data::post("query", "s");
  $limit = __data::post("limit", "u");
  $matches = array();
  $stmt = null;
  if (empty($query))
  {
    $q = "select `id`, `name` from `streets` order by `name` limit ?";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("i", $limit);
  }
  else
  {
    $q = "select `id`, `name` from `streets` where `name` like concat('%',?,'%') order by `name` limit ?";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param("si", $query, $limit);
  }
  $stmt->execute() or die($db->error);
  $res = $stmt->get_result();
  while($row = $res->fetch_assoc())
    $matches[] = array("id" => $row["id"], "caption" => $row["name"]);
  $res->close();
  $stmt->close();
  $response->set_value("query", $query);
  $response->set_value("matches", $matches);
}

/**
 * Получение информации с сайта жкх-чита.рф
 * @param $city - город
 * @param $street - улица
 * @param $house - дом
 * @return array - год эвакуации, кол-во этажей, материал стен, изображения
 */
function parse_zhkh_chita($city, $street, $house)
{
  $result = array(
    "find" => false,
    "year" => "",
    "floors" => "",
    "material" => "",
    "image" => array()
  );
  // поиск дома
  $url = "http://xn----8sbqji4csr.xn--p1ai";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url . "/index.php?path=objects/search.php");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, array( "search" => "{$city}, {$street}, {$house}" ));
  $content = curl_exec($ch);
  curl_close($ch);
  // поиск ссылки здания
  $city = "город {$city}";
  if (!preg_match("/^(бульвар|двор|переулок|площадь|проезд|проспект|тракт|тупик|шоссе)/u", $street)) $street = "улица {$street}";
  $house = "д. {$house}";
  if ($res = preg_match_all("/<a href='([\?0-9a-z.=&_]+)' class='linkMenu'>{$city}, {$street}, {$house}<\/a>/iuU", $content, $matches))
  {
    if ($res !== false)
    {
      $result["find"] = true;
      $link = $matches[1][0];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url . "/" . $link . "&page=1");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $content = curl_exec($ch);
      curl_close($ch);
      // достаём информацию о доме
      if (preg_match_all("/<td>(?:Год ввода в эксплуатацию:|Количество этажей:|Материал стен:)<\/td><td[\s]+(?:colspan='2')?[\s]+>([0-9а-яё\-]*)<\/td>/iuU", $content, $matches) !== false)
      {
        $result["year"] = !empty($matches[1][0]) ? (int)($matches[1][0]) : "";
        $result["floors"] = !empty($matches[1][1]) ? (int)($matches[1][1]) : "";
        $result["material"] = !empty($matches[1][2]) ? $matches[1][2] : "";
      }
      // достаём изображения
      if (preg_match_all("/<a class=fancybox rel='gallery' href='([0-9a-z._\/]+)' title='[^']+'>/iuU", $content, $matches) !== false)
      {
        foreach($matches[1] as $img)
        {
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url . $img);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_NOBODY, true);
          curl_setopt($curl, CURLOPT_HEADER, true);
          $headers = curl_exec($curl);
          curl_close($curl);
          if (preg_match("/200 OK/isU", $headers))
          {
            $result["image"][] = $url . $img;
          }
        }
      }
    }
  }
  return $result;
}

/**
 * Получение инфорации с сайта жкх-чита.рф
 */
function get_images()
{
  $db = __database::get_instance();
  $response = __response::get_instance();
  $hash = __data::post("hash", "s");
  $city = __data::post("city", "s");
  $street = __data::post("street", "s");
  $house = __data::post("house", "s");

  $info = parse_zhkh_chita($city, $street, $house);
  $response->set_value("find", $info["find"]);
  $response->set_value("year", $info["year"]);
  $response->set_value("floors", $info["floors"]);
  $response->set_value("material", $info["material"]);
  $images = array();
  // копирование картинок и добавление их в базу
  foreach ($info["image"] as $image)
  {
    list($path_full, $path_thumb) = __files::create_unique_path(array(UPLOAD_DIR . "{file_name}.jpg", UPLOAD_DIR . "{file_name}.jpg"));
    copy($image, ROOT . $path_full);
    if(($info = getimagesize(ROOT . $path_full)) === false)
      continue;
    $info_full = __files::image_copy(ROOT . $path_full, ROOT . $path_full, MAX_FULLSIZE_WIDTH, MAX_FULLSIZE_HEIGHT, 75);
    $info_thumb = __files::image_copy(ROOT . $path_full, ROOT . $path_thumb, MAX_THUMB_WIDTH, MAX_THUMB_HEIGHT, 90);
    $q = "insert into `images` (`fullsize`, `fw`, `fh`, `thumb`, `tw`, `th`, `hash`) values (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($q) or die($db->error);
    $stmt->bind_param(
      "siisiis",
      $path_full, $info_full["width"], $info_full["height"],
      $path_thumb, $info_thumb["width"], $info_thumb["height"],
      $hash
    );
    $stmt->execute() or die($db->error);
    if ($stmt->insert_id)
    {
      $images[] = array(
        "id" => $stmt->insert_id,
        "thumb" => $path_thumb
      );
    }
    $stmt->close();
  }
  $response->set_value("images", $images);
}

$core = __core::get_instance();
$core->open();

$response = __response::get_instance();

switch(__data::get("act"))
{
case "street_find":
  street_find();
  break;
case "get_images":
  get_images();
  break;
}

$response->send();
$core->close();