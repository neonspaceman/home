"use strict";

/**
 * Множества
 * @constructor
 */
var Set = function (){
  this.set = {};
};
/**
 * Принадлежит множеству
 * @param key
 * @returns {boolean}
 */
Set.prototype.has = function (key){
  return typeof this.set[key] !== 'undefined';
};
/**
 * Добавить в множество
 * @param key
 */
Set.prototype.add = function (key){
  this.set[key] = 1;
};
/**
 * Добавить в множетво массив
 * @param array
 */
Set.prototype.addArray = function (array){
  array.forEach(function (value){
    this.add(value);
  }.bind(this));
};
/**
 * Удалить из множества
 * @param key
 */
Set.prototype.remove = function (key){
  delete this.set[key];
};
/**
 * Удалить из множества массив
 * @param array
 */
Set.prototype.removeArray = function (array){
  array.forEach(function (value){
    this.remove(value);
  }.bind(this));
};
/**
 * Очистка
 */
Set.prototype.clear = function (){
  this.set = {};
};

/**
 * Работа с историей
 */
var Location = {
  params: {}
};
Location.init = function (){
  var query = decodeURIComponent(window.location.search.substr(1)),
    arr = query.split("&");
  arr.forEach(function (param){
    if (param.length !== 0) {
      param = param.split("=");
      this.params[param[0]] = typeof(param[1]) === "undefined" ? "" : param[1];
    }
  }.bind(this));
};
/**
 * Получить все параметры из строки
 * @returns {{}}
 */
Location.get = function (){
  return this.params;
};
/**
 * Получить параметр по имени
 * @param name
 * @returns {*}
 */
Location.getByName = function (name){
  return this.params[name];
};
/**
 * Устаноить параметры
 * @param params
 */
Location.set = function (params){
  this.params = params;
};
/**
 * Добавить параметр
 * @param key
 * @param val
 */
Location.add = function (key, val){
  this.params[key] = typeof(val) === "undefined" ? "" : val;
};
/**
 * Удалить параметр
 * @param key
 */
Location.remove = function (key){
  delete this.params[key];
};
/**
 * Очистить
 */
Location.clear = function (){
  this.set({});
};
/**
 * Обновить страницу
 */
Location.reload = function (){
  var query = "";
  $.each(this.params, function (name, val){
    if (query)
      query += "&";
    query += ((val + "").length > 0 ? name + "=" + val : name);
  });
  location.href = location.pathname + (query ? "?" + query : "");
};

(function (){
  Math.clamp = function (value, min, max){
    if (min !== false && value < min)
      return min;
    if (max !== false && value > max)
      return max;
    return value;
  };
  Math.sign = function (value){
    if (value == 0)
      return 0;
    if (value < 0)
      return -1;
    if (value > 0)
      return 1;
  };
  Date.prototype.getMonthName = function (c){
    switch (c) {
      case "nom":
        return [
          "Январь", "Февраль", "Март", "Апрель",
          "Май", "Июнь", "Июль", "Август",
          "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
        ][this.getMonth()];
      case "gen":
        return [
          "Января", "Февраля", "Марта", "Апреля",
          "Мая", "Июня", "Июля", "Августа",
          "Сентября", "Октября", "Ноября", "Декабря"
        ][this.getMonth()];
    }
  };
  Date.prototype.getFullMinutes = function (){
    var min = this.getMinutes();
    if (min < 10)
      min = "0" + min;
    return min;
  };
})();

/**
 * Общие
 */
var Common = {};
/**
 * Закгрузка для кнопки
 */
Common.setButtonLoading = function($button, loading){
  if (loading){
    $button.attr("disabled", "true").addClass("loading");
  } else {
    $button.removeAttr("disabled").removeClass("loading");
  }
};
/**
 * Вывод заголовка, если текст не помещается в элемент
 * @param el
 * @param title
 */
Common.setTitle = function (el, title){
  if (el[0].scrollWidth > el.outerWidth())
    el.attr('title', title || el.text());
};
/**
 * Склонение по числам
 * @param num
 * @param end
 * @returns {*}
 */
Common.getNumEnding = function (num, end){
  var ret = end[2];
  num %= 100;
  if (!(num >= 11 && num <= 19)) {
    var i = num % 10;
    switch (i) {
      case 1:
        ret = end[0];
        break;
      case 2:
      case 3:
      case 4:
        ret = end[1];
        break;
    }
  }
  return ret;
};
/**
 * Получить выделение
 * @param el
 * @returns {*}
 */
Common.getSelection = function (el){
  var inputBox = el[0];
  if ("selectionStart" in inputBox) { //gecko
    return {
      start: inputBox.selectionStart,
      end: inputBox.selectionEnd
    }
  }
  //and now, the blinkered IE way
  var bookmark = document.selection.createRange().getBookmark();
  var selection = inputBox.createTextRange();
  selection.moveToBookmark(bookmark);
  var before = inputBox.createTextRange();
  before.collapse(true);
  before.setEndPoint("EndToStart", selection);
  var beforeLength = before.text.length;
  var selLength = selection.text.length;
  return {
    start: beforeLength,
    end: beforeLength + selLength
  }
};
/**
 * Установить выделение
 * @param el
 * @param range
 */
Common.setSelection = function (el, range){
  var inputBox = el[0];
  if (range.start > range.end)
    range.start = range.end;
  if ("selectionStart" in inputBox) { //gecko
    inputBox.setSelectionRange(range.start, range.end);
  } else {
    var r = inputBox.createTextRange();
    r.collapse(true);
    r.moveStart('character', range.start);
    r.moveEnd('character', range.end - range.start);
    r.select();
  }
};

/**
 * Элемент <select>
 * @param $target
 * @param data
 * @param options
 * @constructor
 */
var Select = function ($target, data, options){
  this.data = data;
  this.opts = $.extend({
    open: false,
    extension: false,
    selected: 0,
    subSelected: -1,
    disabled: false,
    onChoose: null
  }, options);

  this.$wrap = $("<div class='dropdown'>" +
    "<div class='caret'><i class='fa fa-caret-down'></i></div>" +
    "<div class='input caption'></div>" +
    "<input name='" + $target.attr("name") + "' type='hidden' />" +
    "</div>");
  if (this.opts.extension)
    this.$wrap.append("<input name='sub" + $target.attr("name") + "' type='hidden' />");
  this.$wrap.on("click", this, function (event){
    event.data.open();
    event.stopPropagation();
  });
  $target.replaceWith(this.$wrap);

  this.$values = $("<div class='dropdown_wrap select'>");
  this.data.forEach(function (val, i){
    var $link = $("<a class='value'>" + val["caption"] + "</a>");
    $link.on("click", {i: i, self: this}, function (event){
      event.data.self.choose(event.data.i, -1);
      event.data.self.close();
      if (event.data.self.opts.onChoose)
        event.data.self.opts.onChoose(event.data.i, -1);
    });
    this.$values.append($link);

    if (this.opts.extension && val["sub"].length > 0) {
      var $toggle = $("<a class='toggle'>показать</a>");
      $toggle.on("click", {parent: i, self: this}, function (event){
        var self = event.data.self,
          parent = event.data.parent;
        self.$values.find("[data-parent]").hide();
        if ($(this).data("opened")) {
          $(this).text("показать").data("opened", false);
        } else {
          self.$values.find(".toggle").text("показать").data("opened", false);
          self.$values.find("[data-parent='" + parent + "']").show();
          $(this).text("скрыть").data("opened", true);
        }
        self.updateValuesUI();
        event.stopPropagation();
      });
      $link.append($toggle);

      val["sub"].forEach(function (subVal, subI){
        var $subLink = $("<a class='value' data-parent='" + i + "'>&mdash;&nbsp;" + subVal["caption"] + "</a>");
        $subLink.on("click", {parent: i, child: subI, self: this}, function (event){
          event.data.self.choose(event.data.parent, event.data.child);
          event.data.self.close();
          if (event.data.self.opts.onChoose)
            event.data.self.opts.onChoose(event.data.parent, event.data.child);
        });
        $subLink.hide();
        this.$values.append($subLink);
      }.bind(this));
    }
  }.bind(this));
  this.$values.on("click", function (event){
    event.stopPropagation();
  });
  $("body").append(this.$values);

  this.choose(this.opts.selected, this.opts.subSelected);
  this.setDisabled(this.opts.disabled);
};
/**
 * Открыть элемент
 */
