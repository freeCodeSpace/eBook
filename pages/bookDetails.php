<?php 
require_once('..\lib\Lib_BookDetails.php'); //Вызов ф-ла библ. ф-ий
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

		 <!--Контент страницы подробнее-->
			<?php
			if( isset( $_GET['id'] ) ) {
				Lib_BookDetails::RowFromBooks( $_GET['id'] );
			}
			?>
 
		</div>
	</DIV>
	
</DIV>
</BODY>
</HTML>