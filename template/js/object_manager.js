"use strict";

/**
 * Арендодатели
 */
var Landlord = function ($target){
  this.countTasks = 0;
  this.phones = new Set();
  this.$wrap = $("<div class='landlord_wrap'>");
  this.$attach = $("<table class='column'>" +
    "<tr>" +
    "<td class='left'>" +
    "<label>Телефон</label>" +
    "<input class='input' type='text' />" +
    "<small>Чтобы прикрепить номер, нажмите Enter</small>" +
    "</td>" +
    "<td class='right'></td>" +
    "</tr>" +
    "</table>");
  this.$attachInput = this.$attach.find("input");
  this.$attachInput.on("keydown", this, function (event){
    if (event.keyCode === 13) {
      event.data.attach($(this).val());
      $(this).val("");
      event.preventDefault();
    }
  });
  this.$attachLoading = this.$attach.find(".right");
  this.$wrap.append(this.$attach);

  this.$landlords = $("<div class='landlords'>");
  this.$wrap.append(this.$landlords);

  this.$createLandlord = $("<table class='landlord_item landlord_create column'>" +
    "<tr>" +
    "<td class='left'>&nbsp;</td>" +
    "<td class='right'>" +
    "<div class='dropbox'><div class='dropbox_inner'><i class='fa fa-user-plus' aria-hidden='true'></i>Добавить нового арендодателя</div></div>" +
    "</td>" +
    "</tr>" +
    "</table>");
  this.$wrap.append(this.$createLandlord);

  $target.replaceWith(this.$wrap);
};
Landlord.prototype.load = function (objectId, objectType){
  this.$wrap.addClass("loading");
  $.ajax({
    url: "/act/landlord.php?act=get_by_id",
    type: "post",
    data: { id: objectId, type: objectType },
    dataType: "json",
    success: function (data){
      data.landlord.forEach(function(item){
        this.phones.addArray(item.phones);
        this.createLandlord(item.id, item.name, item.phones);
      }.bind(this));
    }.bind(this),
    error: function (){
      new MessageBox({ message: "При загрузке арендодателей произошла ошибка, обновите страницу и повторите попытку." });
    },
    complete: function(){
      this.$wrap.removeClass("loading");
    }.bind(this)
  });
};
/**
 * Создание dom дерево нового арендодателя
 * @param id - идентификатор (0 - необходимо создать нового арендодателя)
 * @param name - имя
 * @param phones - список номеров
 */
