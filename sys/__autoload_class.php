<?php

function __autoload_class($class_name)
{
  if (mb_substr($class_name, 0, 2) == "__")
  {
    $path = ROOT . "/sys/" . $class_name . ".php";
    require_once $path;
    return true;
  }
  return false;
}