Select.prototype.open = function (){
  var open = this.opts.open;
  $(document).trigger("click");
  if (!open && !this.opts.disabled) {
    this.opts.open = true;
    this.updateValuesUI();
    $(document).on("click", this, function (event){
      event.data.close();
    });
  }
};
/**
 * Обновление расположения values
 */
Select.prototype.updateValuesUI = function (){
  this.$values.css("width", this.$wrap.outerWidth());
  this.$values.css("height", "auto");
  var openType = "open_bottom";
  var coords = {
    width: this.$wrap.outerWidth(),
    height: this.$values.outerHeight(),
    left: this.$wrap.offset().left,
    top: this.$wrap.offset().top + this.$wrap.outerHeight()
  };
  this.$wrap.removeClass("open_top open_bottom");
  this.$values.removeClass("open_top open_bottom");
  if (coords.top + coords.height > $(document).height() &&
    this.$wrap.offset().top - coords.height >= 0) {
    openType = "open_top";
    coords.top = this.$wrap.offset().top - coords.height;
  }
  this.$wrap.addClass(openType);
  this.$values.css(coords).addClass(openType);
};
/**
 * Закрыть элемент
 */
Select.prototype.close = function (){
  this.opts.open = false;
  this.$wrap.removeClass("open_top open_bottom");
  this.$values.removeClass("open_top open_bottom");
  $(document).off("click");
};
/**
 * Выбор элемента
 * @param index - порядковый номер
 * @param subIndex - порядковый номер дочерного значения
 */
Select.prototype.choose = function (index, subIndex){
  this.opts.selected = index;
  var id = this.data[index]["id"],
      caption = this.data[index]["caption"];
  if (this.opts.extension) {
    this.opts.subSelected = subIndex;
    var subId = -1;
    if (subIndex !== -1) {
      subId = this.data[index]["sub"][subIndex]["id"];
      caption += ", " + this.data[index]["sub"][subIndex]["caption"];
    }
    this.$wrap.find("input[type='hidden']").eq(1).val(subId);
  }
  this.$wrap.find(".caption").text(caption);
  this.$wrap.find("input[type='hidden']").eq(0).val(id);
};
/**
 * Выбрать по id
 */
Select.prototype.selectById = function(id){
  this.opts.selected = 0;
  this.opts.subSelected = -1;
  this.data.every(function(item, i){
    if (item.id === id){
      this.opts.selected = i;
      return false;
    }
    if (this.opts.extension) {
      return item.sub.every(function(subItem, subI){
        if (subItem.id === id){
          this.opts.selected = i;
          this.opts.subSelected = subI;
          return false;
        }
        return true;
      }.bind(this));
    }
    return true;
  }.bind(this));
  this.choose(this.opts.selected, this.opts.subSelected);
};
/**
 * Установка активности элемента
 * @param value
 */
Select.prototype.setDisabled = function (value){
  this.opts.disabled = !!value;
  if (this.opts.disabled) {
    this.choose(0);
    this.$wrap.addClass("disabled");
  } else {
    this.$wrap.removeClass("disabled");
  }
};

/**
 * Элемент <autocomplete>
 * @param $target
 * @param options
 * @constructor
 */
var Autocomplete = function ($target, options){
  this.data = [];
  this.xhr = null;
  // значение инпута
  this.value = "";
  // индекс выбранного элемента
  this.selected = false;
  this.opts = $.extend({
    limit: 10,
    url: null,
    open: false,
    defaultHint: "Начните ввод",
    emptyHint: "Нет совпадений",
    onChoose: null
  }, options);
  this.$wrap = $("<div class='dropdown autocomplete'>" +
    "<input class='input caption' type='text' autocomplete='off' placeholder='Выбрать..' />" +
    "<div class='caret'><i class='fa fa-caret-down'></i></div>" +
    "<div class='loading'></div>" +
    "<input name='" + $target.attr("name") + "' type='hidden' />" +
    "</div>");
  this.$wrap.on("click", this, function (event){
    event.data.$input.trigger("focus");
    event.stopPropagation();
  });
  this.$loading = this.$wrap.find(".loading");
  // this.$id = this.$wrap.find("input[type='hidden']");
  this.$input = this.$wrap.find(".caption");
  this.$input.on("focus", this, function (event){
    event.data.open();
  });
  this.$input.on("blur", this, function (event){
    event.data.close();
  });
  this.$input.on("input", this, function (event){
    event.data.updateSelectValues();
  });
  $target.replaceWith(this.$wrap);

  this.$values = $("<div class='dropdown_wrap select autocomplete'>");
  this.$values.on("mousedown", function (event){
    event.preventDefault();
  });
  $("body").append(this.$values);
};
/**
 * Открытие элемента
 */
Autocomplete.prototype.open = function (){
  var open = this.opts.open;
  if (!open && !this.opts.disabled) {
    this.opts.open = true;
    this.value = this.$input.val();
    this.$values.append("<div class='item'>" + this.opts.defaultHint + "</div>");
    this.updateValuesUI();
  }
};
/**
 * Обновление списка найденных элементов
 */
Autocomplete.prototype.updateValuesUI = function (){
  this.$values.css("width", this.$wrap.outerWidth());
  this.$values.css("height", "auto");
  var openType = "open_bottom";
  var coords = {
    width: this.$wrap.outerWidth(),
    height: this.$values.outerHeight(),
    left: this.$wrap.offset().left,
    top: this.$wrap.offset().top + this.$wrap.outerHeight()
  };
  this.$wrap.removeClass("open_top open_bottom");
  this.$values.removeClass("open_top open_bottom");
  if (coords.top + coords.height > $(document).height() &&
    this.$wrap.offset().top - coords.height >= 0) {
    openType = "open_top";
    coords.top = this.$wrap.offset().top - coords.height;
  }
  this.$wrap.addClass(openType);
  this.$values.css(coords).addClass(openType);
};
/**
 * Закрытие элемента
 */
Autocomplete.prototype.close = function (){
  this.opts.open = false;
  if (this.selected === false) {
    this.$input.val("");
    this.$wrap.find("input[type='hidden']").val("");
  }
  this.$wrap.removeClass("open_top open_bottom");
  this.$values.empty().removeClass("open_top open_bottom");
  if (this.opts.onChoose) // чтобы обрабатывать пустую строку
    this.opts.onChoose(this.selected);
};
/**
 * Обновление
 */
