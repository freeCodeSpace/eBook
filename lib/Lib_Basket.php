<?php
require_once('Lib_Paginator.php'); //Вызов ф-ла подключения к библиотеке Пагинатора
/* ===================================================================
						Реализация вывода пользовательской Корзины:
=================================================================== */
class Lib_Basket {
	
	static $orderArr = array(); //2-у мерный масс. хранения информ. о заказанном товаре (в итоге сохр. в сессии)
	static $n = 0; //переменная для увеличения ячейки - $orderArr[$n][данные о товаре] и счетчик №.наимТовара + 1 (только для печати)
	static $bookSum; //переменная в которой будет обновляться сумма заказа

 // > Получение данных из массива сессии и структурирование этого массива с передачей в выборку в БД и вызов печати таблицы
	static function getSesBasket() {
		if( isset($_SESSION["BasketArr"]) ) { //блокирование обращения по кнопке назад (после очистки корзины)
			$Arr = unserialize( $_SESSION["BasketArr"] ); //распакуем массив из сессии
			if( count($Arr) != 0 ) { //если в массиве что то есть
			 //Определим кол-во вхождений элемента в массиве и удалим все последующие дубликаты (сохранив кол-ва вхождений)
				$clearArr = array(); //Массив отфильтрованных элементов
				$elemSizeArr = array(); //Массив кол-ва вхождений элементов в начальный массив
				foreach ($Arr as $val) { //проверка на присутствие входящего элемента в отфильтрованном массиве
					if(  !(in_array($val, $clearArr))  ) { //т.е. если текущего элемента нет в $clearArr
						$clearArr[] = $val; //положили входящий элемент в массив отфильтрованных данных
						$elemSize = count( array_keys($Arr, $val) ); //подсчитали кол-во входящих элем. в массиве
						$elemSizeArr[] = $elemSize; //положили это кол-во в соотв. массив
					}
				}

			 //Имеем $clearArr, $elemSizeArr - отфильтрованный массив и массив кол-ва соответствующих вхождений
				$e = 0; //счетчик для итерации массива соответствующих вхождений
				self::echoTableBox("start"); //Печать шапки таблицы
				foreach ($clearArr as $id) {
				  self::getData( $id, $elemSizeArr[$e] ); //получение информации из таблицы БД и вывод
					$e++;
				}
				self::echoTableBox("end"); //Печать закрывающего тега
				self::lowBasketPrn(); //вывод футера

			 //Формирование выходных данных (сессионных)				
				$str = serialize(self::$orderArr); //Упаковка итогового массива данных заказа в сессию
				$_SESSION["OrderArr"] = 	$str; //Ложим данные обратно в сессию
			} else {
				unset( $_SESSION["OrderArr"] ); //на спец. случай
				echo '<center class="redirectMsg">Корзина пуста!</center>';
				echo '<meta http-equiv="Refresh" content="2; URL='.self::getUrlFromSes().'">';
				exit();
			}
		} else {
			echo '<center class="redirectMsg">Корзина пуста!</center>';
			echo '<meta http-equiv="Refresh" content="3; URL='.self::getUrlFromSes().'">';
			exit();
		}
	}

 // > Выборка из БД данных по ID и отправка на печать
	private function getData( $id, $quantity ) {
		global $mysqli;
		$queryS = "SELECT bookName, bookAuthor, bookPrice
								 FROM books
								 WHERE id='$id'";
		$resultS = $mysqli->query($queryS);
		$selectQueryArr = Lib_Paginator::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
		$resultS->free(); //Освобождаем память занятую результатами запроса
		
		foreach ($selectQueryArr as $inArr) {
		 //Значения ячеек будут передаваться ф-ией в ф-ю печати (и счетчик №наименов. товара, для экономии возьмем self::$n)
		  self::echoBookBox( $id, $inArr['bookName'], $inArr['bookAuthor'], $inArr['bookPrice'], $quantity, self::$n + 1 );
		 //Формирование итогового массива данных заказа (будет обрабатываться на странице оформления заказа)
			self::$orderArr[self::$n]['bookId'] = $id;
			self::$orderArr[self::$n]['bookName'] = $inArr['bookName'];
			self::$orderArr[self::$n]['bookAuthor'] = $inArr['bookAuthor'];
			self::$orderArr[self::$n]['bookPrice'] = $inArr['bookPrice'];
			self::$orderArr[self::$n]['quantity'] = $quantity;
			self::$n++; //увеличиваем ячейку 2-го массива (для сохранения данных следующей книги)
		}
	}

/*____________________________Вывод таблицы корзины____________________________*/
 // > Печать шапки таблицы и закрывающего тега (в зависимости от вход. параметра)
	private function echoTableBox($str) {
		if( $str == "start" ) {
			echo '
					<table id="AdmTable" align="center" cellpadding="5" cellspacing="0">
						<caption><b>Таблица : </b> <i>Заказа</i></caption>
						<thead>
						  <tr>
						    <td id="tabNum">№</td>
						    <td>Имя Книги:</td>
						    <td>Авторы:</td>
						    <td id="tabPrice">Цена:</td>
						    <td id="tabVal">Кол-во:</td>
						    <td id="tabDel">Удалить:</td>
						  </tr>
				    </thead>
			';
		} else if( $str == "end" ) {
			echo '
					</table>
			';
		}
	}

