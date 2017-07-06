"use strict";

var Users = {
  addPopup: null,
  editPopup: null
};
Users.init = function(){
  new Pagination($("#pagination"), {
    offset: params.offset,
    onPage: params.recordsOnPage,
    count: params.countRecords
  });
  this.addPopup = new Popup({ class: "message_box", url: "users/add" });
  Timeago.exec();
};
Users.add = function(){
  this.addPopup.show();
};
Users.edit = function(id_user){
  if (!this.editPopup){
    this.editPopup = new Popup({ class: "message_box", url: "users/edit?id=" + id_user });
  } else {
    this.editPopup.setUrl("users/edit?id=" + id_user);
  }
  this.editPopup.show();
};
Users.newCode = function(name, id_user){
  new ConfirmBox({
    message: "Сгенерировать новый код доступа для " + name + "?",
    onClick: function(type){
      if (type === "ok"){
        new LoadingBox();
        $.ajax({
          type: "post",
          url: "/act/user.php?act=new_code",
          data: { id: id_user },
          dataType: "json",
          success: function(){
            location.reload();
          },
          error: function(){
            PopupCollection.pop(); /* remove loading box */
            new MessageBox({ message: "Произошла ошибка, обновите страницу и повторите попытку." });
          }
        });
      }
    }
  })
};
Users.remove = function(name, id_user){
  new ConfirmBox({
    message: "Удалить " + name + "?",
    onClick: function(type){
      if (type === "ok"){
        new LoadingBox();
        $.ajax({
          type: "post",
          url: "/act/user.php?act=remove",
          data: { id: id_user },
          dataType: "json",
          success: function(){
            location.reload();
          },
          error: function(){
            PopupCollection.pop(); /* remove loading box */
            new MessageBox({ message: "Произошла ошибка, обновите страницу и повторите попытку." });
          }
        });
      }
    }
  })
};

$(function(){
  Users.init();
});