Autocomplete.prototype.updateSelectValues = function (){
  if (this.xhr)
    this.xhr.abort();

  if (this.value === this.$input.val())
    return;

  // сбрасываем в ноль, потому что пользователь вбивает новые значения
  this.selected = false;
  this.value = this.$input.val();
  this.$loading.show();
  this.xhr = $.ajax({
    url: this.opts.url,
    method: "post",
    data: {
      query: this.value,
      limit: this.opts.limit
    },
    dataType: "json",
    success: function (data){
      this.data = data.matches;
      this.$values.empty();
      var highlight = new RegExp(data.query, "gi");
      this.data.forEach(function (val, i){
        var $link = $("<a class='value'>" + val["caption"].replace(highlight, function (match){
            return "<b>" + match + "</b>";
          }) + "</a>");
        $link.on("click", {i: i, self: this}, function (event){
          event.data.self.choose(event.data.i);
        });
        this.$values.append($link);
      }.bind(this));
      if (this.data.length === 0)
        this.$values.append("<div class='item'>" + this.opts.emptyHint + "</div>");
      this.updateValuesUI();
      this.$loading.hide();
    }.bind(this)
  });
};
/**
 * Выбор элемента
 * @param index - порядковый номер
 */
Autocomplete.prototype.choose = function (index){
  this.selected = this.data[index];
  this.$wrap.find("input[type='hidden']").val(this.selected["id"]);
  this.$input.val(this.selected["caption"]).blur();
};
/**
 * Загрузка
 * @param url
 * @param id
 */
Autocomplete.prototype.selectById = function(url, id){
  this.$loading.show();
  this.xhr = $.ajax({
    url: url,
    method: "post",
    data: { id: id },
    dataType: "json",
    success: function (data){
      if (data.status === "success") {
        this.selected = data.matches;
        this.$wrap.find("input[type='hidden']").val(this.selected["id"]);
        this.$input.val(this.selected["caption"]);
        this.$loading.hide();
      } else {
        new MessageBox({ message: "При загрузке улицы произошла ошибка, обновите страницу и повторите попытку." });
      }
    }.bind(this),
    error: function(){
      new MessageBox({ message: "При загрузке улицы произошла ошибка, обновите страницу и повторите попытку." });
    }
  });
};

/**
 * Элемент <checkbox>
 * @param $target
 * @param data
 * @param options
 * @constructor
 */
var Checkbox = function ($target, data, options){
  this.opts = $.extend({
    checked: false,
    disabled: false,
    onChange: null
  }, options);
  this.data = data;

  this.$wrap = $("<div class='checkbox'>" +
    "<input name='" + $target.attr("name") + "' type='checkbox' value='" + data["id"] + "' />" +
    "<a><div class='ico'><i class='fa fa-check' aria-hidden='true'></i></div>" + data["caption"] + "</a>" +
    "</div>");
  $target.replaceWith(this.$wrap);

  this.$wrap.find("a").on("click", this, function (event){
    if (!event.data.opts.disabled) {
      event.data.setChecked(!event.data.opts.checked);
      if (event.data.onChange)
        event.data.onChange(!event.data.opts.checked);
    }
  });

  this.setChecked(this.opts.checked);
  this.setDisabled(this.opts.disabled);
};
Checkbox.prototype.setChecked = function (value){
  this.opts.checked = !!value;
  this.$wrap.find("input").prop("checked", this.opts.checked);
};
Checkbox.prototype.getCheckedIds = function (){
  return this.opts.checked;
};
Checkbox.prototype.getData = function (){
  return this.data;
};
Checkbox.prototype.setDisabled = function (value){
  this.opts.disabled = !!value;
  this.$wrap.find("input").prop("disabled", this.opts.disabled);
  if (this.opts.disabled)
    this.setChecked(false);
};

/**
 * Элемент <checkboxGroup>
 * @param $target
 * @param data
 * @param options
 * @constructor
 */
var CheckboxGroup = function ($target, data, options){
  this.data = data;
  this.opts = $.extend({
    selected: false, /* array of indexes */
    disabled: false
  }, options);
  this.$wrap = $("<div class='checkbox_group'></div>");

  this.checkboxes = [];
  this.data.forEach(function (val){
    var checkbox = new Checkbox($("<input name='" + $target.attr("name") + "[]' />"), val);
    this.checkboxes.push(checkbox);
    this.$wrap.append(checkbox.$wrap);
  }.bind(this));
  $target.replaceWith(this.$wrap);

  if (this.opts.selected !== false)
    this.setChecked(this.opts.selectedItems, true);
};
/**
 * Установить значение всех чекбоксов
 * @param value
 */
CheckboxGroup.prototype.setCheckedAll = function (value){
  this.checkboxes.forEach(function (checkbox){
    checkbox.setChecked(value);
  });
};
/**
 * Установить значение у опр. чекбокса
 * @param index
 * @param value
 */
CheckboxGroup.prototype.setCheckedItem = function (index, value){
  this.checkboxes[index].setChecked(value);
};
/**
 * Установить значения из списка чекбоксов
 * @param items - массив с установленными значениями
 * @param value
 */
CheckboxGroup.prototype.setChecked = function (items, value){
  this.setCheckedAll(!value);
  items.forEach(function (val){
    this.setCheckedItem(val, value);
  }.bind(this));
};
/**
 * Получить id установленныех чекбоксов
 * @returns {Array}
 */
CheckboxGroup.prototype.getCheckedIds = function (){
  var ret = [];
  this.checkboxes.forEach(function (checkbox){
    if (checkbox.getCheckedIds())
      ret.push(checkbox.getData()["id"]);
  });
  return ret;
};
/**
 * Установка checkbox по маске
 * @param mask
 */
CheckboxGroup.prototype.setCheckedByMask = function (mask){
  this.opts.selectedMask = mask;
  this.checkboxes.forEach(function (checkbox, index){
    this.setCheckedItem(index, checkbox.data["id"] & mask);
  }.bind(this));
};
CheckboxGroup.prototype.getValue = function (){
  return this.getCheckedIds();
};

/**
 * Элемент <radio>
 * @param $target
 * @param data
 * @param options
 * @constructor
 */
var Radio = function ($target, data, options){
  this.opts = $.extend({
    disabled: false,
    onSelect: null
  }, options);
  this.data = data;

  this.$wrap = $("<div class='radio'>" +
    "<input name='" + $target.attr("name") + "' type='radio' value='" + data["id"] + "' />" +
    "<a><div class='ico'><i></i></div>" + data["caption"] + "</a>" +
    "</div>");
  $target.replaceWith(this.$wrap);

  this.$wrap.find("a").on("click", this, function (event){
    if (!event.data.opts.disabled) {
      event.data.select();
      if (event.data.onSelect)
        event.data.onSelect();
    }
  });
  this.setDisabled(this.opts.disabled);
};
Radio.prototype.select = function (){
  this.$wrap.find("input").prop("checked", true);
};
Radio.prototype.setDisabled = function (value){
  this.opts.disabled = !!value;
  this.$wrap.find("input").prop("disabled", this.opts.disabled);
  if (this.opts.disabled)
    this.setChecked(false);
};

/**
 * Элемент <radioGroup>
 * @param $target
 * @param data
 * @param options
 * @constructor
 */
var RadioGroup = function ($target, data, options){
  this.data = data;
  this.opts = $.extend({
    selected: false,
    disabled: false
  }, options);
  this.$wrap = $("<div class='radio_group'></div>");

  this.radioes = [];
  this.data.forEach(function (val){
    var radio = new Radio($("<input name='" + $target.attr("name") + "' />"), val);
    this.radioes.push(radio);
    this.$wrap.append(radio.$wrap);
  }.bind(this));
  $target.replaceWith(this.$wrap);

  if (this.opts.selected !== false)
    this.select(this.opts.selected);
};
RadioGroup.prototype.select = function (index){
  this.radioes[index].select();
};

/**
 * Элемент <counter>
 * @param $target
 * @param options
 * @constructor
 */
