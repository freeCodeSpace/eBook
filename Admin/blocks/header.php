<?php
require_once("../lib/Lib_HighLightUrlAdm.php");
Lib_HighLightUrlAdm::highLightUrlAdm(); //Вызовем ф-ию определения подсветки п. меню
?>
		<div id="headerLow">Администраторская Часть</div>	 
		<div id="header">
			<a href="booksAdm.php" class="headerTab headerTabFirst<?=Lib_HighLightUrlAdm::$UrlMenuArrAdm[0] ?>">Книги</a>
			<a href="ordersAdm.php" class="headerTab<?=Lib_HighLightUrlAdm::$UrlMenuArrAdm[1] ?>">Заказы</a>
			<a href="#" class="headerTab<?=Lib_HighLightUrlAdm::$UrlMenuArrAdm[2] ?>">Комментарии</a>
			<a href="#" class="headerTab<?=Lib_HighLightUrlAdm::$UrlMenuArrAdm[3] ?>">Пользователи</a>
		</div>
		