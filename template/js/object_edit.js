"use strict";

/**
 * Форма добавления объектов
 */
var Form = {
  city: "Чита",
  street: "",
  house: ""
};
/**
 * Инициализация
 */
Form.init = function(){
  this.street = params.streetName;
  this.house = params.house;
  (new Select($("input[name='source']"), params.source[0])).selectById(params.source[1]);
  new Checkbox($("input[name='exclusive']"), params.exclusive[0], { checked: params.exclusive[1] });
  new Checkbox($("input[name='quickly']"), params.quickly[0], { checked: params.quickly[1] });
  this.landlord = new Landlord($("#landlord"));
  this.landlord.load(params.id);
  $("input[name='city']").on("change", function(){ Form.setCity($.trim($(this).val())); });
  (new Select($("input[name='region']"), params.region[0], { extension: true })).selectById(params.region[1]);
  (new Autocomplete($("input[name='street']"), {
    url: "/act/geo.php?act=street_find",
    onChoose: function(selected){ Form.setStreet(selected[1]); }
  })).selectById("/act/geo.php?act=street_find_by_id", params.streetId);
  $("input[name='house']").val(params.house).on("change", function(){
    Form.setHouse($.trim($(this).val()));
  });
  $("input[name='flat']").val(params.flat);
  $("textarea[name='guide']").val(params.guide);
  (new Select($("input[name='count_rooms']"), params.countRooms[0], {
    onChoose: function(index) { relatedRooms.setDisabled(index < 3); }
  })).selectById(params.countRooms[1]);
  var relatedRooms = new Checkbox($("input[name='related_rooms']"), params.relatedRooms[0], {
    checked: params.relatedRooms[1],
    disabled: params.countRooms[1] <= 1
  });
  new Counter($("input[name='square_general']"), { value: params.squareGeneral, minValue: 0, isFloat: true });
  new Counter($("input[name='square_living']"), { value: params.squareLiving, minValue: 0, isFloat: true });
  new Counter($("input[name='square_kitchen']"), { value: params.squareKitchen, minValue: 0, isFloat: true });
  (new CheckboxGroup($("input[name='furniture']"), params.furniture[0])).setCheckedByMask(params.furniture[1]);
  new Counter($("input[name='floor']"), { value: params.floor, minValue: 0 });
  new Counter($("input[name='floors']"), { value: params.floors, minValue: 0 });
  new Counter($("input[name='count_sleeps']"), { value: params.countSleeps, minValue: 0 });
  (new CheckboxGroup($("input[name='multimedia']"), params.multimedia[0])).setCheckedByMask(params.multimedia[1]);
  (new CheckboxGroup($("input[name='comfort']"), params.comfort[0])).setCheckedByMask(params.comfort[1]);
  (new CheckboxGroup($("input[name='additionally']"), params.additionally[0])).setCheckedByMask(params.additionally[1]);
  (new Select($("input[name='wc']"), params.wc[0])).selectById(params.wc[1]);
  (new Select($("input[name='heating']"), params.heating[0])).selectById(params.heating[1]);
  (new Select($("input[name='hot_water']"), params.hotWater[0])).selectById(params.hotWater[1]);
  (new Select($("input[name='window']"), params.window[0])).selectById(params.window[1]);
  (new Select($("input[name='state']"), params.state[0])).selectById(params.state[1]);
  new Counter($("input[name='count_balcony']"), {
    value: params.countBalcony,
    minValue: 0,
    onChange: function(value){ typeBalcony.setDisabled(value < 1); }
  });
  var typeBalcony = new Select($("input[name='type_balcony']"), params.typeBalcony[0], { disabled: params.countBalcony < 1 });
  typeBalcony.selectById(params.typeBalcony[1]);
  $("textarea[name='description']").val(params.description);
  $("textarea[name='service_mark']").val(params.serviceMark);
  (new Datepicker($("input[name='date_price']"))).selectByDate(params.datePrice);
  (new Datepicker($("input[name='date_rent']"))).selectByDate(params.dateRent);
  new Counter($("input[name='price']"), { value: params.price, minValue: 0 });
  new Counter($("input[name='guaranty']"), { value: params.guaranty, minValue: 0 });
  new Counter($("input[name='prepayment']"), {
    value: params.prepayment,
    minValue: 0,
    onChange: function(value){
      value = parseInt(value);
      $("#name_month").text(Common.getNumEnding(value, ["месяц", "месяца", "месяцев"]));
    }
  });
  (new CheckboxGroup($("input[name='price_additionally']"), params.priceAdditionally[0])).setCheckedByMask(params.priceAdditionally[1]);
  (new CheckboxGroup($("input[name='for_whom']"), params.forWhom[0])).setCheckedByMask(params.forWhom[1]);
  this.map = new Map($("#flat_map"), { coords: params.coords, zoom: params.zoom });
  this.uploader = new Uploader($("#uploader"));
  this.uploader.load(params.id);
  this.findPhotos = new FindPhotos(this.uploader);
  $("#form_flat_edit").ajaxForm({
    dataType: "json",
    beforeSubmit: function(arr, $form, options){
      var error = "";
      if (this.findPhotos.isBusy())
        error += "<p>Дождитесь завершения поиска фотографий.</p>";
      if (this.map.isBusy())
        error += "<p>Дождитесь определения местоположения здания.</p>";
      if (this.landlord.isBusy())
        error += "<p>Дождитесь загрузки номера телефона.</p>";
      if (this.uploader.isBusy())
        error += "<p>Дождитесь загрузки изображений.</p>";
      if (error !== ""){
        new MessageBox({ message: error });
        return false;
      } else {
        Common.setButtonLoading($form.find("[type='submit']"), true);
      }
    }.bind(this),
    success: function(data, status, xhr, $form){
      if (data.status === "success") {
        new MessageBox({ message: "Объект сохранён." });
      } else {
        var wrap_message = "";
        data.message.every(function(message){
          switch(message) {
            case "landlord is empty":
              wrap_message += "<p>Поле \"Арендодатель\" обязательное.</p>";
              break;
            case "landlord's name is empty":
              wrap_message += "<p>Поле \"ФИО\" обязательное.</p>";
              break;
            case "region is empty":
              wrap_message += "<p>Поле \"Регион\" обязательное.</p>";
              break;
            case "street is empty":
              wrap_message += "<p>Поле \"Улица\" обязательное.</p>";
              break;
            case "house is empty":
              wrap_message += "<p>Поле \"Номер дома\" обязательное.</p>";
              break;
            case "price is empty":
              wrap_message += "<p>Поле \"Цена\" обязательное.</p>";
              break;
            default:
              wrap_message = "";
              new MessageBox({ message: "При сохранении объекта произошла ошибка, обновите страницу и повторите попытку." });
              return false;
          }
          return true;
        });
        if (wrap_message){
          new MessageBox({ message: wrap_message });
        }
        Common.setButtonLoading($form.find("[type='submit']"), false);
      }
    },
    error: function(){
      new MessageBox({ message: "При сохранении объекта произошла ошибка, обновите страницу и повторите попытку." });
    }
  });
};
/**
 * Изменение города
 * @param city
 */
Form.setCity = function(city){
  if (this.city !== city) {
    this.city = city;
    this.updateGeoAddress();
  }
};
/**
 * Изменение уицы
 * @param street
 */
Form.setStreet = function(street){
  if (this.street !== street) {
    this.street = street;
    this.updateGeoAddress();
  }
};
/**
 * Изменения дома
 * @param house
 */
Form.setHouse = function(house){
  if (this.house !== house) {
    this.house = house;
    this.updateGeoAddress();
  }
};
/**
 * Обновление адреса + поиск на карте + загрузка изображений
 */
Form.updateGeoAddress = function(){
  var address = "",
      zoom = 12;
  if (this.city){
    address += this.city;
    if (this.street){
      zoom = 14;
      address += " " + this.street;
      if (this.house){
        zoom = 16;
        address += " " + this.house;
        this.findPhotos.find(this.city, this.street, this.house);
      }
    }
  }
  if (zoom !== 16)
    this.findPhotos.abort();
  this.map.geocode(address, zoom);
};

$(function(){
  Form.init();
});