var Counter = function ($target, options){
  this.opts = $.extend({
    value: 0,
    isFloat: false,
    minValue: false,
    maxValue: false,
    disabled: false,
    onChange: null
  }, options);
  this.$wrap = $("<div class='counter'>" +
    "<button tabindex='-1' class='inc' type='button'><i class='fa fa-caret-up'></i></button>" +
    "<button tabindex='-1' class='dec' type='button'><i class='fa fa-caret-down'></i></button>" +
    "<input class='input' />" +
    "<input name='" + $target.attr("name") + "' type='hidden' />" +
    "</div>");
  this.$wrap.find(".inc").on("click", this, function (event){
    event.data.inc();
    if (event.data.opts.onChange)
      event.data.opts.onChange(event.data.opts.value);
  });
  this.$wrap.find(".dec").on("click", this, function (event){
    event.data.dec();
    if (event.data.opts.onChange)
      event.data.opts.onChange(event.data.opts.value);
  });
  this.$input = this.$wrap.find(".input");
  this.$input.on("change", this, function (event){
    event.data.setValue(event.data.$input.val());
    if (event.data.opts.onChange)
      event.data.opts.onChange(event.data.opts.value);
  });
  $target.replaceWith(this.$wrap);
  this.setValue(this.opts.value);
  this.setDisabled(this.opts.disabled);
};
/**
 * Установка значения
 * @param value
 */
Counter.prototype.setValue = function (value){
  value = (value + "").replace(/[,]/g, ".").replace(/[^0-9.-]/g, "");
  var match = /^(-?[0-9]*)(\.[0-9]*)?/.exec(value);
  var intPart = match[1];
  intPart = parseInt(intPart);
  if (isNaN(intPart))
    intPart = 0;
  var floatPart = (match[2] ? match[2].substr(1) : "").replace(/(0*)$/, "");
  if (!this.opts.isFloat)
    floatPart = "";
  if (this.opts.minValue !== false && intPart < this.opts.minValue) {
    intPart = this.opts.minValue;
    floatPart = "";
  }
  if (this.opts.maxValue !== false && intPart >= this.opts.maxValue) {
    intPart = this.opts.maxValue;
    floatPart = "";
  }
  this.opts.value = intPart + (floatPart ? "." + floatPart : "");
  this.$wrap.find("input[type='hidden']").val(this.opts.value);
  this.$input.val(
    (intPart < 1000 ? intPart : (intPart + "").replace(/\B(?=(?:\d{3})+(?!\d))/g, " ")) +
    (floatPart ? "." + floatPart : "")
  );
};
/**
 * Получить значение
 */
Counter.prototype.getValue = function (){
  return this.opts.value;
};
/**
 * Активность элемента
 * @param value
 */
Counter.prototype.setDisabled = function (value){
  this.opts.disabled = !!value;
  if (this.opts.disabled)
    this.$wrap.addClass("disabled");
  else
    this.$wrap.removeClass("disabled");
  this.$input.prop("disabled", this.opts.disabled);
};
/**
 * Увеличить значение
 */
Counter.prototype.inc = function (){
  var match = /^([0-9]*)(\.[0-9]*)?/.exec(this.opts.value);
  var intPart = match[1];
  intPart = parseInt(intPart);
  if (isNaN(intPart))
    intPart = 0;
  var floatPart = (match[2] ? match[2].substr(1) : "").replace(/(0*)$/, "");
  if (!this.opts.disabled)
    this.setValue((intPart + 1) + (floatPart ? "." + floatPart : ""));
};
/**
 * Уменьшить значение
 */
Counter.prototype.dec = function (){
  var match = /^([0-9]*)(\.[0-9]*)?/.exec(this.opts.value);
  var intPart = match[1];
  intPart = parseInt(intPart);
  if (isNaN(intPart))
    intPart = 0;
  var floatPart = (match[2] ? match[2].substr(1) : "").replace(/(0*)$/, "");
  if (!this.opts.disabled)
    this.setValue((intPart - 1) + (floatPart ? "." + floatPart : ""));
};

/**
 * Элемент <range>
 * @constructor
 */
var Range = function ($target, options){
  this.opts = $.extend({
    minValue: false,
    maxValue: false,
    fromValue: false,
    toValue: false,
    step: 1
  }, options);
  this.$wrap = $("<div class='range'>" +
    "<table class='column'>" +
    "<tr>" +
    "<td class='left'><input name='" + $target.attr("name") + "[]' /></td>" +
    "<td class='middle'>&mdash;</td>" +
    "<td class='right'><input name='" + $target.attr("name") + "[]' /></td>" +
    "</tr>" +
    "</table>" +
    "<input />" +
    "</div>");

  this.$slider = this.$wrap.find("input").eq(2);
  this.$slider.ionRangeSlider({
    type: "double",
    min: this.opts.minValue,
    max: this.opts.maxValue,
    from: this.opts.fromValue,
    to: this.opts.toValue,
    step: this.opts.step,
    hide_from_to: true,
    force_edges: true,
    onChange: function ($el){
      this.changeMinCounter($el.from);
      this.changeMaxCounter($el.to);
    }.bind(this)
  });
  this.$slider = this.$slider.data("ionRangeSlider");

  this.minCounter = new Counter(this.$wrap.find("input[name='" + $target.attr("name") + "[]']").eq(0), {
    value: this.opts.minValue,
    minValue: this.opts.minValue,
    onChange: function (value){
      this.changeMinCounter(value);
      this.$slider.update({from: this.minCounter.getValue(), to: this.maxCounter.getValue()});
    }.bind(this)
  });
  this.maxCounter = new Counter(this.$wrap.find("input[name='" + $target.attr("name") + "[]']").eq(1), {
    value: this.opts.maxValue,
    maxValue: this.opts.maxValue,
    onChange: function (value){
      this.changeMaxCounter(value);
      this.$slider.update({from: this.minCounter.getValue(), to: this.maxCounter.getValue()});
    }.bind(this)
  });
  if (this.opts.fromValue !== false)
    this.changeMinCounter(this.opts.fromValue);
  if (this.opts.toValue !== false)
    this.changeMaxCounter(this.opts.toValue);
  $target.replaceWith(this.$wrap);
};
/**
 * Изменение миниального счётчика
 * @param value
 */
Range.prototype.changeMinCounter = function (value){
  value = parseInt(value);
  var to = parseInt(this.maxCounter.getValue());
  if (value > to)
    value = to;
  this.minCounter.setValue(value);
};
/**
 * Изменение максималного счётчика
 * @param value
 */
Range.prototype.changeMaxCounter = function (value){
  value = parseInt(value);
  var from = parseInt(this.minCounter.getValue());
  if (value < from)
    value = from;
  this.maxCounter.setValue(value);
};
Range.prototype.getValue = function (){
  return [parseInt(this.minCounter.getValue()), parseInt(this.maxCounter.getValue())];
};

/**
 * Элемент <datepicker>
 * @param $target
 * @param options
 * @constructor
 */