Landlord.prototype.createLandlord = function (id, name, phones){
  var $landlord = id
    ? $("<table class='landlord_item column'>" +
      "<tr>" +
      "<td class='left'>" +
      "<label>ФИО</label>" +
      "<input class='input' type='text' disabled='true' value='" + name + "' />" +
      "<input type='hidden' value='/id/' name='landlord[]' />" +
      "<input type='hidden' value='" + id + "' name='landlord[]' />" +
      "<small class='action'><a>2 объекта</a><a>Редактировать</a><a class='unattach'>Открепить</a></small>" +
      "</td>" +
      "<td class='right'>" +
      "<div class='dropbox'><div class='dropbox_inner'><i class='fa fa-link' aria-hidden='true'></i>Закрепить</div></div>" +
      "</td>" +
      "</tr>" +
      "</table>")
    : $("<table class='landlord_item column'>" +
      "<tr>" +
      "<td class='left'>" +
      "<label>ФИО</label>" +
      "<input type='hidden' value='/new/' name='landlord[]' />" +
      "<input class='input' type='text' name='landlord[]' />" +
      "</td>" +
      "<td class='right'>" +
      "<div class='dropbox'><div class='dropbox_inner'><i class='fa fa-link' aria-hidden='true'></i>Закрепить</div></div>" +
      "</td>" +
      "</tr>" +
      "</table>");
  $landlord.find(".unattach").on("click", this, function (event){
    event.data.unattach(phones);
    $landlord.remove();
  });

  phones.forEach(function (phone){
    var match = null, view = "";
    if ((match = /^(?:[78])?(([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2}))$/gi.exec(phone)) !== null)
      view = "+7 " + match[2] + " " + match[3] + " " + match[4] + " " + match[5];
    if ((match = /^([0-9]{2})([0-9]{2})([0-9]{2})$/gi.exec(phone)) !== null)
      view = match[1] + " " + match[2] + " " + match[3];
    var $phone = $("<div class='phone_tag'>" + view + "</div>");
    if (!id) {
      $phone.addClass("draggable");
      $phone.append("<input type='hidden' value='/phone/' name='landlord[]' /><input type='hidden' value='" + phone + "' name='landlord[]' />");

      // удаление
      $phone.append("<a class='remove'><i class='fa fa-times' aria-hidden='true'></i></a>");
      $phone.find(".remove").on("mousedown", this, function (event){
        event.data.unattach([phone]);
        var $landlord = $(this).closest(".landlord_item");
        $phone.remove();
        if (!$landlord.find(".phone_tag").length)
          $landlord.remove();
        event.stopPropagation();
      });

      // перемещение
      $phone.on("mousedown", this, function (event){
        var self = event.data,
          shift = {x: event.pageX - $(this).offset().left, y: event.pageY - $(this).offset().top},
          $dropboxes = self.showDropboxes();

        // клонируем объект, который будем перемещать
        var $clone = $phone.clone();
        $phone.css("visibility", "hidden");
        $clone.addClass("drag");
        $("body").append($clone);
        $clone.css({left: event.pageX - shift.x, top: event.pageY - shift.y});

        // перемещение
        $(document).on("mousemove", function (event){
          $clone.css({left: event.pageX - shift.x, top: event.pageY - shift.y});
          $dropboxes.each(function (){
            if ($(this).offset().top <= event.pageY && event.pageY <= $(this).offset().top + $(this).outerHeight() &&
              $(this).offset().left <= event.pageX && event.pageX <= $(this).offset().left + $(this).outerWidth())
              $(this).addClass("hover");
            else
              $(this).removeClass("hover");
          });
        });

        // конец перемещения
        $(document).on("mouseup", function (event){
          $clone.remove();
          // определяем арендодателя
          var dropbox = null;
          $dropboxes.each(function (){
            if ($(this).offset().top <= event.pageY && event.pageY <= $(this).offset().top + $(this).outerHeight() &&
              $(this).offset().left <= event.pageX && event.pageX <= $(this).offset().left + $(this).outerWidth()) {
              dropbox = $(this);
            }
            return !dropbox;
          });

          if (dropbox) {
            var $prevLandlord = $phone.closest(".landlord_item"),
              $newLandlord = dropbox.closest(".landlord_item");
            if ($newLandlord.hasClass("landlord_create")) { // создаём нового арендодателя с номером, поэтому старого удаляем
              self.createLandlord(0, false, [phone]);
              $phone.remove();
            } else { // перемещаем к другому арендодателю
              $phone.appendTo($newLandlord.find(".right"));
              $phone.css("visibility", "visible");
            }
            if (!$prevLandlord.find(".phone_tag").length)
              $prevLandlord.remove();
          } else {
            $phone.css("visibility", "visible");
          }

          self.hideDropboxes();
          $(document).off("mousemove mouseup");
        });
      });
    }
    $landlord.find(".right").append($phone);
  }.bind(this));
  this.$landlords.append($landlord);
};
/**
 * Прикрепить номер телефона
 * @param phone
 */
Landlord.prototype.attach = function (phone){
  var view = false;
  phone = phone.replace(/[^0-9]/gi, "");

  // мобильный
  var match = /^(?:[78])?(([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2}))$/gi.exec(phone);
  if (match !== null) {
    phone = "7" + match[1];
    view = "+7 " + match[2] + " " + match[3] + " " + match[4] + " " + match[5];
  }

  // домашний
  match = /^([0-9]{2})([0-9]{2})([0-9]{2})$/gi.exec(phone);
  if (match !== null) {
    view = match[1] + " " + match[2] + " " + match[3];
  }

  if (view !== false) {
    this.countTasks++;
    var $phoneLoading = $("<div class='phone_tag loading'>" + view + "</div>");
    this.$attachLoading.append($phoneLoading);
    $.ajax({
      url: "/act/landlord.php?act=find_by_phone",
      type: "post",
      data: {
        phone: phone
      },
      dataType: "json",
      success: function (data){
        this.countTasks--;
        $phoneLoading.remove();
        if (data.status === "success") {
          // проверка на уникальнсть
          var isUnique = data.landlord.phones.every(function (phone, i){
            return !this.phones.has(phone);
          }.bind(this));
          if (!isUnique)
            return;
          this.phones.addArray(data.landlord.phones);
          this.createLandlord(data.landlord.id, data.landlord.name, data.landlord.phones);
        } else {
          new MessageBox({ message: "При добавлении арендодателя произошла ошибка, обновите страницу и повторите попытку." });
        }
      }.bind(this),
      error: function (){
        this.countTasks--;
        new MessageBox({ message: "При добавлении арендодателя произошла ошибка, обновите страницу и повторите попытку." });
      }
    });
  }
};
/**
 * Открепить телефоны
 */
