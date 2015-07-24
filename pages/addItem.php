<?php
require_once('..\lib\Lib_Paginator.php'); //Вызов ф-ла библ. ф-ий

if( isset($_GET['id']) ) { 
	$bookId = Lib_Paginator::clearData($_GET['id'], 'i');
	addOrderInSes($bookId); //запомним в сессии	 
	header('Location: ' . $_SERVER['HTTP_REFERER'] . ''); //Переадресуем обратно
}

// > Сохранение ID товара в массиве сессии
function addOrderInSes($bookId) {
	if( isset( $_SESSION["BasketArr"] ) ) { //Если установлена сессия с данными (с сериализованным массивом)
		$_SESSION["BasketArrSize"] = $_SESSION["BasketArrSize"] + 1; //тут храним размер корзины (что бы проще считывать каждый раз)
		$BasketArr = unserialize( $_SESSION["BasketArr"] ); //распаковали прошлый массив из сессии
		$BasketArr[] = $bookId; //добавили ID новой книги в конец массива
		$str = serialize($BasketArr); //Упаковываем массив в строку
		$_SESSION["BasketArr"] = 	$str; //Ложим данные обратно в сессию
	} else { //если массива еще нет
		$_SESSION["BasketArrSize"] = 1; //присвоим 1
		$BasketArr = array(); //создаем массив (и аналогично сохр. данные)
		$BasketArr[] = $bookId;
		$str = serialize($BasketArr);
		$_SESSION["BasketArr"] = 	$str;
	}
}