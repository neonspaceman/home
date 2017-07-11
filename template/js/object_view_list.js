"use strict";

/**
 * Фильтр
 * @param name
 * @param inputName
 * @param type
 * @param data
 * @param value
 * @constructor
 */
var Filter = function (name, inputName, type, data, value){
  this.ui = null;
  this.name = name;
  this.inputName = inputName;
  this.type = type;
  this.$wrap = $("<div class='filter_item'>" +
    "<div class='filter_title'>" + name + "</div>" +
    "<div class='filter_content'><input type='hidden' name='" + inputName + "' /></div>" +
    "</div>");
  switch (this.type) {
    case "Select":
      this.ui = new CheckboxGroup(this.$wrap.find("input"), data, {
        selectedItems: value
      });
      break;
    case "Counter":
      this.ui = new Range(this.$wrap.find("input"), {
        minValue: data[0],
        maxValue: data[1],
        step: data[2],
        fromValue: value !== false ? value[0] : false,
        toValue: value !== false ? value[1] : false
      });
      break;
  }
};
Filter.prototype.getValue = function(){
  var value = false;
  switch(this.type){
    case "Select":
      var tmp = "";
      this.ui.getValue().forEach(function(val){
        if (tmp)
          tmp += ",";
        tmp += val;
      });
      if (tmp)
        value = tmp;
      break;
    case "Counter":
      var tmp = this.ui.getValue();
      if (tmp[0] !== this.ui.opts.minValue || tmp[1] !== this.ui.opts.maxValue)
        value = tmp[0] + "-" + tmp[1];
      break;
  }
  return value;
};

/**
 * Коллекция фильтров
 * @param $target
 * @constructor
 */
var FilterCollection = function ($target){
  this.$wrap = $("<form class='filter'>" +
    "<div class='filter_list'></div>" +
    "<div class='filter_button'><button class='button'>Подобрать</button></div>" +
    "</form>");
  this.$filters = this.$wrap.find(".filter_list");
  this.$button = this.$wrap.find(".filter_button button");
  this.filters = [];
  this.$wrap.on("submit", this, function (event){
    event.data.find();
    event.preventDefault();
  });
  $target.replaceWith(this.$wrap);
};
/**
 * Добавление кнопки очистки фильтра
 */
FilterCollection.prototype.addClearButton = function(){
  if (!this.$clearButton){
    this.$clearButton = $("<button class='button red filter_clear' type='button'>Очистить поиск</button>");
    this.$clearButton.on("click", function(){
      console.log("1");
      Location.remove("offset");
      Location.remove("filter");
      Location.reload();
    });
    this.$wrap.find(".filter_button").append(this.$clearButton);
  }
};
/**
 * Добавить новый фильтр
 * @param name - наименование фильтра
 * @param inputName - имя
 * @param type - тип фильтра
 * @param data - данные
 * @param value - данные пришедшие от пользователя
 */
FilterCollection.prototype.add = function (name, inputName, type, data, value){
  var filter = new Filter(name, inputName, type, data, value);
  this.filters.push(filter);
  this.$filters.append(filter.$wrap);
  if (value)
    this.addClearButton();
};
/**
 * Поиск
 */
FilterCollection.prototype.find = function (){
  var query = "";
  this.filters.forEach(function (filter){
    var val = filter.getValue();
    if (val !== false) {
      if (query)
        query += ";";
      query += filter.inputName + ":" + val;
    }
  });
  if (query)
    Location.add("filter", query);
  else
    Location.remove("filter");
  Location.remove("offset");
  Location.reload();
};

/**
 * Вывод опций сортировки
 * @param $target
 * @param data - [id, name]
 * @param options
 * @constructor
 */
var Order = function($target, data, options){
  this.opts = $.extend({
    by: "time"
  }, options);
  this.$wrap = $("<ul class='order_by'>");

  data.forEach(function(val){
    var $li = $("<li><a>" + val[1] + "</a></li>");
    if (val[0] === this.opts.by)
      $li.addClass("active");
    $li.find("a").on("click", this, function(event){
      Location.add("order_by", val[0]);
      Location.reload();
    });
    this.$wrap.append($li);
  }.bind(this));

  $target.replaceWith(this.$wrap);
};

$(function(){
  new Order($("#order_by"), params.orders, { by: params.orderBy });
  new Pagination($("#pagination"), {
    offset: params.offset,
    onPage: params.recordsOnPage,
    count: params.countRecords
  });
  var filterCollection = new FilterCollection($("#filter"));
  filterCollection.add("Район", "id_region", "Select", params.region.data, params.region.select);
  if (params.floor)
    filterCollection.add("Этаж", "floor", "Counter", params.floor.data, params.floor.select);
  if (params.floors)
    filterCollection.add("Этажность", "floors", "Counter", params.floors.data, params.floors.select);
  filterCollection.add("Кол-во комнат", "count_rooms", "Select", params.countRooms.data, params.countRooms.select);
  filterCollection.add("Площадь", "square_general", "Counter", params.squareGeneral.data, params.squareGeneral.select);
  filterCollection.add("Цена", "price", "Counter", params.price.data, params.price.select);
});