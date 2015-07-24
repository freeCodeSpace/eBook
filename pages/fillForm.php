<?php 
require_once('..\lib\Lib_Paginator.php'); //Вызов ф-ла библ. ф-ий
require_once('..\lib\Lib_FillForm.php'); //Вызов ф-ла библ. ф-ий
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
	<META charset="utf-8">
	<TITLE>BookShop</TITLE>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="shortcut icon" href="../img/favicon.ico">
</HEAD>
<BODY>
<DIV id="box">
 <!--Шапка-->
	<div class="header">
		<span class="header text">BoOkS &nbsp &nbsp &nbsp  &nbsp  &nbsp  &nbsp  &nbsp ShOp</span>
	</div>
 <!--Блок Корзины-->
	<div id="regBox" style="background: url(../img/2bookBox2.jpg);">
		<center>
			<span id="basketHead">Оформление заказа:</span>
		</center>
	</div>
 <!--Левое Меню-->
	<?php
	include('..\blocks\leftMenuBase.php');
	?>
 <!--Контейнер для правого блока(Контента)-->

	<DIV id="rightBox">
		<div id="content" style="background-color: #F0F0F0;">

		 <!--Контент страницы Заполнения формы-->
			<?php
			if( isset($_SESSION["OrderArr"]) ) { //блокирование заполнения формы (если нет заказов, если пришли по прямой URL)

				if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					Lib_FillForm::CheckForm(); //Обработка формы
				} else {
					Lib_FillForm::PrnForm(); //Вызов печати формы заполнения заказа
				}

			} else { //Перенаправление по умолчанию 
				echo "<center>Заказов нет!</center>";
				echo '<meta http-equiv="Refresh" content="1; URL=bookCatalog.php?category=1">';
				exit();
			}
			?>
 
		</div>
	</DIV>
	
</DIV>
</BODY>
</HTML>