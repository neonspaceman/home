<?php
$user = __user::get_instance();
$page = __page::get_instance();
?>
<meta charset="utf-8" />
<!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script><![endif]-->
<?php
$page->render_title();
$page->render_icon();
$page->render_keywords();
$page->render_description();
$page->render_meta();
?>
<link href="/template/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<link href="/template/css/common.css?<?= time() ?>" rel="stylesheet" />
<?php $page->render_styles() ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="/template/js/jquery.form.min.js"></script>
<script>var user = { logged: <?= json_encode($user->get("logged")); ?> };</script>
<script src="/template/js/common.js?<?= time() ?>"></script>
<?php $page->render_scripts() ?>