var Datepicker = function ($target, options){
  var today = new Date();
  this.pointerHeight = 10;
  this.opts = $.extend({
    open: false,
    disabled: false,
    selected: {year: today.getFullYear(), month: today.getMonth(), date: today.getDate()},
    selectedMonth: today.getMonth(),
    selectedYear: today.getFullYear(),
    onChoose: null
  }, options);
  this.$wrap = $("<div class='dropdown datepicker'>" +
    "<div class='caret'><i class='fa fa-calendar-o'></i></div>" +
    "<div class='input caption'>Не выбрано</div>" +
    "<input name='" + $target.attr("name") + "' type='hidden' />" +
    "</div>");
  this.$wrap.on("click", this, function (event){
    event.data.open();
    event.stopPropagation();
  });
  $target.replaceWith(this.$wrap);

  this.$datepicker = $("<div class='datepicker_wrap'>" +
    "<div class='datepicker_header'>" +
    "<div class='datepicker_month'></div>" +
    "<a class='datepicker_left'><i class='fa fa-angle-left'></i></a>" +
    "<a class='datepicker_right'><i class='fa fa-angle-right'></i></a>" +
    "</div>" +
    "<table class='datepicker_body'>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "<tr><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td><td><a></a></td></tr>" +
    "</table>" +
    "</div>");
  this.$datepicker.find(".datepicker_left").on("click", this, function (event){
    event.data.prevMonth();
  });
  this.$datepicker.find(".datepicker_right").on("click", this, function (event){
    event.data.nextMonth();
  });
  this.$datepicker.on("click", function (event){
    event.stopPropagation();
  });
  $("body").append(this.$datepicker);

  this.fill();
  this.choose(this.opts.selected);
  this.setDisabled(this.opts.disabled);
};
/**
 * Открыть элемент
 */
Datepicker.prototype.open = function (){
  var open = this.opts.open;
  $(document).trigger("click");
  if (!open && !this.opts.disabled) {
    this.opts.open = true;

    var openType = "open_bottom";
    var coords = {
      width: this.$datepicker.outerWidth(),
      height: this.$datepicker.outerHeight(),
      left: this.$wrap.offset().left,
      top: this.$wrap.offset().top + this.$wrap.outerHeight() + this.pointerHeight
    };
    this.$datepicker.removeClass("open_top open_bottom");
    if (coords.top + coords.height > $(document).height() &&
      this.$wrap.offset().top - coords.height - this.pointerHeight >= 0) {
      openType = "open_top";
      coords.top = this.$wrap.offset().top - coords.height - this.pointerHeight;
    }
    this.$datepicker.css(coords).addClass(openType);

    $(document).on("click", this, function (event){
      event.data.close();
    });
  }
};
/**
 * Закрыть элемент
 */
Datepicker.prototype.close = function (){
  this.opts.open = false;
  this.$datepicker.removeClass("open_top open_bottom");
  $(document).off("click");
};
/**
 * Установка активности элемента
 * @param value
 */
Datepicker.prototype.setDisabled = function (value){
  this.opts.disabled = !!value;
  if (this.opts.disabled)
    this.$wrap.addClass("disabled");
  else
    this.$wrap.removeClass("disabled");
};
/**
 * Заполнение калндаря
 */
Datepicker.prototype.fill = function (){
  var currentMonth = new Date(this.opts.selectedYear, this.opts.selectedMonth, 1),
    date = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
  // смещаемся назад до первого понедельника
  while (date.getDay() !== 1)
    date.setDate(date.getDate() - 1);

  this.$datepicker.find(".datepicker_month").text(currentMonth.getMonthName("nom") + ", " + currentMonth.getFullYear());
  for (var i = 0; i < 42; ++i) {
    var day = i % 7,
      week = parseInt(i / 7),
      $day = this.$datepicker.find("tr").eq(week).find("td").eq(day).find("a");

    $day.text(date.getDate());

    if (!(date.getMonth() === currentMonth.getMonth() && date.getFullYear() === currentMonth.getFullYear()))
      $day.addClass("datepicker_outmonth");
    else
      $day.removeClass("datepicker_outmonth");

    if (this.opts.selected !== null && date.getDate() === this.opts.selected.date &&
      date.getMonth() === this.opts.selected.month && date.getFullYear() === this.opts.selected.year)
      $day.addClass("datepicker_active");
    else
      $day.removeClass("datepicker_active");

    $day.off("click");
    $day.on("click", {
      self: this,
      year: date.getFullYear(),
      month: date.getMonth(),
      date: date.getDate()
    }, function (event){
      var date = {year: event.data.year, month: event.data.month, date: event.data.date};
      event.data.self.choose(date);
      if (event.data.self.opts.onChoose)
        event.data.self.opts.onChoose(date);
    });

    date.setDate(date.getDate() + 1);
  }
};
/**
 * Выбор определённого числа
 */
Datepicker.prototype.choose = function (date){
  date = new Date(date.year, date.month, date.date);
  this.opts.selected = {year: date.getFullYear(), month: date.getMonth(), date: date.getDate()};
  this.opts.selectedMonth = date.getMonth();
  this.opts.selectedYear = date.getFullYear();
  this.$wrap.find(".caption").text((date.getDate() + " " + date.getMonthName("gen") + " " + date.getFullYear()).toLowerCase());
  this.$wrap.find("input[type='hidden']").val(date.getFullYear() + "" +
    (date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1) + "" +
    (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()));
  this.fill();
};
Datepicker.prototype.selectByDate = function(date){
  date = /^(\d{4})(\d{2})(\d{2})$/.exec(date);
  if (date){
    date = { year: parseInt(date[1]), month: parseInt(date[2]) - 1, date: parseInt(date[3]) };
    this.choose(date);
  }
};
/**
 * Предыдущий месяц
 */
Datepicker.prototype.prevMonth = function (){
  var date = new Date(this.opts.selectedYear, this.opts.selectedMonth - 1, 1);
  this.opts.selectedYear = date.getFullYear();
  this.opts.selectedMonth = date.getMonth();
  this.fill();
};
/**
 * Следующий месяц
 */
Datepicker.prototype.nextMonth = function (){
  var date = new Date(this.opts.selectedYear, this.opts.selectedMonth + 1, 1);
  this.opts.selectedYear = date.getFullYear();
  this.opts.selectedMonth = date.getMonth();
  this.fill();
};

/**
 * Время
 */
var Timeago = {
  yesterday: null,
  now: null,
  tomorrow: null,
  yesterdayTexts: ["Сегодня в {t}", "вчера в {t}", "{d} в {t}"],
  tomorrowTexts: ["Сегодня в {t}", "завтра в {t}", "{d} в {t}"]
};
/**
 * Выполнить парсинг времени
 */
Timeago.exec = function (){
  this.now = new Date();
  this.yesterday = new Date(this.now.getTime() - 24 * 3600 * 1000);
  this.tomorrow = new Date(this.now.getTime() + 24 * 3600 * 1000);
  var self = this;
  $("[data-timestamp]").each(function (){
    var info = self.parse($(this).attr("data-timestamp") * 1000);
    if (info[0] !== false)
      $(this).attr("title", info[1]).text(info[0]);
    else
      $(this).text(info[1]);
  });
};
/**
 * Создание корректного времени и подсказки
 * @param timestamp
 * @returns {[*,*]}
 */
