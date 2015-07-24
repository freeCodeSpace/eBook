<?php 
require_once('..\lib\Lib_PaginatorAdm.php'); //Вызов ф-ла библ. ф-ий
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
				<?php
				include('..\blocks\menuBooks.php');
				?>
			</div>

		 <!--Контент-->
			<div id="rightBox">
				<div id="centerCont">

					<?php
					if( isset( $_GET['list'] ) ) {
						Lib_PaginatorAdm::getBooksCatalog( $_GET['list'] - 1 );
					} else {
						Lib_PaginatorAdm::getBooksCatalog(0);
					}
					if( $_GET['addRow']==true ) { //если страница была вызвана, успешным добавлением новой записи
						//осуществить переход в конец страницы пагинации (что бы сразу увидеть ту запись, которую только добавили)
						Lib_PaginatorAdm::goLastPage();
					}
					?>

				</div>
			</div>
		</div>

	</DIV>
</BODY>
</HTML>