 // > Печать Таблицы заказа (Книг)
	private function echoBookBox( $id, $bookName, $bookAuthor, $bookPrice, $quantity, $i ) {
		self::$bookSum = self::$bookSum + $bookPrice * $quantity;
		echo '
						<tr>
						  <td>' . $i . '</td>
							<td>' . $bookName . '</td>
							<td>' . $bookAuthor . '</td>
							<td>' . $bookPrice . '</td>
							<td>' . $quantity . '</td>
							<td><a href="inBasket.php?clear=' . $id . '" class="delUrl" title="Удалить"></a></td>
						</tr>
		';
	}

 // > Итоги корзины и печать кнопки оформления заказа
	private function lowBasketPrn() {
		echo '
			<br><hr style="color: #E8A363;">
			<center> Товара на сумму:
			<span class="bookPrice">' . self::$bookSum . '</span> грн.<br>				
				<a href="inBasket.php?clear=allBasket" class="fillOrder clearOrderAll">Очистить заказ</a>
				<a href="fillForm.php" class="fillOrder">Оформить заказ</a><br>
				<a href="'.self::getUrlFromSes().'" class="fillOrder clearOrder">Вернутся в каталог</a>
			</center>
		';
	}

 // > Очистка корзины
	static function clearBasket( $clearInfo, $type ) {
		$clearInfo = Lib_Paginator::clearData( $clearInfo, $type ); //пропустим через фильтрацию
		if( $clearInfo == 'allBasket' ) {
			unset( $_SESSION["BasketArrSize"] );
			unset( $_SESSION["BasketArr"] );
			unset( $_SESSION["OrderArr"] ); //на спец. случай
			echo '<center class="redirectMsg">Корзина пуста!</center>';
			echo '<meta http-equiv="Refresh" content="1; URL='.self::getUrlFromSes().'">';
			exit();
		} else {
			$Arr = unserialize( $_SESSION["BasketArr"] ); //распакуем массив из сессии
			if( in_array($clearInfo, $Arr) ) { //проверим существует ли такое значение в массиве (на случай ручной подстановки)
				$k = array_search($clearInfo, $Arr); //найдем индекс данного значения
				unset($Arr[$k]); //удалим элемент с индексом $k
				$_SESSION["BasketArrSize"] = $_SESSION["BasketArrSize"] - 1; //уменьшаем размер числа корзины на 1
			}
			$str = serialize($Arr); //Упаковываем массив в строку
			$_SESSION["BasketArr"] = 	$str; //Ложим данные обратно в сессию
			echo '<meta http-equiv="Refresh" content="0; URL=inBasket.php">';
		}
	}

 //_______________Реализация возврата на позицию каталога (позицию пагинации)_______________
 // > Реализация получения имени реферальной страницы
	static function clearPageRef($url) {
		if( empty($url) ) { // если нет реф. ссылки (придет пустое значение)
			return $url; //вернем его же
		}
	 //принимает: реферальную ссылку вида, Например: ( http://.../bookCatalog.php?category=*&list=* )		
		$bStr = basename($url); //правая часть ( bookCatalog.php?category=*&list=* )	
		$qStr = parse_url( $bStr, PHP_URL_QUERY); //то, что нужно отбросить category=*&list=*
		if( empty($qStr) ) { //на тот случай если нечего отбрасывать
			return $bStr;
		}
	 //если что то отбросилось
		$sLen = strlen($bStr); //значение длины всей строки ( bookCatalog.php?category=*&list=* )
	 //значение позиции вхождения подстроки ( category=*&list=* ) в строку ( bookCatalog*.php?list=* )			
		$pos = strpos($bStr, $qStr);
	 //итоговое название ф-ла ( bookCatalog.php ) с учетом сдвига на -1, за счет симв. ?
		return substr($bStr, 0, $pos - 1);
	}

 // > Реализация получения начального URL из файл
	static function getUrlFromSes() {		
		if( isset($_SESSION["startURL"]) && !empty($_SESSION["startURL"]) ) { //если сессия установлена и не пуста
			return $_SESSION["startURL"]; //возвращаем реф. ссылку
		} else { //возврат по умолчанию
			return "bookCatalog.php?category=1"; //вернем начальную стр. как начало каталога
		}
	}

}