Timeago.parse = function (timestamp){
  var date = new Date(timestamp),
    text = date.getDate() + " " + date.getMonthName("gen") + " " + date.getFullYear() + " в " + date.getHours() + ":" + date.getFullMinutes(),
    isNeedHint = true;
  if (this.now - timestamp >= 0) {
    if (this.now.getDate() === date.getDate() && this.now.getMonth() === date.getMonth() && this.now.getFullYear() === date.getFullYear())
      text = "сегодня в " + date.getHours() + ":" + date.getFullMinutes();
    else if (this.yesterday.getDate() === date.getDate() && this.yesterday.getMonth() === date.getMonth() && this.yesterday.getFullYear() === date.getFullYear())
      text = "вчера в " + date.getHours() + ":" + date.getFullMinutes();
    else if (this.now.getFullYear() === date.getFullYear())
      text = date.getDate() + " " + date.getMonthName("gen") + " в " + date.getHours() + ":" + date.getFullMinutes();
    else
      isNeedHint = false;
  }
  else {
    if (this.now.getDate() === date.getDate() && this.now.getMonth() === date.getMonth() && this.now.getFullYear() === date.getFullYear())
      text = "сегодня в " + date.getHours() + ":" + date.getFullMinutes();
    else if (this.tomorrow.getDate() === date.getDate() && this.tomorrow.getMonth() === date.getMonth() && this.tomorrow.getFullYear() === date.getFullYear())
      text = "завтра в " + date.getHours() + ":" + date.getFullMinutes();
    else if (this.now.getFullYear() === date.getFullYear())
      text = date.getDate() + " " + date.getMonthName("gen") + " в " + date.getHours() + ":" + date.getFullMinutes();
    else
      isNeedHint = false;
  }
  return [text, isNeedHint ? date.getDate() + " " + date.getMonthName("gen") + " " + date.getFullYear() + " в " + date.getHours() + ":" + date.getFullMinutes() : false];
};

/**
 * Постраничная навигация
 * @param $target
 * @param options
 * @constructor
 */
var Pagination = function ($target, options){
  this.opts = $.extend({
    offset: 0,
    onPage: 0,
    count: 0
  }, options);
  this.$wrap = $("<ul class='pagination'>");
  var currentPage = this.opts.offset,
    countPages = Math.ceil(this.opts.count / this.opts.onPage);
  if (isNaN(currentPage))
    currentPage = 0;
  var $link = null;
  if (countPages > 1) {
    if (currentPage > 2) {
      $link = $("<li><a><i class='fa fa-angle-double-left'></i></a></li>");
      $link.on("click", this, function (event){
        event.data.go(0);
      });
      this.$wrap.append($link);
    }
    for (var i = -2; i <= 2; ++i) {
      var offset = currentPage + i;
      if (offset < 0 || offset > countPages - 1)
        continue;
      $link = $("<li><a>" + (offset + 1) + "</a></li>");
      $link.on("click", {self: this, offset: offset}, function (event){
        event.data.self.go(event.data.offset);
      });
      if (offset === currentPage)
        $link.addClass("active");
      this.$wrap.append($link);
    }
    if (currentPage < countPages - 3) {
      $link = $("<li><a><i class='fa fa-angle-double-right'></i></a></li>");
      $link.on("click", this, function (event){
        event.data.go(countPages - 1);
      });
      this.$wrap.append($link);
    }
    if (countPages > 5) {
      $link = $("<li><a title='Перейти на страницу'><i class='fa fa-sign-out'></i></a></li>");
      $link.on("click", this, function (event){
        event.data.goto(countPages);
      });
      this.$wrap.append($link);
    }
  } else {
    this.$wrap.hide();
  }
  $target.replaceWith(this.$wrap);
};
Pagination.prototype.go = function (offset){
  if (offset)
    Location.add("offset", offset);
  else
    Location.remove("offset");
  Location.reload();
};
Pagination.prototype.goto = function (count){
  new PromptBox({
    message: "Номер страницы (всего " + count + " " + Common.getNumEnding(count, ["страница", "страницы", "страниц"]) + "):",
    onClick: function(page, type){
      if (type === "ok") {
        page = parseInt(page);
        if (isNaN(page))
          page = 0;
        this.go(Math.clamp(page - 1, 0, count - 1));
      }
    }.bind(this)
  });
};

/**
 * Коллекция всплывающих окон
 */
var PopupCollection = {
  title: document.title,
  popups: []
};
/**
 * Инициализация
 */
PopupCollection.init = function (){
  $(document).on("keydown", this, function (event){
    var self = event.data,
        popup = self.peek();
    if (event.keyCode === 27 && popup && !popup.opts.disableEscKey)
      self.pop();
  });
  $(window).on("resize", this, function(event){
    var self = event.data,
      popup = self.peek();
    if (popup && popup.opts.onResize)
      popup.opts.onResize();
  });
};
/**
 * Получить верхний попап
 * @returns {boolean}
 */
PopupCollection.peek = function (){
  var size = this.popups.length;
  return size > 0 ? this.popups[size - 1] : false;
};
/**
 * Обновить отображение
 */
PopupCollection.update = function (){
  var $body = $("body");
  $body.css("overflow", "auto");
  $(".popup, .pv").hide();
  var popup = this.peek();
  if (popup !== false) {
    $body.css("overflow", "hidden");
    popup.$wrap.show();
    document.title = popup.opts.title;
  } else {
    document.title = this.title;
  }
};
/**
 * Очистить все попапы
 */
PopupCollection.clear = function (){
  this.popups = [];
  this.update();
};
/**
 * Добавить попап в стек
 * @param popup
 */
PopupCollection.push = function (popup){
  this.popups.push(popup);
  this.update();
  if (popup.opts.onShow)
    popup.opts.onShow();
};
/**
 * Вытащить попап из стека
 */
PopupCollection.pop = function (){
  var popup = this.peek();
  this.popups.pop();
  this.update();
  if (popup && popup.opts.onHide)
    popup.opts.onHide();
};

/**
 * Вспылвающее окно
 * @param options
 * @constructor
 */
var Popup = function (options){
  this.opts = $.extend({
    loaded: false,
    id: false,
    class: false,
    title: document.title,
    disableEscKey: false,
    url: false,
    onLoad: null,
    onShow: null,
    onHide: null,
    onResize: null
  }, options);
  this.$wrap = $("<div class='popup'></div>");
  if (this.opts.id !== false)
    this.$wrap.attr("id", this.opts.id);
  if (this.opts.class !== false)
    this.$wrap.addClass(this.opts.class);
  $("body").append(this.$wrap);
};
Popup.prototype.setUrl = function(url){
  this.opts.loaded = false;
  this.opts.url = url;
};
/**
 * Показываем попап, если он загружен, иначе загружаем
 */
Popup.prototype.show = function (){
  if (this.opts.loaded)
    PopupCollection.push(this);
  else
    this.load();
};
/**
 * Загрузка содержимого
 */
Popup.prototype.load = function (){
  new LoadingBox();
  this.$wrap.empty();
  $.ajax({
    type: "get",
    url: this.opts.url,
    dataType: "html",
    success: (function (data){
      this.opts.loaded = true;
      this.$wrap.html(data);
      this.$wrap.find(".popup_close").on("click", this, function(){
        PopupCollection.pop();
      });
      if (this.opts.onLoad)
        this.opts.onLoad(this);
      PopupCollection.pop(); /* remove loading box */
      PopupCollection.push(this);
    }).bind(this),
    error: function(){
      PopupCollection.pop(); /* remove loading box */
      new MessageBox({ message: "При загрузке произошла ошибка, обновите страницу и повторите попытку." });
    }
  });
};
/**
 * Установить содержимое popup
 * @param $content
 */
Popup.prototype.setContent = function($content){
  this.opts.loaded = true;
  this.$wrap.empty().append($content);
};

/**
 * Просмотр изображений
 */
var PhotoViewer = {
  source: "/act/photo_viewer.php",

  xhr: null,
  photo: { id: false, object: false, type: false, hash: false },
  photos: [],
  offset: 0,
  $photo: null,

  popup: null,
  $wrap: null,
  $prev: null,
  $next: null,
  $close: null,
  $summary: null,
  $photoWrap: null
};
/**
 * Инициализация
 */
