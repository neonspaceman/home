<?php

const FLAT_ORDERS = array(
  array("time", "по дате добавления"),
  array("price", "по цене")
);
const FLAT_SOURCE = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "find-home.ru"),
  array("id" => 2, "caption" => "Авито"),
  array("id" => 3, "caption" => "БарахлаНет (barahla.net)"),
  array("id" => 4, "caption" => "ВКонтакте (vk.com)"),
  array("id" => 5, "caption" => "Вся Чита (allchita.ru)"),
  array("id" => 6, "caption" => "Дорус (dorus.ru)"),
  array("id" => 7, "caption" => "Забмедия (vsechita.ru)"),
  array("id" => 8, "caption" => "Из рук в руки (ч/об)"),
  array("id" => 9, "caption" => "Интернет"),
  array("id" => 10, "caption" => "Клиент"),
  array("id" => 11, "caption" => "Листовки на улице"),
  array("id" => 12, "caption" => "Найди дом (naydidom.com)"),
  array("id" => 13, "caption" => "Найду дом (naidudom.ru)"),
  array("id" => 14, "caption" => "Одноклассники (ok.ru)"),
  array("id" => 15, "caption" => "Повторное обращение"),
  array("id" => 16, "caption" => "Прозвон газет"),
  array("id" => 17, "caption" => "РАУИ (raui.ru)"),
  array("id" => 18, "caption" => "Синдом (sindom.ru)"),
  array("id" => 19, "caption" => "Через знакомых"),
  array("id" => 20, "caption" => "ЧитаРу (chita.ru)"),
  array("id" => 21, "caption" => "Щитовая реклама")
);
const FLAT_EXCLUSIVE = array("id" => 1, "caption" => "Эксклюзив");
const FLAT_QUICKLY = array("id" => 1, "caption" => "Срочно");
const FLAT_COUNT_ROOMS = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => -1, "caption" => "Студия"),
  array("id" => 1, "caption" => "1 комната"),
  array("id" => 2, "caption" => "2 комнаты"),
  array("id" => 3, "caption" => "3 комнаты"),
  array("id" => 4, "caption" => "4 комнаты")
);
const FLAT_RELATIVE_ROOMS = array("id" => 1, "caption" => "Смежные команты");
const FLAT_STATE = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Отличное"),
  array("id" => 2, "caption" => "Хорошое"),
  array("id" => 3, "caption" => "Жилое"),
  array("id" => 4, "caption" => "Требует ремонта")
);
const FLAT_HEATING = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Нет"),
  array("id" => 2, "caption" => "Местная котельная / ТЭЦ"),
  array("id" => 3, "caption" => "Электро"),
  array("id" => 4, "caption" => "Печное")
);
const FLAT_HOT_WATER = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Нет"),
  array("id" => 2, "caption" => "Местная котельная / ТЭЦ"),
  array("id" => 3, "caption" => "Бойлер")
);
const FLAT_WC = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Раздельный"),
  array("id" => 2, "caption" => "Совмещённый")
);
const FLAT_WINDOW = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Пластик"),
  array("id" => 2, "caption" => "Дерево")
);
const FLAT_TYPE_BALCONY = array(
  array("id" => 0, "caption" => "Не выбрано"),
  array("id" => 1, "caption" => "Пластик"),
  array("id" => 2, "caption" => "Дерево"),
  array("id" => 3, "caption" => "Аллюминий"),
  array("id" => 4, "caption" => "Не застеклён")
);
const FLAT_FURNITURE = array(
  array("id" => 1, "caption" => "Шкаф / комод", "class" => "cabinet"),
  array("id" => 2, "caption" => "Стол / стул", "class" => "table"),
  array("id" => 4, "caption" => "Кух. гарнитур", "class" => "kitchen"),
  array("id" => 8, "caption" => "Cпальные места", "class" => "bed")
);
const FLAT_MULTIMEDIA = array(
  array("id" => 1, "caption" => "Телевизор", "class" => "tv"),
  array("id" => 2, "caption" => "Кабельное / цифровое ТВ", "class" => "cable"),
  array("id" => 4, "caption" => "Интернет", "class" => "internet"),
  array("id" => 8, "caption" => "Wi-Fi", "class" => "wifi"),
);
const FLAT_COMFORT = array(
  array("id" => 1, "caption" => "Холодильник", "class" => "fridge"),
  array("id" => 2, "caption" => "Плита", "class" => "cooker"),
  array("id" => 4, "caption" => "Стиральная машина", "class" => "washer"),
  array("id" => 8, "caption" => "Микроволновка", "class" => "microwave"),
  array("id" => 16, "caption" => "Пылесос", "class" => "vacuum_cleaner"),
  array("id" => 32, "caption" => "Фен", "class" => "hair_dryer"),
  array("id" => 64, "caption" => "Утюг", "class" => "iron"),
  array("id" => 128, "caption" => "Кондиционер", "class" => "air_con")
);
const FLAT_ADDITIONALLY = array(
  array("id" => 1, "caption" => "Ул. планировка"),
  array("id" => 2, "caption" => "Закрытый двор"),
  array("id" => 4, "caption" => "Можно с питомцами"),
  array("id" => 8, "caption" => "Можно с детьми"),
  array("id" => 16, "caption" => "Можно для мероприятий"),
  array("id" => 32, "caption" => "Можно курить")
);
const FLAT_FOR_WHOM = array(
  array("id" => 1, "caption" => "Молодой человек"),
  array("id" => 2, "caption" => "Девушка"),
  array("id" => 4, "caption" => "Мужчина"),
  array("id" => 8, "caption" => "Женщина"),
  array("id" => 16, "caption" => "Семья (пара)"),
  array("id" => 32, "caption" => "Только русские"),
  array("id" => 64, "caption" => "Студентам"),
  array("id" => 128, "caption" => "Студенткам"),
  array("id" => 256, "caption" => "Приезжие"),
  array("id" => 512, "caption" => "Организация")
);
const FLAT_PRICE_ADDITIONALLY = array(
  array("id" => 1, "caption" => "Газ"),
  array("id" => 2, "caption" => "Свет"),
  array("id" => 4, "caption" => "Вода"),
  array("id" => 8, "caption" => "Тепло"),
  array("id" => 16, "caption" => "Интернет"),
  array("id" => 32, "caption" => "Кабельное / цифровое ТВ")
);