 <!--Хедер/Лого-->
	<div class="header">
		<span class="header text">BoOkS &nbsp &nbsp &nbsp  &nbsp  &nbsp  &nbsp  &nbsp ShOp</span>
	</div>
 <!--Блок Корзины-->
	<div id="regBox">
<?php //Печать корзины в зависимости от ее содержимого
if( $count == 0 ) {
	echo '
		<span id="emptyBasket">В корзине: </a><span class="basketZero">0</span>
	';
} else {
	echo '
		<a href="inBasket.php" id="inBasket">В корзине:</a><span class="bookBasket">' . $count . '</span>
';
}
?>
	</div>