PhotoViewer.init = function (){
  this.popup = new Popup({
    class: "photo_viewer",
    onResize: this.adapt.bind(this),
    onHide: this.hide.bind(this)
  });
  this.popup.setContent($("<div class='photo_viewer_layout'>" +
    "<a class='photo_viewer_prev'><i></i></a>" +
    "<a class='photo_viewer_next'><i></i></a>" +
    "<a class='photo_viewer_close'><i></i></a>" +
    "<div class='photo_viewer_summary'></div>" +
    "<div class='photo_viewer_wrap'></div>" +
  "</div>"));
  this.$wrap = this.popup.$wrap;
  this.$wrap.on("scroll", this, function (event){
    var self = event.data,
      top = $(this).scrollTop();
    self.$prev.css("top", top + 50);
    self.$next.css("top", top + 50);
    self.$close.css("top", top);
  });

  this.$prev = this.$wrap.find(".photo_viewer_prev");
  this.$prev.on("click", this, function (event){
    event.data.prevPhoto();
  });
  this.$next = this.$wrap.find(".photo_viewer_next");
  this.$next.on("click", this, function (event){
    event.data.nextPhoto();
  });
  this.$close = this.$wrap.find(".photo_viewer_close");
  this.$close.on("click", function (){
    PopupCollection.pop();
  });
  this.$summary = this.$wrap.find(".photo_viewer_summary");
  this.$photoWrap = this.$wrap.find(".photo_viewer_wrap");
};
/**
 * Открытие просмотра фотографии
 * @param info
 */
PhotoViewer.show = function (info){
  console.log(info);
  $(document).on("keydown.photo_viewer", this, function (event){
    var self = event.data;
    switch (event.keyCode) {
      case 39: // ->
        self.nextPhoto();
        break;
      case 37: // <-
        self.prevPhoto();
        break;
    }
  });
  this.$prev.hide();
  this.$next.hide();

  this.photo.id = info.photo ? info.photo : false;
  this.photo.object = info.object ? info.object : false;
  this.photo.type = info.type ? info.type : false;
  this.photo.hash = info.hash ? info.hash : false;
  this.photos = [];
  this.offset = 0;

  this.$photoWrap.empty();
  this.popup.show();
  this.xhr = $.ajax({
    url: this.source,
    method: "post",
    data: this.photo,
    dataType: "json",
    success: function (data){
      if (data.status === "success") {
        this.xhr = null;
        this.photos = data.photos;
        this.offset = data.offset;
        this.showPhoto();
      } else {
        new MessageBox({ message: "При загрузке фотографий произошла ошибка, обновите страницу и повторите попытку." });
      }
    }.bind(this),
    error: function(){
      new MessageBox({ message: "При загрузке фотографий произошла ошибка, обновите страницу и повторите попытку." });
    }
  });
};
/**
 * Закрытие
 */
PhotoViewer.hide = function(){
  $(document).off("keydown.photo_viewer");
  if (this.xhr){
    this.xhr.abort();
    this.xhr = null;
  }
};
/**
 * Показ фотографии
 */
PhotoViewer.showPhoto = function (){
  this.$photoWrap.empty();
  var count_photos = this.photos.length;
  if (count_photos > 1) {
    this.$prev.show();
    this.$next.show();
    this.$summary.text("Фотография " + (this.offset + 1) + " из " + count_photos);
  } else {
    this.$summary.text("Просмотр фотографии");
  }
  this.$photo = $("<img src='" + this.photos[this.offset].source + "' />");
  this.$photoWrap.append(this.$photo);
  if (count_photos > 1) {
    this.$photo.wrap("<a></a>").closest("a").on("click", this, function(event){
      event.data.nextPhoto();
    });
  }
  this.adapt();
  // кешируем следующие фотографии
  if (count_photos > 1) {
    var prevPhotoId = this.offset - 1,
      nextPhotoId = this.offset + 1;
    if (prevPhotoId < 0)
      prevPhotoId = count_photos - 1;
    nextPhotoId %= count_photos;
    preLoadImg([this.photos[prevPhotoId].source, this.photos[nextPhotoId].source]);
  }
};
/**
 * Адаптирование показа фотографий
 */
PhotoViewer.adaptPhoto = function (){
  var viewport = {
    width: $(window).width() - 100, /* padding */
    height: Math.max($(window).height(), 740 /* min height in css */) - 100 /* padding */
  };
  var photo = this.photos[this.offset],
      width = photo.width,
      height = photo.height,
      ratio = viewport.width < width || viewport.height < height
        ? Math.min(viewport.width / width, viewport.height / height)
        : false;
  if (ratio !== false) {
    width = Math.floor(width * ratio);
    height = Math.floor(height * ratio);
  }
  var top = height < viewport.height ? Math.floor((viewport.height - height) / 2) : 0;
  this.$photo.css({
    "width": width,
    "height": height,
    "margin-top": top
  });
};
/**
 * Адаптирование кнопок left, right под высоту экрана
 */
PhotoViewer.adaptNavigation = function (){
  var height = $(window).height() - 100; /* 100 - padding */
  this.$prev.css("height", height);
  this.$next.css("height", height);
};
/**
 * Изменеие размера
 */
PhotoViewer.adapt = function(){
  this.adaptNavigation();
  this.adaptPhoto();
};
/**
 * Предыдущая фотография
 */
PhotoViewer.prevPhoto = function (){
  this.offset--;
  if (this.offset < 0)
    this.offset = this.photos.length - 1;
  this.showPhoto();
};
/**
 * Следующая фотография
 */
PhotoViewer.nextPhoto = function (){
  this.offset++;
  this.offset %= this.photos.length;
  this.showPhoto();
};

var MessageBox = function(options){
  this.opts = $.extend({
    message: "",
    okCaption: "Ок"
  }, options);
  this.popup = new Popup({
    class: "message_box",
    disableEscKey: true,
    onHide: this.hide.bind(this)
  });
  this.popup.setContent($("<div class='popup_html'>" +
    "<div class='popup_body'>" +
    "<div class='popup_content'>" +
    "<div class='text_message'>" + this.opts.message + "</div>" +
    "<div class='button_wrap'><button class='button'>" + this.opts.okCaption + "</button></div>" +
    "</div>" +
    "</div>" +
  "</div>"));
  this.$wrap = this.popup.$wrap;
  this.$wrap.find("button").on('click', function(){
    PopupCollection.pop();
  });
  this.popup.show();
};
MessageBox.prototype.hide = function(){
  this.$wrap.remove();
};

var PromptBox = function(options){
  this.opts = $.extend({
    message: "",
    okCaption: "Ок",
    cancelCaption: "Отмена",
    onClick: null
  }, options);
  this.popup = new Popup({
    class: "message_box",
    disableEscKey: true,
    onHide: this.hide.bind(this)
  });
  this.popup.setContent($("<div class='popup_html'>" +
    "<div class='popup_body'>" +
    "<div class='popup_content'>" +
    "<label>" + this.opts.message + "</label>" +
    "<input class='input' type='text' />" +
    "<div class='button_wrap'>" +
      "<button class='button'>" + this.opts.okCaption + "</button>" +
      "<button class='button red'>" + this.opts.cancelCaption + "</button>" +
    "</div>" +
    "</div>" +
    "</div>" +
  "</div>"));
  this.$wrap = this.popup.$wrap;
  this.$wrap.find("button").eq(0).on("click", this, function(event){
    var self = event.data;
    if (self.opts.onClick)
      self.opts.onClick(self.$wrap.find("input").val(), "ok");
    PopupCollection.pop();
  });
  this.$wrap.find("button").eq(1).on("click", this, function(event){
    var self = event.data;
    if (self.opts.onClick)
      self.opts.onClick(self.$wrap.find("input").val(), "cancel");
    PopupCollection.pop();
  });
  this.popup.show();
};
PromptBox.prototype.hide = function(){
  this.$wrap.remove();
};

