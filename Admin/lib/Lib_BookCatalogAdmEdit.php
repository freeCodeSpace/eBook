<?php
require_once('Lib_PaginatorAdm.php'); //Вызов ф-ла подключения к библиотеке Пагинатора
require_once('Lib_FormValidation.php'); //Вызов ф-ла подключения к библиотеке Обработки данных формы
/* ===================================================================
						Реализация Обновления записи в БД:
=================================================================== */
class Lib_BookCatalogAdmEdit {

 // > Удаление данных (записи из БД по ID)
	static function delRowFromBooks( $id, $img ) {
		$id = Lib_PaginatorAdm::clearData($id,'i');
		$img = Lib_PaginatorAdm::clearData($img);
	 //Удаление картинки в каталоге картинок
		$file = '../../Img/Catalog/' . $img . ".jpg"; //получаем название и путь к файлу
		if( !file_exists($file) ) { //если у книги не было картинки			
			echo '<center class="redirectMsg">Файл изображения картинки не удален, т.к. не найден.</center>';
		} else {
			unlink($file); //удалим файл
		}
	 //Очистка записи в БД
		global $mysqli;
		$queryDel = "DELETE FROM books WHERE id='$id'";
		$mysqli->query($queryDel);
		echo '<center class="redirectMsg">Запись удалена!</center>';
		echo '<meta http-equiv="Refresh" content="1; URL='.$_SERVER['HTTP_REFERER'].'">';
		exit();
	}

 // > Получение данных из таблицы books для передачи на печать таблицы
	static function editRowFromBooks($id) {
		$id = Lib_PaginatorAdm::clearData($id,'i');
		global $mysqli; //Используем глобальный объект
		$queryS = "SELECT id, imgName, bookName, bookAuthor, bookPrice, bookClass, bookDescr
							 FROM books WHERE id='$id'";
		$resultS = $mysqli->query($queryS);
		$selectQueryRow = Lib_PaginatorAdm::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
		$resultS->free(); //Освобождаем память занятую результатами запроса
		foreach ($selectQueryRow as $inArr) {
		  self::PrnEditBook($inArr); //Передадим ячейки подмассива в ф-ию
		}
	}

 // > Получение массива данных и распечатка формы с этими данными
	private function PrnEditBook($inArr) {
		echo '
			<div><br>
				<center><b>Форма редактирования:</b></center> <hr>
				<form id="mForm" name="myForm" action="'. $_SERVER['PHP_SELF'] .'" method="post">
					<label>
						<span class="mFormText">Имя Картинки:</span> <i>любые значения</i><br>
						<input type="text" name="imgName" value="'. htmlspecialchars( $inArr['imgName'], ENT_QUOTES ) .'" size="20" maxlength="30"> <br>
					</label>
					<label>
						<span class="mFormText">Имя Книги:</span> <i>любые значения</i><br>
						<input type="text" name="bookName" value="'. htmlspecialchars( $inArr['bookName'], ENT_QUOTES ) .'" size="40" maxlength="60"> <br>
					</label>
					<label>
						<span class="mFormText">Авторы:</span> <i><br>Кир/Лат,(Любой регистр), Доп. симв: <b>. , \' - _ &</b> и не цифры</i><br>
						<input type="text" name="bookAuthor" value="'. $inArr['bookAuthor'] .'" size="40" maxlength="60"> <br>
					</label>
					<label>
						<span class="mFormText">Цена:</span> <br><i>положит. целые цифры</i><br>
						<input class="mFormInputC" type="text" name="bookPrice" value="'. $inArr['bookPrice'] .'" size="5" maxlength="4"> <br>
					</label>
		';
	 //Вывод опции для radio-input (в зависимости от значения в БД)
		if( $inArr['bookClass'] == "Catalog1" ) {
			echo '
					<label>
					<input type="radio" name="bookClass" value="Catalog1" checked>Catalog1<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog2">Catalog2<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog3">Catalog3<br>
					</label>
			';
		} elseif( $inArr['bookClass'] == "Catalog2" ) {
			echo '
					<label>
					<input type="radio" name="bookClass" value="Catalog1">Catalog1<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog2" checked>Catalog2<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog3">Catalog3<br>
					</label>
			';
		} elseif( $inArr['bookClass'] == "Catalog3" ) {
			echo '
					<label>
					<input type="radio" name="bookClass" value="Catalog1">Catalog1<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog2">Catalog2<br>
					</label>
					<label>
					<input type="radio" name="bookClass" value="Catalog3" checked>Catalog3<br>
					</label>
			';
		}
		echo '
					<span class="mFormText">Контент Урока:</span><br>
					<textarea id="m_textarea1" type="text" name="bookDescr" rows="12" cols="65" size="20">'.$inArr['bookDescr'].'</textarea>
					<br>
					<!-- Передача ID - через скрытое поле, для опер. UPDATE -->
					<input type="hidden" name="id" value="'. $inArr['id'] .'">
					<div id="sendDiv">
						<input type="submit" value="Внести изменения"> <br>
					</div>
				</form>
			</div>
		';
	}

