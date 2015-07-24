<?php
/* ===================================================================
							Класс реализации подсветки пункта меню :
=================================================================== */
class Lib_HighLightUrlAdm {

 /*Определим Массив из 4-х пустых элементов (строк), при каждом обращении он будет принимать пустые значения.
	 Будет временно хранить Подсветку классом выбранной ссылки меню.*/
	static $UrlMenuArrAdm = array('', '', '', '', '');

 // > Определение имени файла в URL, для подсветки пункта меню
	static function highLightUrlAdm() {
		switch( basename( $_SERVER['PHP_SELF'] ) ) {
			case 'booksAdm.php' :
			case 'booksAdmEdit.php' :
			case 'booksAdmAdd.php' :
				self::$UrlMenuArrAdm[0] = " headerUrlSel";//Имя CSS класса с пробелом в начале
			break;
			case 'ordersAdm.php':
			case 'ordersClientAdm.php':
				self::$UrlMenuArrAdm[1] = " headerUrlSel";
			break;
			case 'commentsAdm.php':
				self::$UrlMenuArrAdm[2] = " headerUrlSel";
			break;
			case 'usersAdm.php':
				self::$UrlMenuArrAdm[3] = " headerUrlSel";
			break;
			default:
				foreach (self::$UrlMenuArrAdm as $i => $value) {
					self::$UrlMenuArrAdm[$i] = "";
				}
			break;
		}
	}

}