var ConfirmBox = function(options){
  this.opts = $.extend({
    message: "",
    okCaption: "Ок",
    cancelCaption: "Отмена",
    onClick: null
  }, options);
  this.popup = new Popup({
    class: "message_box",
    disableEscKey: true,
    onHide: this.hide.bind(this)
  });
  this.popup.setContent($("<div class='popup_html'>" +
    "<div class='popup_body'>" +
    "<div class='popup_content'>" +
    "<div class='text_message'>" + this.opts.message + "</div>" +
    "<div class='button_wrap'>" +
    "<button class='button'>" + this.opts.okCaption + "</button>" +
    "<button class='button red'>" + this.opts.cancelCaption + "</button>" +
    "</div>" +
    "</div>" +
    "</div>" +
  "</div>"));
  this.$wrap = this.popup.$wrap;
  this.$wrap.find("button").eq(0).on("click", this, function(event){
    PopupCollection.pop();
    var self = event.data;
    if (self.opts.onClick)
      self.opts.onClick("ok");
  });
  this.$wrap.find("button").eq(1).on("click", this, function(event){
    PopupCollection.pop();
    var self = event.data;
    if (self.opts.onClick)
      self.opts.onClick("cancel");
  });
  this.popup.show();
};
ConfirmBox.prototype.hide = function(){
  this.$wrap.remove();
};

var LoadingBox = function(){
  this.popup = new Popup({
    class: "loading_box",
    onHide: this.hide.bind(this)
  });
  this.popup.setContent($("<div class='popup_html'>" +
    "<div class='popup_body'>" +
    "<div class='popup_content'>" +
    "</div>" +
    "</div>" +
  "</div>"));
  this.$wrap = this.popup.$wrap;
  this.popup.show();
};
LoadingBox.prototype.hide = function(){
  this.$wrap.remove();
};

/**
 * Всплывающее меню
 * @param $target
 * @param data [link, name]
 * @param options
 * @constructor
 */
var Menu = function($target, data, options){
  this.$target = $target;
  this.pointerHeight = 10; /* высота указателя */
  this.opts = $.extend({
    open: false,
    onChoose: null
  }, options);
  this.$wrap = $("<div class='dropdown_menu'></div>");
  data.forEach(function(val){
    this.$wrap.append("<a href='" + val["link"] + "' class='item'>" + val["caption"] + "</a>");
  }.bind(this));
  this.$target.on("click", this, function(event){
    event.data.open();
    event.stopPropagation();
  });
  $("body").append(this.$wrap);
};
Menu.prototype.open = function(){
  var open = this.opts.open;
  $(document).trigger("click");
  if (!open && !this.opts.disabled) {
    this.opts.open = true;
    var openType = "open_bottom";
    var coords = {
      width: this.$wrap.outerWidth(),
      height: this.$wrap.outerHeight(),
      left: this.$target.offset().left + this.$target.outerWidth() - this.$wrap.outerWidth(),
      top: this.$target.offset().top + this.$target.outerHeight() + this.pointerHeight
    };
    this.$wrap.removeClass("open_top open_bottom");
    if (coords.top + coords.height > $(document).height() &&
      this.$target.offset().top - coords.height - this.pointerHeight >= 0) {
      openType = "open_top";
      coords.top = this.$target.offset().top - coords.height - this.pointerHeight;
    }
    this.$wrap.css(coords).addClass(openType);
    $(document).on("click", this, function (event){
      event.data.close();
    });
  }
};
/**
 * Закрыть элемент
 */
Menu.prototype.close = function (){
  this.opts.open = false;
  this.$wrap.removeClass("open_top open_bottom");
  $(document).off("click");
};

/**
 * Подсказки
 */
var Tooltip = function ($target, options){
  this.$target = $target;
  this.opts = $.extend({
    width: false,
    message: "",
    position: "top",
    delay: 0,
    showDt: 300,
    hideDt: 400
  }, options);
  this.open = false;
  this.timeOut = false;
  this.$wrap = $("<div class='tooltip'>" +
    "<div class='pointer'></div>" +
    "<div class='message'>" + this.opts.message + "</div>" +
  "</div>");
  if (this.opts.width)
    this.$wrap.css("width", this.opts.width);
  this.$wrap.addClass(this.opts.position);
  $("body").append(this.$wrap);
  this.$target.on("mouseenter", this, function (event){
    event.data.show();
  });
  this.$target.on("mouseleave", this, function (event){
    event.data.hide();
  });
};
/**
 * Показать
 */
Tooltip.prototype.show = function (){
  clearTimeout(this.timeOut);
  var targetInfo = {
      width: this.$target.outerWidth(),
      height: this.$target.outerHeight(),
      left: this.$target.offset().left,
      top: this.$target.offset().top
    },
    ttpInfo = {
      width: this.$wrap.outerWidth(),
      height: this.$wrap.outerHeight()
    },
    offset = 7, /* animation offset */
    start = {
      left: 0,
      top: 0
    },
    finish = {
      left: 0,
      top: 0
    };
  switch (this.opts.position) {
    case "top":
      finish.left = targetInfo.left + targetInfo.width / 2 - ttpInfo.width / 2;
      finish.top = targetInfo.top - ttpInfo.height - 7;
      start.left = finish.left;
      start.top = finish.top - offset;
      break;
    case "right":
      finish.left = targetInfo.left + targetInfo.width + 10;
      finish.top = targetInfo.top + targetInfo.height / 2 - ttpInfo.height / 2;
      start.left = finish.left + offset;
      start.top = finish.top;
      break;
    case "bottom":
      finish.left = targetInfo.left + targetInfo.width / 2 - ttpInfo.width / 2;
      finish.top = targetInfo.top + targetInfo.height + 7;
      start.left = finish.left;
      start.top = finish.top + offset;
      break;
    case "left":
      finish.left = targetInfo.left - ttpInfo.width - 10;
      finish.top = targetInfo.top + targetInfo.height / 2 - ttpInfo.height / 2;
      start.left = finish.left - offset;
      start.top = finish.top;
      break;
  }
  if (!this.open) {
    this.open = true;
    this.$wrap.css({ left: start.left, top: start.top, opacity: 0 }).show();
  }
  this.$wrap
    .stop()
    .animate({ left: finish.left, top: finish.top, opacity: 1 }, this.opts.showDt);
};
/**
 * Скрыть
 */
Tooltip.prototype.hide = function (){
  this.timeOut = setTimeout(function (){
    this.$wrap.stop().fadeOut(this.opts.hideDt, function (){ this.open = false; }.bind(this));
  }.bind(this), this.opts.delay);
};
/**
 * Удалить
 */
Tooltip.prototype.remove = function(){
  this.$target.off("mouseenter mouseleave");
  this.$wrap.remove();
};


/**
 * Кеширование изоюражений
 * @param images
 */
function preLoadImg(images){
  images.forEach(function (val){
    var img = new Image();
    img.src = val;
  });
}

$(function (){
  // проверка наличия аттрибута
  $.fn.hasAttr = function (name){
    return this.attr(name) !== undefined;
  };
  Location.init();
  PopupCollection.init();
  PhotoViewer.init();
  if (user.logged){
    new Menu($("#user_action"), [ ["/act/user.php?act=logout", "Выйти"] ]);
  } else {
    var loginPopup = new Popup({ class: "message_box", "url": "/users/login" });
    $("#user_action").on("click", function(){
      loginPopup.show();
    });
  }
});