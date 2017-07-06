<?php

define("ROOT", $_SERVER["DOCUMENT_ROOT"]);

define("UPLOAD_DIR", "/upload/");
define("UPLOAD_IMG_MAX_WIDTH", 4000);
define("UPLOAD_IMG_MAX_HEIGHT", 4000);
define("UPLOAD_IMG_MAX_SIZE", 2);
define("MAX_FULLSIZE_WIDTH", 1280);
define("MAX_FULLSIZE_HEIGHT", 1280);
define("MAX_THUMB_WIDTH", 100);
define("MAX_THUMB_HEIGHT", 100);

if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1")
{
  define("DB_HOST", "localhost");
  define("DB_USER", "root");
  define("DB_PASSWORD", "");
  define("DB_BASE", "real_estate");
}
else
{
  define("DB_HOST", "localhost");
  define("DB_USER", "ct41445_dom");
  define("DB_PASSWORD", "anito");
  define("DB_BASE", "ct41445_dom");
}

define("RECORDS_ON_PAGE", 50);
define("DEFAULT_TITLE", "Информационный центр \"ДОМ\"");
define("DEFAULT_KEYWORDS", false);
define("DEFAULT_DESCRIPTION", false);
define("DEFAULT_ICON_URL", "/template/favicon.ico");
define("DEFAULT_ICON_VER", time());