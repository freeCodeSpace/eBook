<?php
require_once('Lib_PaginatorAdm.php'); //Вызов ф-ла библ. ф-ий
/* ===================================================================
		 Реализация Вывода каталога заказов с постраничной разбивкой:
=================================================================== */
class Lib_OrderCatalogAdm {
	
	static $i; //счетчик наименований товара (для нумерации таблицы)		
	static $name;

 // > Выборка всех заказов
	static function getOrdersCatalog($page) {
		$page = Lib_PaginatorAdm::clearData($page, 'i');
		global $mysqli; //Используем глобальный объект
	 //Контент с n-й, 10-ть записей
		$pageRow = $page*10; //с учетом разбивки по 10-ть записей
		self::$i = $pageRow; //занесем в переменную

	 //Кол-во записей заказов
		$queryCount = "SELECT count(*)
							 FROM orders";
		$queryS = "SELECT id, bookName, bookAuthor, bookPrice, quantity, datetime, customerName, address, telefon
							 FROM orders LIMIT $pageRow, 10";

		$resultCount = $mysqli->query($queryCount);
		$selectAllQueryCount = $resultCount->fetch_row(); //Сохр. рез. запроса в переменной (массив)
		$resultCount->free(); //Освобождаем память занятую результатами запроса

	 //Распечатка пагинации
		if( ($page*10) <= $selectAllQueryCount[0] ) {
				$resultS = $mysqli->query($queryS);
			 //Передаем рез. выборки в ф-ию Конвертации в массив
				$selectAllQueryArr = Lib_PaginatorAdm::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
				$resultS->free(); //Освобождаем память занятую результатами запроса					
			 //Печатаем каталог заказов передавая массив (10-ть заказов)
				self::PrnOrders($selectAllQueryArr);
			if( $selectAllQueryCount[0] >= 11 ) { //Выводим пагинацию, если есть контент
			 //Печать пагинации:
				self::PrnPaginator( $page, $selectAllQueryCount[0] );
			} else { /* Пагинация не печатается */ }
		} else {
			echo '<center class="redirectMsg">Нечего вводить в URL непонятные цифры!</center><br>';
			echo '<meta http-equiv="Refresh" content="3; URL='.$_SERVER['PHP_SELF'].'">';
			exit();
		}

		$mysqli->close(); //Закр. соединение
	}


 /*____________________________Вывод каталога заказов____________________________*/
 // > Вывод таблицы каталога заказов
	private function PrnOrders($selectQueryArr) {
		self::echoTableBox("start"); //Печать шапки таблицы
		foreach ($selectQueryArr as $inArr) {
		 //Значения ячеек будут передаваться ф-ией в ф-ю печати
		  self::echoOrderBox( $inArr['id'], $inArr['bookName'], $inArr['bookAuthor'], $inArr['bookPrice'], $inArr['quantity'], $inArr['datetime'], $inArr['customerName'], $inArr['address'], $inArr['telefon'] );
		}
		self::echoTableBox("end"); //Печать закрывающего тега
	}

 // > Печать шапки таблицы
	private function echoTableBox($str) {
		if( $str == "start" ) {
			echo '
					<table id="AdmTable" align="center" cellpadding="5" cellspacing="0">
						<caption><b>База : </b> <i>Заказы</i></caption>
						<thead>
						  <tr>
						  	<td id="tabNum">№</td>
						    <td>Имя Книги:</td>
						    <td>Авторы:</td>
						    <td id="tabPrice">Цена:</td>
						    <td id="tabQuant">Кол-во:</td>
						    <td>Покупатель:</td>
						    <td>Адресс:</td>
						    <td>Телефон:</td>
						    <td>Дата Заказа:</td>
						    <td class="tabDelOrd">Удалить:</td>
						  </tr>
				    </thead>
			';
		} else if( $str == "end" ) {
			echo '
					</table>
			';
		}
	}

 // > Печать Таблицы Заказов
	private function echoOrderBox( $id, $bookName, $bookAuthor, $bookPrice, $quantity, $datetime, $customerName, $address, $telefon ) {
		echo '
						<tr>
							<td>' . self::getNumeration() . '</td>
							<td>' . $bookName . '</td>
							<td>' . $bookAuthor . '</td>
							<td>' . $bookPrice . '</td>
							<td>' . $quantity . '</td>
							<td>' . $customerName . '</td>
							<td>' . $address . '</td>
							<td>' . $telefon . '</td>
							<td>' . date('d-m-Y H:i', $datetime) . '</td>
							<td><a href="#?id=' . $id . '" class="delUrl" title="Удалить"></a></td>
						</tr>
		';
	} 

 // > Печать разделителя
	private function echoHBorderSepar() {
		echo '
			<!--Горизонтальный разделитель (между блоками)-->
			<div class="hBorderSepar"></div>
		';
	}