Landlord.prototype.unattach = function (phones){
  this.phones.removeArray(phones);
};
/**
 * Показать dropboxes
 * @returns {*} - массив всех найденых dropbox
 */
Landlord.prototype.showDropboxes = function (){
  $("body").addClass("dragging");
  this.$createLandlord.show();
  var $dropboxes = this.$wrap.find(".dropbox");
  $dropboxes.show();
  return $dropboxes;
};
/**
 * Скрыть dropboxes
 */
Landlord.prototype.hideDropboxes = function (){
  $("body").removeClass("dragging");
  this.$createLandlord.hide();
  this.$wrap.find(".dropbox").hide();
};
/**
 * Идёт ли обработка
 */
Landlord.prototype.isBusy = function (){
  return this.countTasks > 0;
};

/**
 * Карта
 * @param $target
 * @param options
 * @constructor
 */
var Map = function ($target, options){
  this.countTasks = 0;
  this.opts = $.extend({
    coords: [52.04, 113.49],
    zoom: 12
  }, options);
  this.$wrap = $("<div class='map'>" +
    "<div class='map_loading'><span>Загрузка</span></div>" +
    "<input type='hidden' name='lat' value='52.04' />" +
    "<input type='hidden' name='lon' value='113.49' />" +
    "</div>");
  $target.replaceWith(this.$wrap);

  ymaps.ready(function (){
    this.hideLoading();
    this.map = new ymaps.Map(this.$wrap[0], {
      center: this.opts.coords,
      zoom: this.opts.zoom,
      controls: ["zoomControl"]
    });
    this.map.events.add("click", function (event){
      var global = this.map.converter.pageToGlobal(event.get('position')),
        coords = this.map.options.get('projection').fromGlobalPixels(global, this.map.getZoom());
      this.setCoords(coords);
    }.bind(this));
    this.map.behaviors.disable("scrollZoom");
    this.map.cursors.push('crosshair');
    this.placemark = new ymaps.Placemark(this.opts.coords, {}, {
      iconLayout: 'default#image',
      iconImageHref: '/template/images/map_marker.png',
      iconImageSize: [32, 32],
      iconImageOffset: [-16, -32],
      hasBalloon: false,
      cursor: "move",
      draggable: true
    });
    this.placemark.events.add("dragend", function (event){
      var coords = this.placemark.geometry.getCoordinates();
      this.setCoords(coords);
    }.bind(this));
    this.map.geoObjects.add(this.placemark);
  }.bind(this));
};
/**
 * Установить настройки карты
 * @param coords
 * @param zoom
 */
Map.prototype.setCoords = function (coords, zoom){
  this.opts.coords = coords;
  this.opts.zoom = zoom;
  this.placemark.geometry.setCoordinates(this.opts.coords);
  this.$wrap.find("input[name='lon']").val(this.opts.coords[1]);
  this.$wrap.find("input[name='lat']").val(this.opts.coords[0]);
};
/**
 * Преобразование адреса в координаты
 * @param address
 * @param zoom
 * @param isCenter
 */
Map.prototype.geocode = function (address, zoom){
  this.showLoading();
  this.countTasks++;
  var geocoder = ymaps.geocode(address);
  geocoder.then(
    function (res){
      this.countTasks--;
      var coords = res.geoObjects.get(0).geometry.getCoordinates();
      this.setCoords(coords, zoom);
      this.map.setCenter(this.opts.coords, this.opts.zoom);
      this.hideLoading();
    }.bind(this),
    function (err){
      this.countTasks--;
      this.hideLoading();
      new MessageBox({ message: "При определении местоположения произошла ошибка." });
    }.bind(this)
  );
};
/**
 * Показать загрузку
 */
