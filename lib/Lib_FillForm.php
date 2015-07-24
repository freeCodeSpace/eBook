<?php
require_once('Lib_Paginator.php'); //Вызов ф-ла подключения к библиотеке Пагинатора
require_once('Lib_Basket.php'); //Вызов ф-ла подключения к ф-лу корзины
/* ===================================================================
						Реализация Оформления заказа:
=================================================================== */
class Lib_FillForm {

 // > Печать формы оформления заказа
	static function PrnForm( $customerName="", $address="", $telefon="", $msgCpt="" ) {
		echo '
			<div><br>
				<form id="mForm" class="OrderForm" name="myForm" action="'. $_SERVER['PHP_SELF'] .'" method="post">
					<label>
						<span class="mFormText">Имя:</span><br>
						<input type="text" name="customerName" value="'.htmlspecialchars($customerName, ENT_QUOTES).'" size="30" maxlength="60"><br><br>
					</label>
					<label>
						<span class="mFormText">Адрес:</span><br>
						<input type="text" name="address" value="'.htmlspecialchars($address, ENT_QUOTES).'" size="30" maxlength="60"><br><br>
					</label>
					<label>
						<span class="mFormText">Телефон:</span><br>
						<input type="text" name="telefon" value="'.htmlspecialchars($telefon, ENT_QUOTES).'" size="20" maxlength="15"><br><br>
					</label>
					<!--Вывод captcha-->
					<p><strong>Анти-спам проверка:</strong></p>
				';
				if( !empty($msgCpt) ) { //если ошибка captcha (выведем на время данный блок)
					echo '<div id="ErrCpt">текст не совапал с картинкой</div>
								<script>
									var ErrCpt = document.getElementById("ErrCpt");
									window.setTimeout( function() { ErrCpt.parentNode.removeChild(ErrCpt); }, 3500 );
								</script>
					';
				}
				echo '
						<img src="../captcha/captcha.php" id="captcha"><br>
					<script>
						function captchaF1() { document.getElementById("captcha").src="../captcha/captcha.php?"+Math.random(); }
						function captchaF2() { document.getElementById("captcha-form").focus();	}
						function remErr() { var ErrCpt = document.getElementById("ErrCpt"); ErrCpt.parentNode.removeChild(ErrCpt); }
					</script>
					<i>Введите текст на картинке: </i>
						<input type="text" name="captcha" id="captcha-form" autocomplete="off"><br>
						<a href="#" onclick="captchaF1(); captchaF2(); remErr();" id="change-image">Обновить картинку</a>
					<center>
						<hr>
						<input type="submit" class="fillOrder" value="Оформлено"><br>
					</center>
				</form>
			</div>
		';
	}

 // > Прием данных полученных POST-ом
	static function CheckForm() {
		if( //проверка на пустоту полей
       !empty( $_POST['customerName'] ) and !empty( $_POST['address'] ) and
       !empty( $_POST['telefon'] ) and !empty( $_POST['captcha'] )
			) { //если все поля заполнены

			if( !empty($_POST['captcha']) ) { //Обработка введения данных формы
				lib_fillForm::ValidationCpth(); //Валидация captcha
			}

		} else { //если не все поля заполнены, перевывод формы с сохранением уже введенных значений
			echo '<center style="color:brown; font-size: 22px;"><i>Не все поля заполнены</i></center>';
			self::PrnForm( $_POST['customerName'], $_POST['address'], $_POST['telefon'] ); //с передачей уже введенных значений
		}
	}

 // > Валидация captcha
	private function ValidationCpth() {
    if ( empty($_SESSION['captcha']) || trim( strtolower($_REQUEST['captcha']) ) != $_SESSION['captcha'] ) {
     // Если ввод не совпал с данными на картинке
    	$msgCpt = true; //режим вывода сообщения об ошибке
			self::PrnForm( $_POST['customerName'], $_POST['address'], $_POST['telefon'], $msgCpt ); //с передачей уже введенных значений
    } else { // Успешный ввод captcha			
			self::getSesBasket();
    }
    unset($_SESSION['captcha']); //обнуляется сессия (в любом случае)
	}	

 // > Занесение данных из массива сессии в БД, после заполнения формы и успешной валидации captcha
	private function getSesBasket() {
  	$customerName = Lib_Paginator::clearData( $_POST['customerName'] ); //по умолч. строка
  	$address = Lib_Paginator::clearData( $_POST['address'] );
  	$telefon = Lib_Paginator::clearData( $_POST['telefon'] ); //пусть хранится как строка
  	$customerSesId = session_id(); //ID сессии
		$datetime = time(); //дата добавления товара в корзину (текущую дату)

	 //Занесение данных из массива сессии в БД, после заполнения формы и успешной валидации captcha
		global $mysqli;
		$sqlAdd = "
			INSERT INTO orders(
				bookId,
				bookName,
				bookAuthor,
				bookPrice,
				quantity,
				customerSesId,
				datetime,
				customerName,
				address,
				telefon
			)
    	VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $mysqli->prepare($sqlAdd);
	 
	 //Обработка массива сессии
		$OrderArr = unserialize( $_SESSION["OrderArr"] ); //распакуем массив из сессии
		foreach ($OrderArr as $val) {
			if( count($val) != 0 ) { //если в массиве что то есть
			 //используя уже установленный подготовленный запрос (в цикле связываем параметры и выполняем подготовленный запрос)
				$stmt->bind_param( "issiisisss", $val['bookId'], $val['bookName'], $val['bookAuthor'], $val['bookPrice'], $val['quantity'], $customerSesId, $datetime, $customerName, $address,	$telefon ); //Связываем параметры
				$stmt->execute(); //исполнение запроса
			} else { //это на всякий случай:
				echo "<center>!Ошибка! - Данных нет</center>";
				echo '<meta http-equiv="Refresh" content="1; URL='.Lib_Basket::getUrlFromSes().'">';
				exit();
			}			
		}
		$stmt->close(); //Закрывает подготовленный запрос

	 //После успешного добавления данных в БД (очистим переменные сессии)
		unset( $_SESSION["BasketArrSize"] );
		unset( $_SESSION["BasketArr"] );
		unset( $_SESSION["OrderArr"] );
		echo '<center class="redirectMsg">Заказ успешно оформлен!<br> С вами свяжется менеджер.</center>';
		echo '<meta http-equiv="Refresh" content="3; URL='.Lib_Basket::getUrlFromSes().'">';
		exit();
	}

}