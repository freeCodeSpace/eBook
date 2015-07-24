<?php
require_once('Lib_DB.php'); //Вызов ф-ла подключения к БД
/* ===================================================================
						Реализация Постраничной разбивки:
=================================================================== */
class Lib_PaginatorAdm {

 //тут будет сохранятся ссылка на самую последнюю страницу (для возврата к последн. записи со стр. добавления)
	static $LastUrlList = "booksAdm.php"; //по умолчанию она будет базовой страницей

 // > Фильтрация данных
	static function clearData($data, $type="s") { //по умолч. string
		global $mysqli; //Используем глобальный объект
	  switch ($type) {
	    case 's': //для строк
				$data = $mysqli->real_escape_string( trim( strip_tags($data)) ); //вместо addslashes
	      break;
	    case 'i': //для integer
	      $data = abs( (int)$data );
	      break;
	    case 'sf': //для строк но для сохранения в файл
	      $data = trim( strip_tags($data) );
	      break;
	    case 't': //для контента, так же теги
				$data = $mysqli->real_escape_string( trim($data) );
	      break;
	  }
	  return $data;
	}

 // > Выборка всех книг
	static function getBooksCatalog($page) {
		$page = self::clearData($page, 'i');
		global $mysqli; //Используем глобальный объект
	 // Контент с n-й, 10-ть записей
		$pageRow = $page*10; //с учетом разбивки по 10-ть книг

	 //Кол-во записей всех книг
		$queryCount = "SELECT count(*) FROM books";
		$queryS = "SELECT id, imgName, bookName, bookAuthor, bookPrice, bookClass
							 FROM books LIMIT $pageRow, 10";

		$resultCount = $mysqli->query($queryCount);
		$selectAllQueryCount = $resultCount->fetch_row(); //Сохр. рез. запроса в переменной (массив)
		$resultCount->free(); //Освобождаем память занятую результатами запроса

	 //Распечатка пагинации
		if( ($page*10) <= $selectAllQueryCount[0] ) {
				$resultS = $mysqli->query($queryS);
			 //Передаем рез. выборки в ф-ию Конвертации в массив
				$selectAllQueryArr = self::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
				$resultS->free(); //Освобождаем память занятую результатами запроса					
			 //Печатаем каталог книг передавая массив (10-ть книг)
				self::PrnBooks($selectAllQueryArr);
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

 // > Конвертация SQL-выборки в массив
	static function selectQuery2Arr($result) {
		$selectQueryArr = array(); //Если пусто, передадим пустой массив
			while( $row = $result->fetch_assoc() ) { //Конвертируем рез. SQL-выборки в массив (что б отвязаться от привязки к БД)
				$selectQueryArr[] = $row;
			}
		return $selectQueryArr; //возвращаем массив
	}

 /*____________________________Вывод каталога товаров____________________________*/
 // > Вывод таблицы каталога товаров (книг)
	private function PrnBooks($selectQueryArr) {
		self::echoTableBox("start"); //Печать шапки таблицы
		foreach ($selectQueryArr as $inArr) {
		 //Значения ячеек будут передаваться ф-ией в ф-ю печати
		  self::echoBookBox( $inArr['id'], $inArr['imgName'], $inArr['bookName'], $inArr['bookAuthor'], $inArr['bookPrice'], $inArr['bookClass'] );
		}
		self::echoTableBox("end"); //Печать закрывающего тега
	}

 // > Печать шапки таблицы
	private function echoTableBox($str) {
		if( $str == "start" ) {
			echo '
					<table id="AdmTable" align="center" cellpadding="5" cellspacing="0">
						<caption><b>База : </b> <i>Книги</i></caption>
						<thead>
						  <tr>
						    <td id="tabNum">ID</td>
						    <td class="tabRow">Имя Картинки:</td>
						    <td>Имя Книги:</td>
						    <td>Авторы:</td>
						    <td id="tabPrice">Цена:</td>
						    <td class="tabRow">Тип Каталога:</td>
						    <td class="tabRow">Редактировать:</td>
						    <td class="tabRow">Удалить:</td>
						  </tr>
				    </thead>
			';
		} else if( $str == "end" ) {
			echo '
					</table>
			';
		}
	}

 // > Печать Таблицы Товара(Книг) с учетом TAB-отступов в оформлении кода ( форматированный результирующий код (по Ctrl+U) )
	private function echoBookBox( $id, $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass ) {
		echo '
						<tr>
							<td>' . $id . '</td>
							<td>' . $imgName . '</td>
							<td>' . $bookName . '</td>
							<td>' . $bookAuthor . '</td>
							<td>' . $bookPrice . '</td>
							<td>' . $bookClass . '</td>
							<td><a href="booksAdmEdit.php?id=' . $id . '" class="editUrl" title="Редактировать"></a></td>
							<td><a href="booksAdmDel.php?id=' . $id . '&img=' . $imgName . '" class="delUrl" title="Удалить"></a></td>
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
		$pagRightNum = ceil( ( $pagRightNum ) / 10 ); //Преобразуем число строк всего, в постраничн. разбивку
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
		if( $pagRightNum > 1 ) { self::$LastUrlList = $hrefLast; } //сохранение ссылки на последнюю страницу

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

 // > Переход к последней странице пагинации
	static function goLastPage() {
		echo '<meta http-equiv="Refresh" content="0; URL=' . self::$LastUrlList . '">';
	}

}