Map.prototype.showLoading = function (){
  this.$wrap.find(".map_loading").show();
};
/**
 * Убрать загрузку
 */
Map.prototype.hideLoading = function (){
  this.$wrap.find(".map_loading").hide();
};
/**
 * Идёт ли обработка
 */
Map.prototype.isBusy = function(){
  return this.countTasks > 0;
};

/**
 * Загрузка изображений
 */
var Uploader = function ($target, options){
  this.opts = $.extend({
    maxWidth: 4000,
    maxHeight: 4000,
    maxSize: 2 // мб
  }, options);
  this.xhr = null;
  this.files = [];
  this.currentFile = 0;
  this.uploading = false;
  this.progress = 0;

  this.$wrap = $("<div class='uploader'>" +
    "<div class='browser_not_support'>Ваш браузер не поддерживает загрузку файлов</div>" +
    "<div class='uploaded'></div>" +
    "<div class='dropbox'>" +
    "<div class='drop_label'>Перенесите сюда фотографии, чтобы прикрепить их</div>" +
    "<div class='release_label'>Отпустите клавишу мыши, чтобы прикрепить фотографии</div>" +
    "</div>" +
    "<button type='button' class='button'>" +
    "<span class='button_caption'><i class='fa fa-camera'></i>Прикрепить фотографии</span>" +
    "<span class='progress'>" +
    "<span class='back'><div class='inner'></div></span>" +
    "<span class='front'><div class='inner'></div></span>" +
    "</span>" +
    "</button>" +
    "</div>");
  $target.replaceWith(this.$wrap);
  this.$inputFile = $("<input class='uploader_input' type='file' accept='image/jpeg,image/png,image/gif' multiple='true' />");
  this.$inputFile.on("change", this, function (event){
    event.data.send();
  });
  $("body").append(this.$input);
  this.$button = this.$wrap.find("button");
  this.$backProgress = this.$button.find(".progress .back");
  this.$frontProgress = this.$button.find(".progress .front");
  this.$button.on("click", this, function (event){
    event.data.$inputFile.trigger("click");
  });
  new Tooltip(this.$button, {
    message: "JPG, GIF или PNG.<br/>Не более " + this.opts.maxSize + " мб.",
    position: "bottom"
  });

  // поиск рабочего способа для отправки файлов
  if (window.XMLHttpRequest) {
    this.xhr = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    try {
      this.xhr = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
      try { this.xhr = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
    }
  }
  if (!this.xhr) {
    this.$wrap.find(".browser_not_support").show();
    this.$button.hide();
  }
  // // drag n' drop
  // if (window.FileReader) {
  //   $("body").on("dragenter", this, function (event){
  //     var self = event.data;
  //     event = event.originalEvent;
  //     self.dragFile = false;
  //     for (var i = 0; i < event.dataTransfer.types.length; ++i) {
  //       if (event.dataTransfer.types[i] == "Files") {
  //         self.dragFile = true;
  //         break;
  //       }
  //     }
  //     if (self.dragFile)
  //       self.dropbox.show();
  //   });
  //   $("body").on("dragover", this, function (event){
  //     var self = event.data;
  //     event = event.originalEvent;
  //     if (self.dragFile) {
  //       event.dataTransfer.dropEffect = self.dropEffect;
  //       clearTimeout(self.bodyOutTimer);
  //       self.bodyOutTimer = setTimeout((function (){
  //         this.dropbox.hide();
  //       }).bind(self), 100);
  //       Common.preventDefault(event);
  //     }
  //   });
  // }
  // // dropbox
  // this.dropbox = this.wrap.find(".upload_dropbox");
  // this.dropbox.on("dragenter", this, function (event){
  //   event.data.dropbox.addClass("upload_dropbox_over");
  // });
  // this.dropbox.on("dragover", this, function (event){
  //   var self = event.data;
  //   event = event.originalEvent;
  //   clearTimeout(self.dropboxOutTimer);
  //   self.dropboxOutTimer = setTimeout((function (){
  //     this.dropbox.removeClass("upload_dropbox_over");
  //     this.dropEffect = "none";
  //   }).bind(self), 100);
  //   self.dropEffect = "copy";
  // });
  // this.dropbox.on("drop", this, function (event){
  //   var self = event.data;
  //   event = event.originalEvent;
  //   // Img.info = $(".upload_info");
  //   // $(".upload_large_msg").hide();
  //   for (var i = 0; i < event.dataTransfer.files.length; ++i) {
  //     if (event.dataTransfer.files[i].size <= 1024 * 1024 * self.opts.maxSize)
  //       self.files.push(event.dataTransfer.files[i]);
  //   }
  //   self.input.val("");
  //   if (self.files.length && !self.timer) {
  //     self.timer = setInterval((function (){
  //       this.upload();
  //     }).bind(self), 500);
  //   }
  //   Common.preventDefault(event);
  // });
};
/**
 * Загрузка ране загруженных фотографий
 */
Uploader.prototype.load = function (objectId, objectType){
  var $uploaded = this.$wrap.find(".uploaded");
  $uploaded.addClass("loading");
  $.ajax({
    type: "post",
    url: "/act/uploader.php?act=load_images_by_id",
    data: { id: objectId, type: objectType },
    dataType: "json",
    success: function(data){
      data.images.forEach(function(item){ this.addThumb(item); }.bind(this));
    }.bind(this),
    error: function(){
      new MessageBox({ message: "При загрузке фотографий произошла ошибка, обновите страницу и повторите попытку." });
    },
    complete: function(){
      $uploaded.removeClass("loading");
    }
  });
};
/**
 * Отправка выбранных файлов на зарузку
 */
Uploader.prototype.send = function (){
  var input = this.$inputFile[0];
  for (var i = 0; i < input.files.length; ++i) {
    if (input.files[i].size <= 1024 * 1024 * this.opts.maxSize) {
      this.files.push(input.files[i]);
    } else {
      new MessageBox({ message: "Превышен допустимый размер фотографии." });
    }
  }
  this.$inputFile.val("");
  this.uploadStart();
};
/**
 * Обновление прогресса
 */
Uploader.prototype.updateProgress = function (){
  if (this.uploading) {
    this.$button.addClass("processing");
    var countLoaded = this.currentFile - 1,
      countFiles = this.files.length,
      fileWidth = 100 / countFiles,
      progressWidth = Math.ceil(fileWidth * countLoaded +  fileWidth * this.progress);
    var text = "загружено " + countLoaded + " из " + countFiles;
    this.$backProgress.find(".inner").text(text);
    this.$frontProgress.find(".inner").text(text);
    this.$frontProgress.css({ width: progressWidth + "%" });
  } else {
    this.$button.removeClass("processing");
  }
};
/**
 * Запустить цикл загрузки
 */
Uploader.prototype.uploadStart = function (){
  if (!this.uploading) {
    this.uploading = true;
    this.uploadNextFile();
  } else {
    this.updateProgress(); /* чтобы не было -1 в updateProgress */
  }
};
/**
 * Начинает загрузку следующего файла
 */
Uploader.prototype.uploadNextFile = function (){
  if (this.currentFile < this.files.length) {
    this.upload(this.files[this.currentFile]);
    this.currentFile++;
  } else {
    this.uploading = false;
  }
  this.updateProgress();
};
/**
 * Выбор способа загрузки
 * @param file
 */
Uploader.prototype.upload = function (file){
  if (window.FileReader && window.File && window.Blob && window.Image) {
    var reader = new FileReader();
    reader.onloadend = (function (){
      var img = new Image();
      img.onload = (function (){
        var width = img.width,
          height = img.height;
        if (this.opts.maxWidth < width || this.opts.maxHeight < height) {
          var ratio = Math.min(this.opts.maxWidth / width, this.opts.maxHeight / height);
          width *= ratio;
          height *= ratio;
          var canvas = document.createElement("canvas");
          canvas.width = width;
          canvas.height = height;
          var ctx = canvas.getContext("2d");
          ctx.drawImage(img, 0, 0, width, height);
          // генерация base64 кода
          var data = canvas.toDataURL("image/jpeg");
          if (data)
            this.uploadAsBase64(data);
          else
            this.uploadAsFile(file);
        } else {
          this.uploadAsFile(file);
        }
      }).bind(this);
      img.onerror = (function (){
        this.uploadAsFile(file);
      }).bind(this);
      img.src = reader.result;
    }).bind(this);
    reader.readAsDataURL(file);
  }
  else {
    this.uploadAsFile(file);
  }
};
/**
 * Загрузка через file
 * @param file
 */
Uploader.prototype.uploadAsFile = function (file){
  this.xhr.upload.addEventListener("progress", function (event){
    this.progress = event.loaded / event.total;
    this.updateProgress();
  }.bind(this), false);
  this.xhr.open("post", "/act/uploader.php?act=upload_image_file&hash=" + params.hash, true);
  this.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  this.xhr.setRequestHeader("Content-Type", "application/octet-stream");
  this.xhr.onreadystatechange = this.uploaded.bind(this);
  this.xhr.send(file);
};
/**
 * Загрзка через base64
 * @param data
 */
Uploader.prototype.uploadAsBase64 = function (data){
  this.xhr.upload.addEventListener("progress", (function (event){
    this.progress = event.loaded / event.total;
    this.updateProgress();
  }).bind(this), false);
  this.xhr.open("post", "/act/uploader.php?act=upload_image_base64&hash=" + params.hash, true);
  this.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  this.xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  this.xhr.onreadystatechange = this.uploaded.bind(this);
  this.xhr.send("image=" + data);
};
/**
 * Файл загружен
 */
Uploader.prototype.uploaded = function (){
  if (this.xhr.readyState === 4) {
    if (this.xhr.status === 200) {
      var data = $.parseJSON(this.xhr.responseText);
      this.addThumb({id: data.id, thumb: data.thumb});
    } else {
      new MessageBox({ message: "При загрузке фотографии произошла ошибка." });
    }
    this.progress = 0;
    this.uploadNextFile();
  }
};
/**
 * Добавление изображения в загуженные
 * @param info
 */
Uploader.prototype.addThumb = function (info){
  var $img = $("<div class='cover'>" +
    "<img src='" + info.thumb + "' /><a class='remove'></a>" +
    "<input type='hidden' name='uploader[]' value='" + info.id + "' />" +
  "</div>");
  var tooltip = new Tooltip($img.find("a"), { message: "Открепить", width: "80" });
  $img.on("click", function (){
    PhotoViewer.show({ photo: info.id, object: params.objectId, type: params.objectType, hash: params.hash });
  });
  $img.find("a").on("click", function(event){
    $(this).closest(".cover").remove();
    tooltip.remove();
    event.stopPropagation();
  });
  this.$wrap.find(".uploaded").append($img);
};
/**
 * Идёт ли обработка
 */
Uploader.prototype.isBusy = function (){
  return this.uploading;
};

/**
 * Поиск фотографий
 * @constructor
 */
var FindPhotos = function(uploader){
  this.uploader = uploader;
  this.xhr = null;
  this.$loading = $("#find_photos_loading");
};
/**
 * Начать поиск
 * @param city
 * @param street
 * @param house
 */
FindPhotos.prototype.find = function (city, street, house){
  this.abort();
  this.$loading.show();
  this.xhr = $.ajax({
    url: '/act/geo.php?act=get_images',
    method: 'post',
    data: {
      hash: params.hash,
      city: city,
      street: street,
      house: house
    },
    dataType: "json",
    success: function (data){
      this.xhr = null;
      this.$loading.hide();
      data.images.forEach(function(image){
        this.uploader.addThumb({id: image.id, thumb: image.thumb});
      }.bind(this));
    }.bind(this),
    error: function(){
      new MessageBox({ message: "При поиске фотографий произошла ошибка." });
    }
  });
};
/**
 * Прервать поиск
 */
FindPhotos.prototype.abort = function (){
  if (this.xhr) {
    this.xhr.abort();
    this.xhr = null;
  }
  this.$loading.hide();
};
/**
 * Идёт ли обработка
 */
FindPhotos.prototype.isBusy = function (){
  return !!this.xhr;
};