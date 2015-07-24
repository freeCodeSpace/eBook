<?php
require_once('..\lib\Lib_BookCatalogAdmEdit.php'); //Вызов ф-ла обработки
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
					if( isset($_GET['id']) && isset($_GET['img']) ) {
						Lib_BookCatalogAdmEdit::delRowFromBooks( $_GET['id'], $_GET['img'] );
					}
					?>

				</div>				
			</div>
		</div>

	</DIV>
</BODY>
</HTML>