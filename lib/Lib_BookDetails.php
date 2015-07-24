<?php
require_once('Lib_Paginator.php'); //Вызов ф-ла подключения к библиотеке Пагинатора
/* ===================================================================
					Реализация Вывода страницы Подробнее (для книги):
=================================================================== */
class Lib_BookDetails {

 // > Получение данных из таблицы books для передачи на печать таблицы
	static function RowFromBooks($id) {
		$id = Lib_Paginator::clearData($id, 'i'); //пропустим через фильтр
		global $mysqli; //Используем глобальный объект
		$queryS = "SELECT imgName, bookName, bookAuthor, bookPrice, bookDescr
							 FROM books WHERE id='$id'";
		$resultS = $mysqli->query($queryS);
		$selectQueryRow = Lib_Paginator::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)
		$resultS->free(); //Освобождаем память занятую результатами запроса
		foreach ($selectQueryRow as $inArr) {
		  self::PrnBook( $inArr, $id ); //Передадим ячейки подмассива в ф-ию
		}
	}

 // > Получение массива данных и вывод
	private function PrnBook( $inArr, $id ) {
		echo '
			<div class="bookDetBox">
				<div class="bookImgBox">
					<img src="../Img/Catalog/' . $inArr['imgName'] . '.jpg" alt="" class="ImgBook"><!--Картинка к Книге-->
				</div>
				<div><b>' . $inArr['bookName'] . '</b></div>
				<div><b>Авторы:&nbsp</b>'. $inArr['bookAuthor'] . '</div>
				<b>Цена:&nbsp</b>'. $inArr['bookPrice'] . ' грн.
				<br><br><hr>
				<div>'. $inArr['bookDescr'] . '</div>
				<br>
				<div style="text-align: right;">
					<a href="addItem.php?id=' . $id . '" class="urlBookDown urlToBasket">Заказать</a><!--Ссылка заказать/Добавить в корзину-->
				</div>
				<center><b>Отзывы:</b></center>
				<div class="commentsBoxDet">
					Форма добавления коментариев (подобно добавлению книги в Admin) + учет пользовательского входа
				</div>
				<div class="commentsBoxDet" style="background-color: #E4FFED; margin-top: 15px;">
					Вывод записей с комментариями (аналгично выводу каталога книг с пагинацией страниц)
				</div>
				<center style="margin-top: 20px;"><b>Оценить:</b></center>
				<div class="commentsBoxDet" style="background-color: #E7E7E7;">
					Подобно заказать товар но разово оценить (для зарегистрированных пользователей)
				</div>
			</div>
		';
	}

}