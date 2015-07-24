<?php 
require_once('..\lib\Lib_OrderClientsAdm.php'); //Вызов ф-ла библ. ф-ий
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
	<META charset="utf-8">
	<TITLE>Admin BookShop</TITLE>
	<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen, projection" />
	<link rel="shortcut icon" href="../img/favicon.ico">
</HEAD>
<BODY>	
	<DIV id="box">

	 <!--Шапка-->
		<?php
		include('..\blocks\header.php');
		?>

	 <!--Центральная часть-->
		<div id="contentBox">

		 <!--Меню-->
			<div id="leftBox">
				<a href="../../index.php" class="MenuFirstButt">На сайт</a>
				<div id="SubMenu">
				 <!--Обработка книг-->
					<a href="<?= $_SERVER['PHP_SELF']; ?>" class="SubMenuTab  addRow">Покупатели</a><br>
				</div>
			</div>
		 <!--Контент-->
			<div id="rightBox">
				<div id="centerCont">

					<?php
					Lib_OrderClientsAdm::getClientOrders();
					?>

				</div>
			</div>
		</div>

	</DIV>
</BODY>
</HTML>