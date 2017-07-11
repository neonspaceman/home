<?php $user = __user::get_instance(); ?>
<header class="header">
  <a href="/" class="logo"></a>
  <div class="action">
    <ul>
      <li><a><i class="fa fa-info-circle" aria-hidden="true"></i>О компании</a></li>
      <li><a><i class="fa fa-rub" aria-hidden="true"></i>Цены</a></li>
      <li><a><i class="fa fa-question-circle-o" aria-hidden="true"></i>Вопросы и ответы</a></li>
      <li><a><i class="fa fa-lock" aria-hidden="true"></i>Получить доступ</a></li>
      <?php if ($user->get("logged")): ?>
      <li><a id="user_action"><i class="fa fa-user" aria-hidden="true"></i><?= text($user->get("name")) ?><i class="fa fa-caret-down right" aria-hidden="true"></i></a></li>
      <?php else: ?>
      <li><a id="user_action" onclick="User.openLoginPopup()"><i class="fa fa-user" aria-hidden="true"></i>Войти</a></li>
      <?php endif; ?>
    </ul>
  </div>
</header><!-- .header-->