 /*____________________________Вывод пагинатора____________________________*/
 // > Печать пагинатора:
	private function PrnPaginator( $pagCenterNum, $pagRightNum ) {		
		$pagRightNum = ceil( ( $pagRightNum ) / 10 ); //Преобразуем число строк всего В постраничн. разбивку
		settype($pagRightNum, "integer");
		$pagCenterNum = $pagCenterNum + 1; //(+1) т.к. счет начинается с 0

		if( ($pagCenterNum != 1) && ($pagCenterNum != $pagRightNum) ) { //Если это не 1-я и не Последняя стр
		 //Вызывается обычный режим печати
			$pagLeftNum = 1;
			self::echoPaginator( $pagLeftNum, $pagCenterNum, $pagRightNum, "InsidePage"); //+передача параметра тип-страницы
		} else {

			if( $pagCenterNum == 1 ) { //Если запрашиваемая стр. первая
		 	 //Вызывается другой режим печати
				$pagLeftNum = "&nbsp"; //Тогда первую цифру не выводим (печать пробела)
				self::echoPaginator( $pagLeftNum, $pagCenterNum, $pagRightNum, "FirstPage"); //+передача параметра тип-страницы

			}	
			elseif( $pagCenterNum == $pagRightNum ) { //Если запрашиваемая стр. последняя
		 	 //Вызывается другой режим печати
				$pagRightNum = "&nbsp"; //Тогда последнюю цифру не выводим (печать пробела)
				$pagLeftNum = 1;
				self::echoPaginator( $pagLeftNum, $pagCenterNum, $pagRightNum, "LastPage"); //+передача параметра тип-страницы
			} else { /* пагинатора не будет */}

		}
	}

 // > Печать по условиям ( с учетом TAB-отступов в оформлении кода ( форматированный результирующий код (по Ctrl+U) )
	private function echoPaginator( $pagLeftNum, $pagCenterNum, $pagRightNum, $type ) {
		self::echoHBorderSepar();
		$hrefFirst = basename($_SERVER['PHP_SELF']); //Первая страница
		if( $pagCenterNum == 2 ) { //учет 1-й страницы (для листания вперед)
			$hrefPrev = basename($_SERVER['PHP_SELF']); //Предыдущая страница (без вывода счетч. пагин.)
		} else { //если это не 1-я страница, добавится приставка пагинации
			$hrefPrev = basename($_SERVER['PHP_SELF']) . "?list=" . ($pagCenterNum - 1);
		}
		$hrefNext = basename($_SERVER['PHP_SELF']) . "?list=" . ($pagCenterNum + 1); //Следующая страница
		$hrefLast = basename($_SERVER['PHP_SELF']) . "?list=" . $pagRightNum; //Последняя страница

	 //Печать пагинатора - 1-й страницы ( 1-й цифры нет, она не ссылка, стиль офр.2; Левая стрелка не ссылка; стиль офр. 2)
		if( $type == "FirstPage") {		
		echo '
			<DIV id="paginatorBox">
				<div id="paginatorPanel">
					<span id="paginatorPanelLeftNum" class="paginatorNum paginatorNumNone">' . $pagLeftNum . '</span>
					<span id="paginatorPanelPrevArr" class="paginatorNum paginatorArrNone">◄</span>
					<span id="paginatorPanelCenterNum" class="paginatorNum">' . $pagCenterNum . '</span>
					<a href="' . $hrefNext . '" id="paginatorPanelNextArr" class="paginatorNum paginatorNumHover" title="Следующая">►</a>
					<a href="' . $hrefLast . '" id="paginatorPanelRightNum" class="paginatorNum paginatorNumHover" title="Последняя">' . $pagRightNum . '</a>
				</div>
			</DIV>
		';
		}
	 //Печать пагинатора - внутр. страниц ( обычный стиль вывода )
		elseif( $type == "InsidePage") {		
		echo '
			<DIV id="paginatorBox">
				<div id="paginatorPanel">
					<a href="' . $hrefFirst . '" id="paginatorPanelLeftNum" class="paginatorNum paginatorNumHover" title="Первая">' . $pagLeftNum . '</a>
					<a href="' . $hrefPrev . '" id="paginatorPanelPrevArr" class="paginatorNum paginatorNumHover" title="Предыдущая">◄</a>
					<span id="paginatorPanelCenterNum" class="paginatorNum">' . $pagCenterNum . '</span>
					<a href="' . $hrefNext . '" id="paginatorPanelNextArr" class="paginatorNum paginatorNumHover" title="Следующая">►</a>
					<a href="' . $hrefLast . '" id="paginatorPanelRightNum" class="paginatorNum paginatorNumHover" title="Последняя">' . $pagRightNum . '</a>
				</div>
			</DIV>
		';
		}
	 //Печать пагинатора - последней страницы ( N-й цифры нет, она не ссылка, стиль офр.2; Правая стрелка не ссылка; стиль офр. 2)
		elseif( $type == "LastPage") {
		echo '
			<DIV id="paginatorBox">
				<div id="paginatorPanel">
					<a href="' . $hrefFirst . '" id="paginatorPanelLeftNum" class="paginatorNum" title="Первая">' . $pagLeftNum . '</a>
					<a href="' . $hrefPrev . '" id="paginatorPanelPrevArr" class="paginatorNum paginatorNumHover" title="Предыдущая">◄</a>
					<span id="paginatorPanelCenterNum" class="paginatorNum">' . $pagCenterNum . '</span>
					<span id="paginatorPanelNextArr" class="paginatorNum paginatorArrNone">►</span>
					<span id="paginatorPanelRightNum" class="paginatorNum paginatorNumNone">' . $pagRightNum . '</span>
				</div>
			</DIV>
		';
		}
	}

 // > Расчет нумерации таблицы заказов (в зависимости от начального значения пагинации)
	private function getNumeration() {
		self::$i++;
		return self::$i;
	}

}