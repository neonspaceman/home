<script>
  "use strict";
  $(function (){
    $("#form_user_login").ajaxForm({
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
              case "code is empty":
              case "incorrect code":
                new MessageBox({ message: "Код доступа не введён или введён некорректно." });
                break;
              default:
                new MessageBox({ message: "При входе произошла ошибка, обновите страницу и повторите попытку." });
            }
          });
          Common.setButtonLoading($form.find("[type='submit']"), false);
        }
      },
      error: function(){
        new MessageBox({ message: "При входе произошла ошибка, обновите страницу и повторите попытку." });
      }
    });
  });
</script>
<div class="popup_html">
  <div class="popup_body">
    <div class="popup_header">
      <div class="popup_header_inner">Вход в систему</div>
      <a class="popup_close">закрыть</a>
    </div>
    <div class="popup_content">
      <form id="form_user_login" action="/act/user.php?act=login" method="post">
        <label>Код доступа</label>
        <input class="input" name="code" type="text" />
        <div class="button_wrap">
          <button class="button" type="submit"><span class="button_caption">Войти</span></button>
          <button class="button link">Как получить доступ?</button>
        </div>
      </form>
    </div>
  </div>
</div>