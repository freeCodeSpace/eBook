<?php 
require_once('..\lib\Lib_Paginator.php'); //Вызов ф-ла библ. ф-ий
require_once('..\lib\Lib_Basket.php'); //Вызов ф-ла библ. ф-ий
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
			<span id="basketHead">корзина:</span>
		</center>
	</div>
 <!--Левое Меню-->
	<?php
	include('..\blocks\leftMenuBase.php');
	?>
 <!--Контейнер для правого блока(Контента)-->

	<DIV id="rightBox">
		<div id="content">

		 <!--Контент страницы Корзины-->
			<?php
		  //Реализация запоминания реф. ссылки в переменной сессии
			$refPage = Lib_Basket::clearPageRef( $_SERVER['HTTP_REFERER'] );
			$selfPage = 'inBasket.php';
			if( ($selfPage != $refPage) && !empty($refPage) ) {//если текFileName НЕРАВЕН отфильт. рефURL
			 //и не пуста (для блок. удаления по (?clear=*)  с возвратом на себя)
				$_SESSION["startURL"] = $_SERVER['HTTP_REFERER']; //запоминаем реф. ссылку в сессию
			}

			if( isset($_GET['clear']) ) { //если страница была перезапрошена (self)
				if( $_GET['clear'] == 'allBasket' ) { //очистка (всей) корзины сессии
					Lib_Basket::clearBasket( $_GET['clear'], 's' ); //т.к. строка
				} else { //удаление конкретного товара
					Lib_Basket::clearBasket( $_GET['clear'], 'i' ); //т.к. число
				}
			} else { //если пришли не с self страницы
				Lib_Basket::getSesBasket();
			}			
			?>
 
		</div>
	</DIV>
	
</DIV>
</BODY>
</HTML>