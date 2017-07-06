<?php

class __files
{
  /**
   * Создание имени
   * @return string
   */
  public static function create_name()
  {
    return base_convert(time() . rand(10, 99), 10, 16);
  }

  /**
   * Создание файла с уникальными путями
   * @param $templates - список с шаблонами путей
   * @return array
   */
  public static function create_unique_path($templates)
  {
    $ret = array();
    foreach($templates as $t)
    {
      do
        $path = str_replace("{file_name}", self::create_name(), $t);
      while (file_exists(ROOT . $path));
      $f = fopen(ROOT . $path, "wb");
      fclose($f);
      $ret[] = $path;
    }
    return $ret;
  }

  /**
   * Уменьшение размера изображения
   * @param $src - исходный путь
   * @param $dest - путь куда будет сохрванено изображение
   * @param $width - макс. ширина
   * @param $height - макс. высота
   * @param int $quality - качество
   * @return array - массив с шириной и высотой
   */
  public static function image_copy($src, $dest, $width, $height, $quality = 100)
  {
    $size = getimagesize($src);
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;

    // пропорциональное уменьшение изображения
    $ratio = $width < $size[0] || $height < $size[1] ? min($width / $size[0], $height / $size[1]) : 1;
    $width = floor($size[0] * $ratio);
    $height = floor($size[1] * $ratio);

    // копирование изображения в формат .jpg
    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
    imagefill($idest, 0, 0, imagecolorallocate($idest, 255, 255, 255));
    imagealphablending($idest, true);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    imagedestroy($isrc);

    imagejpeg($idest, $dest, $quality);
    chmod($dest, 0666);
    imagedestroy($idest);

    return array(
      "width" => $width,
      "height" => $height
    );
  }
}