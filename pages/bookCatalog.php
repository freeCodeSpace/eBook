<?php 
require_once('..\lib\Lib_Paginator.php'); //Вызов ф-ла библ. ф-ий
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
	<?php
	include('..\blocks\header.php');
	?>
 <!--Левое Меню-->
	<?php
	include('..\blocks\leftMenuBase.php');
	?>
 <!--Контейнер для правого блока(Контента)-->

	<DIV id="rightBox">
		<div id="content">

		 <!--Контент страницы Каталога-->
		  <!--Вывод Каталога Товаров и постраничной разбивки (с учетом 4-х блоков на странице)-->
			<?php
			if( isset( $_GET['category'] ) && !empty( $_GET['category'] ) ) { //если страница вызвана с передачей category
				Lib_Paginator::getBooksCatalog();
			} else { //на случай (если набрана без category или криво), обновим стр. на 1-ю категорию.
				echo '<meta http-equiv="Refresh" content="0; URL=' . $_SERVER['PHP_SELF'] . '?category=1">';
				exit();				
			}
			?>
 
		</div>
	</DIV>
	
</DIV>
</BODY>
</HTML>