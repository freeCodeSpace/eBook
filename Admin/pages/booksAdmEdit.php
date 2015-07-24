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
					$refPage = Lib_BookCatalogAdmEdit::clearPageRef( $_SERVER['HTTP_REFERER'] );						
					//если реферальная страница, либо она же, но с параметром пагинации (пропущенная через фильтр имени) РАВНА "booksAdm.php"
					if( (basename($_SERVER['HTTP_REFERER']) == "booksAdm.php") || ($refPage == "booksAdm.php") ) {
						Lib_BookCatalogAdmEdit::putUrlInFile( $_SERVER['HTTP_REFERER'] ); //запомним с какой страницы мы пришли положив в файл
					}

					if( isset($_GET['id']) ) {
						Lib_BookCatalogAdmEdit::editRowFromBooks( $_GET['id'] );
					} else {
						Lib_BookCatalogAdmEdit::updateBook();
					}
					?>

				</div>
			</div>
		</div>

	</DIV>
</BODY>
</HTML>