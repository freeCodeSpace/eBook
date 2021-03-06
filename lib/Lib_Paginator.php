<?php
session_start(); //запуск сессии реализуется тут (т.к. данная библиотека, подключается чаще всего)
require_once('Lib_DB.php'); //Вызов ф-ла подключения к БД
/* ===================================================================
			Реализация Постраничной разбивки и вывод блоков контента
=================================================================== */
class Lib_Paginator {

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

 // > Возврат значения категории (+добавочный str), для преобразования URL
	private function getCateg() {
		return "?category=" . self::clearData( $_GET['category'], 'i' );
	}

 // > Выборка книг
	static function getBooksCatalog() {
		$nCatalog = "Catalog" . self::clearData( $_GET['category'], 'i' ); //отфильтруем
		if( isset( $_GET['list'] ) ) { //если есть значение пагинации
			if( 0 < $_GET['list'] != 0 ) { //если параметры лежат в 'нормальном диапазоне'
				$page = self::clearData( $_GET['list'], 'i' ) - 1;
			} else { //на случай, если будет пользов. ввод некорректных параметров GET
				echo '<center class="redirectMsg">Нечего вводить в URL непонятные цифры!</center>';
				echo '<meta http-equiv="Refresh" content="2; URL='.$_SERVER['PHP_SELF'].self::getCateg().'">';
				exit();
			}
		} else { //если его нет, установим его в 0
			$page = 0;
		}
		global $mysqli; //Используем глобальный объект
	 //Контент с n-й, 4-е записи
		$pageRow = $page*4; //с учетом разбивки по 4-е книги

	 //Анализ типа запроса каталога (Весь товар таблицы или по Каталогу - п.Меню)
		if( $nCatalog == 'Catalog4' ) { //Вывод всего каталога
 		 //Кол-во записей всех книг
			$queryCount = "SELECT count(*) FROM books";
			$queryS = "SELECT id, imgName, bookName, bookAuthor, bookPrice
								 FROM books LIMIT $pageRow, 4";
		} else { //Для category=1..3
			$queryS = "SELECT id, imgName, bookName, bookAuthor, bookPrice
								 FROM books WHERE bookClass='$nCatalog' LIMIT $pageRow, 4";
 		 //Кол-во записей всех книг
			$queryCount = "SELECT count(*)
								 FROM books WHERE bookClass='$nCatalog'";
		}

		$resultCount = $mysqli->query($queryCount);
		$selectAllQueryCount = $resultCount->fetch_row(); //Сохр. рез. запроса в переменной
		$resultCount->free(); //Освобождаем память занятую результатами запроса

	 //Распечатка пагинации
		if( ($page*4) <= $selectAllQueryCount[0] ) {
			$resultS = $mysqli->query($queryS);		
		 //Передаем рез. выборки в ф-ию Конвертации в массив
			$selectAllQueryArr = self::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
			$resultS->free(); //Освобождаем память занятую результатами запроса				
		 //Печатаем каталог книг передавая массив (4-е книги)
			self::PrnBooks($selectAllQueryArr);
			if( $selectAllQueryCount[0] >= 5 ) { //Выводим пагинацию, если есть контент
			 //Печать пагинации:
				self::PrnPaginator( $page, $selectAllQueryCount[0] );
			} else { /* Пагинация не печатается */ }
		} else {
			echo '<center class="redirectMsg">Нечего вводить в URL непонятные цифры!</center>';
			echo '<meta http-equiv="Refresh" content="2; URL='.$_SERVER['PHP_SELF'].self::getCateg().'">';
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
 // > Вывод каталога товаров (книг), с учетом распечатки отступов между ними
	private function PrnBooks($selectQueryArr) {
		$i = 0; //"Внешняя" (от foreach) переменная счетчик, для определ. отступов

		if( count($selectQueryArr) == 2 ) { //Если только 2-а блока (не печатать гор. разделитель)
			foreach ($selectQueryArr as $inArr) {
			  self::echoBookBox( $inArr['id'], $inArr['imgName'], $inArr['bookName'], $inArr['bookAuthor'], $inArr['bookPrice'] );
			 //Добавление блоков разделителей
				if( $i==0 ) {
					self::echoBorderSepar('V');
				}
				$i++;
			}
		} else { // Если блоков (1,3,4) гор. разделитель не будет продублирован
			foreach ($selectQueryArr as $inArr) {
			 //Значения ячеек будут передаваться ф-ией в ф-ю печати
			  self::echoBookBox( $inArr['id'], $inArr['imgName'], $inArr['bookName'], $inArr['bookAuthor'], $inArr['bookPrice'] );
			 // Добавление блоков разделителей
				if( $i==0 || $i==2 ) {
					self::echoBorderSepar('V');
				} elseif( $i==1 ) {
					self::echoBorderSepar('H');
				}
				$i++;
			}
		}

	}

 // > Печать Блоков (с учетом TAB-отступов в оформлении кода, форматированный результирующий код (по Ctrl+U) )
	private function echoBookBox( $id, $imgName, $bookName, $bookAuthor, $bookPrice ) {
		echo '
			<DIV class="bookConteiner">
				<div class="bookBox">
					<div class="bookImgBox">
						<img src="../Img/Catalog/' . $imgName . '.jpg" alt="" class="ImgBook"><!--Картинка к Книге-->
					</div>
					<div class="bookContentBox">
						<div class="bookInHeadBlock">
							<span class="bookName">' . $bookName . '</span><br><!--Название книги-->
							<span class="bookAuthor">' . $bookAuthor . '</span><br><!--Авторы книги-->
						</div>
						<div class="bookMiddleBlock">
							Оценка Отзывов:
							<span class="bookUsersRating">#</span><br>
							<i>Комментариев:</i>
							<span class="bookCommentsValue">#</span><br>
							<!--Цена книги-->
							<i>Цена:</i>
							<span class="bookPrice">' . $bookPrice . '</span>грн<br>
						</div>
						<div class="bookDownBlock">
						 <!--Ссылка Добавить в корзину-->
							<a href="addItem.php?id=' . $id . '" class="urlBookDown urlToBasket">Заказать</a>
						 <!--Ссылка заказать-->
							<a href="bookDetails.php' . self::getCateg() . "&id=" . $id . '" class="urlBookDown bookDetails">Подробнее</a>
						</div>
					</div>
				</div>
			</DIV>			
		';
	}

 // > Печать разделителя (так же учет отступов в результ. html коде):
	private function echoBorderSepar($type) {
		if( $type == 'V' ) {
			echo '
			<!--Вертикальный разделитель (между блоками)-->
			<div class="vBorderSepar"></div>
			';
		} elseif ( $type == 'H' ) {
			echo '
			<!--Горизонтальный разделитель (между блоками)-->
			<div class="hBorderSepar"></div>
			';
		}
	}

 /*____________________________Вывод пагинатора____________________________*/
 // > Печать пагинатора:
	private function PrnPaginator( $pagCenterNum, $pagRightNum ) {		
		$pagRightNum = ceil( ( $pagRightNum ) / 4 ); //Преобразуем число строк всего, в постраничн. разбивку
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
		self::echoBorderSepar('H');
		$hrefFirst = basename($_SERVER['PHP_SELF']) . self::getCateg(); //Первая страница
		if( $pagCenterNum == 2 ) { //учет 1-й страницы (для листания вперед)
			$hrefPrev = basename($_SERVER['PHP_SELF']) . self::getCateg(); //Предыдущая страница (без вывода счетч. пагин.)
		} else { //если это не 1-я страница, добавится приставка пагинации
			$hrefPrev = basename($_SERVER['PHP_SELF']) . self::getCateg() . "&list=" . ($pagCenterNum - 1);
		}		
		$hrefNext = basename($_SERVER['PHP_SELF']) . self::getCateg() . "&list=" . ($pagCenterNum + 1); //Следующая страница
		$hrefLast = basename($_SERVER['PHP_SELF']) . self::getCateg() . "&list=" . $pagRightNum; //Последняя страница

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
	
}