 // > Прием данных полученных POST-ом либо возврат на вызванную страницу (если есть пустые поля)
	static function updateBook() {
		if( //проверка на пустоту полей
       !empty( $_POST['id'] ) and !empty( $_POST['imgName'] ) and !empty( $_POST['bookName'] ) and
       !empty( $_POST['bookAuthor'] ) and !empty( $_POST['bookPrice'] ) and
       !empty( $_POST['bookClass'] ) and !empty( $_POST['bookDescr'] )
			) {
	  	$id = Lib_PaginatorAdm::clearData( $_POST['id'], 'i' ); //т.к. число
	  	$imgName = Lib_PaginatorAdm::clearData( $_POST['imgName'] ); //по умолч. строка
	  	$bookName = Lib_PaginatorAdm::clearData( $_POST['bookName'] );
	  	$bookAuthor = Lib_PaginatorAdm::clearData( $_POST['bookAuthor'] );
	  	$bookPrice = Lib_PaginatorAdm::clearData( $_POST['bookPrice'], 'i' );
	  	$bookClass = Lib_PaginatorAdm::clearData( $_POST['bookClass'] );
	  	$bookDescr = Lib_PaginatorAdm::clearData( $_POST['bookDescr'], 't' );

		 //Вызов класса валидации данных формы, перед обновлением данных в БД
			Lib_FormValidation::FormValidationEdit( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass );

		 //Обновление данных
			$resUpd = self::updateData( $id, $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr );
			if( $resUpd == true ) {
				echo '<center class="redirectMsg">Запись Обновлена!</center><br>';
				echo '<meta http-equiv="Refresh" content="1; URL=' . self::getUrlFromFile() . '">'; //возьмем из файла значение реферальной ссылки
				exit();
			} else {
				echo '<center class="redirectMsg">Данные не попали!</center><br>';
			}
	  } else { //Если ошибка с данными (Рефреш с редиректом, с ожиданием автоматического перехода):
			self::refreshURL();
	  }
	}

 // > Реализация редиректа
	static function refreshURL( $msg = "Не все поля заполнены!", $timer = 3 ) {
		echo '<meta http-equiv="Refresh" content="' . $timer . '; URL=' . $_SERVER['HTTP_REFERER'] . '">';
	  echo '<div class="refreshErr">' . $msg . '</div>',
	       '<a href="' . $_SERVER['HTTP_REFERER'] . '">Вернутся назад</a><br>',
	       '<span style="font-size:18px;">Либо переход осуществится автоматически через</span> <div id="redirectClock">' . $timer . '</div>
	      	<script>
						var HtmlBox = document.getElementById("redirectClock");
						var boxNum = parseInt( HtmlBox.innerHTML );
						var interval = window.setInterval(function() {
							boxNum--;
							HtmlBox.innerHTML = boxNum;
							if( boxNum < 1 ) {
								window.clearInterval(interval);
							}
						}, 1000);
	      	</script>
	      ';
	  exit();
	}

 // > Реализация запроса обновления
	private function updateData( $id, $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ) {
		global $mysqli; //Используем глобальный объект
		$sqlUpDate = "
			UPDATE books SET
				imgName = '$imgName',
				bookName = '$bookName',
				bookAuthor = '$bookAuthor',
				bookPrice = $bookPrice,
				bookClass = '$bookClass',
				bookDescr = '$bookDescr'
			WHERE id = $id
		";
		return $mysqli->query($sqlUpDate); //Вернется либо true либо false
	}

 //_______________Реализация возврата на позицию каталога (позицию пагинации)_______________
 // > Реализация получения имени реферальной страницы
	static function clearPageRef($url) { // принимает: реферальную ссылку вида ( http://myw...es/booksAdm.php?list=* )	 
		$bStr = basename($url); //правая часть ( booksAdm.php?list=* )
		$qStr = parse_url( $bStr, PHP_URL_QUERY); //то что нужно отбросить ( list=* ) учитывая что симв. ? необх. учесть в длине
		if( empty($qStr) ) { //на тот случай если нечего отбрасывать (обрабатывалась стр. типа: booksAdm.php)
			return $bStr;
		} else { //если что то отбросилось
			$sLen = strlen($bStr); //значение длины всей строки ( booksAdm.php?list=* )
			$pos = strpos($bStr, $qStr); //значение позиции вхождения подстроки ( list=* ) в строку ( booksAdm.php?list=* )
			return substr($bStr, 0, $pos - 1); //итоговое название ф-ла ( booksAdm.php ) с учетом сдвига на -1, за счет симв. ?
		}
	}

 // > Реализация сохранения начального URL в файл
	static function putUrlInFile($urlStart) {
		$file = "startURl.txt";
	 //если файла нету или он есть, тогда создадим/перезапишем
		$fp = fopen($file, "w"); //тогда создадим
		fwrite($fp, $urlStart); //и положим туда значение URL, куда потом необходимо вернутся
		fclose ($fp);		
	}

 // > Реализация получения начального URL из файл
	private function getUrlFromFile() {		
		$file = "startURl.txt";
		if( !file_exists($file) ) { //если файла нету
			return "booksAdm.php"; //тогда возможно это прямая ссылка и не было реферала, вернемся просто в начало страницы каталога
		} else { //иначе он есть, возьмем из него реферальный URL
			$fp = fopen($file, "r"); //откроем для чтения
			$urlStart = fgets($fp); //считаем всю 1-ю строку
			fclose ($fp); //закроем 
			unlink("startURl.txt"); //удалим файл
			return $urlStart;
		}
	}

}