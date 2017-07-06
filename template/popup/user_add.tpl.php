<script>
  "use strict";
  $(function (){
    $("#form_user_add").ajaxForm({
      dataType: "json",
      beforeSubmit: function(arr, $form, options){
        Common.setButtonLoading($form.find("[type='submit']"), true);
      },
      success: function(data, status, xhr, $form){
        if (data.status === "success") {
          location.reload();
        } else {
          data.message.forEach(function(message){
            switch(message) {
              case "name is empty":
                new MessageBox({ message: "Введите имя пользователя." });
                break;
              default:
                new MessageBox({ message: "При добавлении пользователя запроса произошла ошибка, обновите страницу и повторите попытку." });
            }
          });
          Common.setButtonLoading($form.find("[type='submit']"), false);
        }
      },
      error: function(){
        new MessageBox({ message: "При добавлении пользователя запроса произошла ошибка, обновите страницу и повторите попытку." });
      }
    });
  });
</script>
<div class="popup_html">
  <div class="popup_body">
    <div class="popup_header">
      <div class="popup_header_inner">Добавить нового пользователя</div>
      <a class="popup_close">закрыть</a>
    </div>
    <div class="popup_content">
      <form id="form_user_add" action="/act/user.php?act=add" method="post">
        <label>ФИО</label>
        <input class="input" type="text" name="name" />
        <div class="button_wrap">
          <button class="button" type="submit"><span class="button_caption">Добавить</span></button>
        </div>
      </form>
    </div>
  </div>
</div>