<?php
/* ===================================================================
							Класс реализации подсветки пункта меню :
=================================================================== */
class Lib_HighLightUrl {

 /*Определим Массив из 4-х пустых элементов (строк), при каждом обращении он будет принимать пустые значения.
   Будет временно хранить Подсветку css-классом выбранной ссылки меню.*/
	static $UrlMenuArr = array('', '', '', '', '');

 // > Установка подсветки пункта меню, в соответствии с параметром $_GET['category']
	static function highLightUrl() {
		if( isset( $_GET['category'] ) ) { //если установлен параметр category
			$clearCateg = self::clearData( $_GET['category'], 'i' ); //отфильтруем принятый параметр
			switch( $clearCateg ) {
				case 1 :
					self::$UrlMenuArr[0] = " urlMenuSelect urlMenuSelectLMarg";//Имя CSS класса с пробелом в начале
				break;
				case 2 :
					self::$UrlMenuArr[1] = " urlMenuSelect urlMenuSelectLMarg";
				break;
				case 3 :
					self::$UrlMenuArr[2] = " urlMenuSelect urlMenuSelectLMarg";
				break;
				case 4 :
					self::$UrlMenuArr[3] = " urlMenuSelect urlMenuSelectLMarg";
				break;
				default: //очистка ячеек, на всякий случай
					foreach (self::$UrlMenuArr as $i => $value) {
						self::$UrlMenuArr[$i] = "";
					}
				break;
			}
		}
	}

 // > Фильтрация данных
	private function clearData($data, $type="s") { //по умолч. string
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

}