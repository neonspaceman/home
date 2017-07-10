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
  new Select($("input[name='source']"), params.source);
  new Checkbox($("input[name='exclusive']"), params.exclusive);
  new Checkbox($("input[name='quickly']"), params.quickly);
  this.landlord = new Landlord($("#landlord"));
  $("input[name='city']").on("change", function(){ Form.setCity($.trim($(this).val())); });
  new Select($("input[name='region']"), params.region, { extension: true });
  new Autocomplete($("input[name='street']"), {
    url: "/act/geo.php?act=street_find",
    onChoose: function(selected){ Form.setStreet(selected["caption"]); }
  });
  $("input[name='house']").on("change", function(){
    Form.setHouse($.trim($(this).val()));
  });
  new Select($("input[name='count_rooms']"), params.countRooms, {
    onChoose: function(index) {
      relatedRooms.setDisabled(index > 2);
    }
  });
  var relatedRooms = new Checkbox($("input[name='related_rooms']"), params.relatedRooms, {
    disabled: false
  });
  new Counter($("input[name='square_general']"), { minValue: 0, isFloat: true });
  new Counter($("input[name='square_living']"), { minValue: 0, isFloat: true });
  new Counter($("input[name='square_kitchen']"), { minValue: 0, isFloat: true });
  new CheckboxGroup($("input[name='furniture']"), params.furniture);
  new Counter($("input[name='floor']"), { minValue: 0 });
  new Counter($("input[name='floors']"), { minValue: 0 });
  new Counter($("input[name='count_sleeps']"), { minValue: 0 });
  new CheckboxGroup($("input[name='multimedia']"), params.multimedia);
  new CheckboxGroup($("input[name='comfort']"), params.comfort);
  new CheckboxGroup($("input[name='additionally']"), params.additionally);
  new Select($("input[name='wc']"), params.wc);
  new Select($("input[name='heating']"), params.heating);
  new Select($("input[name='hot_water']"), params.hotWater);
  new Select($("input[name='window']"), params.window);
  new Select($("input[name='state']"), params.state);
  new Counter($("input[name='count_balcony']"), {
    minValue: 0,
    onChange: function(value){ typeBalcony.setDisabled(value < 1); }
  });
  var typeBalcony = new Select($("input[name='type_balcony']"), params.typeBalcony, { disabled: true });
  new Datepicker($("input[name='date_price']"));
  new Datepicker($("input[name='date_rent']"));
  new Counter($("input[name='price']"), { minValue: 0 });
  new Counter($("input[name='guaranty']"), { minValue: 0 });
  new Counter($("input[name='prepayment']"), {
    minValue: 0,
    onChange: function(value){
      value = parseInt(value);
      $("#name_month").text(Common.getNumEnding(value, ["месяц", "месяца", "месяцев"]));
    }
  });
  new CheckboxGroup($("input[name='price_additionally']"), params.priceAdditionally);
  new CheckboxGroup($("input[name='for_whom']"), params.forWhom);
  this.map = new Map($("#flat_map"));
  this.uploader = new Uploader($("#uploader"));
  this.findPhotos = new FindPhotos(this.uploader);
  $("#form_flat_add").ajaxForm({
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
        location.href = "/flat/view?id=" + data.id;
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
              new MessageBox({ message: "При добавлении объекта произошла ошибка, обновите страницу и повторите попытку." });
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
      new MessageBox({ message: "При добавлении объекта произошла ошибка, обновите страницу и повторите попытку." });
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