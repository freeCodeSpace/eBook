<?php
require_once('Lib_PaginatorAdm.php'); //Вызов ф-ла подключения к библиотеке Пагинатора
require_once('Lib_FormValidation.php'); //Вызов ф-ла подключения к библиотеке Обработки данных формы
/* ===================================================================
						Реализация Обновления записи в БД:
=================================================================== */
class Lib_BookCatalogAdmAdd {
	
	static $startUrl = "booksAdm.php"; //страница распечатки каталога

 // > Печать формы: ( либо перевывод формы с печатью ошибок )
	static function PrnAddBook( $imgName="", $bookName="", $bookAuthor="", $bookPrice="", $bookClass="", $bookDescr="", $msg="", $msgClass="" ) {
		echo '
			<div><br>
				<center><b>Форма добавления:</b></center> <hr>
				<form id="mForm" name="myForm" action="'. $_SERVER['PHP_SELF'] .'" method="post">
					<label>
						<span class="mFormText">Имя Картинки:</span> <i>любые значения</i><br>
						<input type="text" name="imgName" value="'. htmlspecialchars( $imgName, ENT_QUOTES ) .'" size="20" maxlength="30"> <br>
					</label>
					<label>
						<span class="mFormText">Имя Книги:</span> <i>любые значения</i><br>
						<input type="text" name="bookName" value="'. htmlspecialchars( $bookName, ENT_QUOTES ) .'" size="40" maxlength="60"> <br>
					</label>
		';
	 //Вывод ошибки валидации, для типа 1 (если есть)
		if( $msgClass == 1 ) { //с пустым полем (т.к. в нем ошибка)
			echo '
					<label>
						<span class="mFormText">Авторы:</span> <i><br>Кир/Лат,(Любой регистр), Доп. симв: <b>. , \' - _ &</b> и не цифры</i><br>
						<input type="text" name="bookAuthor" value="" size="40" maxlength="60"> <br>
					</label>
			';
			echo $msg;
		} else {
			echo '
					<label>
						<span class="mFormText">Авторы:</span> <i><br>Кир/Лат,(Любой регистр), Доп. симв: <b>. , \' - _ &</b> и не цифры</i><br>
						<input type="text" name="bookAuthor" value="'. $bookAuthor .'" size="40" maxlength="60"> <br>
					</label>
			';
		}
	 //Вывод ошибки валидации, для типа 2 (если есть)
		if( $msgClass == 2 ) { //с пустым полем (т.к. в нем ошибка)
			echo '
					<label>
						<span class="mFormText">Цена:</span> <br><i>положит. целые цифры</i><br>
						<input class="mFormInputC" type="text" name="bookPrice" value="" size="5" maxlength="4"> <br>
					</label>
			';
			echo $msg;
		} else {
			echo '
					<label>
						<span class="mFormText">Цена:</span> <br><i>положит. целые цифры</i><br>
						<input class="mFormInputC" type="text" name="bookPrice" value="'. $bookPrice .'" size="5" maxlength="4"> <br>
					</label>
			';
		}
	 //Вывод опции для radio-input (в зависимости от уже введенного)		
		if( $bookClass == "Catalog1" || $bookClass == "" ) { //то что есть, либо по умолчанию
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
		} elseif( $bookClass == "Catalog2" ) {
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
		} elseif( $bookClass == "Catalog3" ) {
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
					<textarea id="m_textarea1" type="text" name="bookDescr" rows="12" cols="65" size="20">'. $bookDescr .'</textarea>
					<br>
					<div id="sendDiv">
						<input type="submit" value="Добавить"> <br>
					</div>
				</form>
			</div>
		';
	}

 // > Прием данных полученных POST-ом
	static function addBook() {
		if( //проверка на пустоту полей
       !empty( $_POST['imgName'] ) and !empty( $_POST['bookName'] ) and
       !empty( $_POST['bookAuthor'] ) and !empty( $_POST['bookPrice'] ) and
       !empty( $_POST['bookClass'] ) and !empty( $_POST['bookDescr'] )
			) {
	  	$imgName = Lib_PaginatorAdm::clearData( $_POST['imgName'] ); //по умолч. строка
	  	$bookName = Lib_PaginatorAdm::clearData( $_POST['bookName'] );
	  	$bookAuthor = $_POST['bookAuthor']; //т.к. целевое поля для валидации
	  	$bookPrice = Lib_PaginatorAdm::clearData( $_POST['bookPrice'], 'i' );
	  	$bookClass = Lib_PaginatorAdm::clearData( $_POST['bookClass'] );
	  	$bookDescr = Lib_PaginatorAdm::clearData( $_POST['bookDescr'], 't' );

		 //Вызов класса валидации данных формы, перед вставкой в БД
			lib_FormValidation::FormValidationAdd( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr );

		 //Добавление данных
			$resUpd = self::addData( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr );
			if( $resUpd == true ) {
				echo '<center class="redirectMsg">Запись Добавлена!</center><br>';
				 																						 //с параметром успешного добавления для перехода в конец пагинации
				echo '<meta http-equiv="Refresh" content="2; URL=' . self::$startUrl . '?addRow=true' . '">';
				exit();
			} else {
				echo '<center class="redirectMsg">Данные не попали!</center><br>';
			}
	  } else { //Если ошибка с данными (Рефреш с редиректом, с ожиданием автоматического перехода):
				echo '<meta http-equiv="Refresh" content="3; URL='.$_SERVER['HTTP_REFERER'].'">';
	      echo '<center class="redirectMsg">Не все поля знаполнены!</center><br>',
	           '<a href=" ' . $_SERVER['HTTP_REFERER'] . ' ">Вернутся назад</a><br>',
	           'Либо переход осуществится автоматически через <div id="redirectClock">3</div>
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
				exit;
		}
	}

 // > Реализация Вставки
	private function addData( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ) {
		global $mysqli; //Используем глобальный объект
		$sqlAdd = "
			INSERT INTO books(
				imgName,
				bookName,
				bookAuthor,
				bookPrice,
				bookClass,
				bookDescr
			)
    	VALUES(?, ?, ?, ?, ?, ?)";
		if( !$stmt = $mysqli->prepare($sqlAdd) ) return false; //Вернется либо true либо false
		$stmt->bind_param( "sssiss", $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ); //Связываем параметры
		$stmt->execute(); //исполнение запроса
 		$stmt->close(); //Закрывает подготовленный запрос
 		return true; //ф-ия вернет истину если все успешно
	}

}