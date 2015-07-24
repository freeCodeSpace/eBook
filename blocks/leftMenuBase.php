<?php
require_once( "../lib/Lib_HighLightUrl.php");
Lib_HighLightUrl::highLightUrl(); //Вызовем ф-ию определения подсветки п. меню
?>
 
 <!--Контейнер для левого блока-->
	<DIV id="leftBox">
	 <!--Меню-->
		<div id="menu">			
			<a href="../index.php" class="urlMenu urlMenuMain">Главная</a>
			<div id="headMenu">Категории Книг:
				<a href="bookCatalog.php?category=1" class="urlMenu categoryMenu<?=Lib_HighLightUrl::$UrlMenuArr[0] ?>">HTML&CSS</a>
				<a href="bookCatalog.php?category=2" class="urlMenu categoryMenu<?=Lib_HighLightUrl::$UrlMenuArr[1] ?>">jS&jQ</a>
				<a href="bookCatalog.php?category=3" class="urlMenu categoryMenu<?=Lib_HighLightUrl::$UrlMenuArr[2] ?>">PHP&MySQL</a>
				<a href="bookCatalog.php?category=4" class="urlMenu categoryMenu<?=Lib_HighLightUrl::$UrlMenuArr[3] ?>">Все категории</a>
			</div>
		</div>
	</DIV>