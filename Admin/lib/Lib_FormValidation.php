<?php
require_once('Lib_BookCatalogAdmEdit.php'); //Вызов ф-ла обработки
/* ===================================================================
                						Валидация формы :
=================================================================== */
class Lib_FormValidation {
	
 /*____________Для Обновления____________*/
 // > Прием данных для передачи на валидацию (с применением рег.выраж. соотв. типу поля)
	static function FormValidationEdit( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass ) {
		self::ValidationEdit( $imgName, "Имя Картинки", 30, 0 ); //Значение поля, "Имя поля", его длина, тип валидации
		self::ValidationEdit( $bookName, "Имя Книги", 60, 0 );
		self::ValidationEdit( $bookAuthor, "Авторы", 60, 1 ); //..тип валидации: 1
		self::ValidationEdit( $bookPrice, "Цена", 4, 2 ); //..тип валидации: 2
		self::ValidationEdit( $bookClass, "Тип Каталога", 30, 3 );
	}

 // > Реализация передачи вывода сообщений об ошибке в данных, под соответ. полем формы.
	private function ValidationEdit( $fieldVal, $fieldName, $fieldSize, $type ) {
	 //(т.к. длина полей ограничена в атрибуте тега, на случай модификации клиентом кода html )
		if( strlen($fieldVal) > $fieldSize ) { //если поле вообще пустое, оно обработается раньше и вернет редирект
			$msg = "В поле <b>" .$fieldName. " </b>превышен диапазон длины";
			Lib_BookCatalogAdmEdit::refreshURL( $msg, 4 );
		}
		if( $type == 0 ) { //по умолчанию
		} elseif( $type == 1 ) { //Кириллица, Латиница,(Любой регистр), Доп. симв: .,'-_& и не цифры
			$regShablon = "/^[aA-zZаА-яЯ\s\-',.&]+$/u";
			if ( !preg_match( $regShablon, $fieldVal ) ) {
			 //Сообщение об ошибке
				$msg = 'Поле ' . $fieldName . ' имеет недопустимое значение: <span class="refreshErrVal">' . $fieldVal . '</span>';
				Lib_BookCatalogAdmEdit::refreshURL( $msg, 4 ); //Вызовем ф-ию перенаправления на повторный ввод, с описанием ошибки
			}
		} elseif( $type == 2 ) { //без знака, только целое значение
			$regShablon = "#^[\d]+$#"; //так же полученные данные уже будут пропущенные через clearData()
			if ( !preg_match( $regShablon, $fieldVal ) ) { //т.е. эта проверка возможно излишняя.
				$msg = 'Поле ' . $fieldName . ' имеет недопустимое значение: <span class="refreshErrVal">' . $fieldVal . '</span>';
				Lib_BookCatalogAdmEdit::refreshURL( $msg, 4 );
			}
		} elseif( $type == 3 ) { //Для проверки типа каталога (предотвращение модификации клиентом кода html)			
    	if( ($fieldVal != "Catalog1") && ($fieldVal != "Catalog2") && ($fieldVal != "Catalog3") ) {
				$msg = 'Поле ' . $fieldName . ' имеет недопустимое значение: <span class="refreshErrVal">' . $fieldVal . '</span>';
				Lib_BookCatalogAdmEdit::refreshURL( $msg, 4 );
    	}
		}	
	}

 /*____________Для Добавления____________*/
 // > Прием данных для передачи на валидацию (с применением рег.выраж. соотв. типу поля)
	static function FormValidationAdd( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ) {
	 //Значение поля, его длина, тип валидации и для фильтрующих полей - еще раз, все значения данных
		self::ValidationAdd( $imgName, 30 ); 
		self::ValidationAdd( $bookName, 60 );
		self::ValidationAdd( $bookAuthor, 60, 1, $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ); //тип валидации: 1
		self::ValidationAdd( $bookPrice, 4, 2, $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr ); //тип валидации: 2
		self::ValidationAdd( $bookClass, 30, 3 );
	}

 // > Реализация передачи вывода сообщений об ошибке в данных, под соответ. полем формы (и передача уже заполненных полей)
	private function ValidationAdd( $fieldVal, $fieldSize, $type=0, $imgName="", $bookName="", $bookAuthor="", $bookPrice="", $bookClass="", $bookDescr="" ) {
		//(т.к. длина полей ограничена в атрибуте тега, на случай модификации клиентом кода html )
		if( strlen($fieldVal) > $fieldSize ) { //если поле вообще пустое оно обработается раньше и вернет редирект
			echo '<center class="redirectMsg">Превышен диапазон длины!</center><br>';
			echo '<meta http-equiv="Refresh" content="5; URL=booksAdmAdd.php">';
			exit();
		}
		if( $type == 0 ) { //по умолчанию
		} elseif( $type == 1 ) { //Кириллица, Латиница,(Любой регистр), Доп. симв: .,'-_& и не цифры
			$regShablon = "/^[aA-zZаА-яЯ\s\-',.&]+$/u";
			if ( !preg_match( $regShablon, $fieldVal ) ) {
				$msg = '<div class="refreshErr">Недопустимое значение: <span class="refreshErrVal">' . $fieldVal . '</span></div>';
			 //Повторный вызов формы с описанием ошибки
				Lib_BookCatalogAdmAdd::PrnAddBook( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr, $msg, 1 );
				exit();
			}
			$bookAuthor = Lib_PaginatorAdm::clearData( $bookAuthor ); //в итоге прогоним через фильтр
		} elseif( $type == 2 ) { //без знака, только целое значение
			$regShablon = "#^[\d]+$#"; //так же полученные данные уже будут пропущенные через clearData()
			if ( !preg_match( $regShablon, $fieldVal ) ) { //т.е. эта проверка возможно излишняя.
				$msg = '<div class="refreshErr">Недопустимое значение: <span class="refreshErrVal">' . $fieldVal . '</span></div>';
			 //Повторный вызов формы с описанием ошибки
				Lib_BookCatalogAdmAdd::PrnAddBook( $imgName, $bookName, $bookAuthor, $bookPrice, $bookClass, $bookDescr, $msg, 2 );
				exit();
			}
		} elseif( $type == 3 ) { //Для проверки типа каталога (предотвращение модификации клиентом кода html)			
    	if( ($fieldVal != "Catalog1") && ($fieldVal != "Catalog2") && ($fieldVal != "Catalog3") ) {
    		echo '<center class="redirectMsg">Недопустимое значение!</center><br>';
			 //Повторный вызов формы с описанием ошибки
				echo '<meta http-equiv="Refresh" content="5; URL=booksAdmAdd.php">';
				exit();
    	}
		}	
	}

}