<?php 
$addRow = "";
if( basename($_SERVER['PHP_SELF']) == 'booksAdmAdd.php' ) {
	$addRow = " addRow";
}
?>
				<a href="../../index.php" class="MenuFirstButt">На сайт</a>
			 <!--СубМеню-->
				<div id="SubMenu">
				 <!--Обработка книг-->
					<a href="booksAdmAdd.php" class="SubMenuTab<?=$addRow; ?>">Добавить</a><br>
				</div>