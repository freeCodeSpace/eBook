<?php
require_once('..\lib\Lib_BookCatalogAdmAdd.php'); //Вызов ф-ла обработки
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
			<div id="rightBox" class="AddBoxColor">
				<div id="centerCont" class="AddBoxColor">

					<?php
					if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
						Lib_BookCatalogAdmAdd::addBook();
					}
					Lib_BookCatalogAdmAdd::PrnAddBook();
					?>

				</div>				
			</div>
		</div>

	</DIV>
</BODY>
</HTML>