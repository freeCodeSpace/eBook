<?php
require_once('Lib_PaginatorAdm.php'); //Вызов ф-ла библ. ф-ий

/* ===================================================================
							 Реализация Вывода каталога заказчиков:
=================================================================== */
class Lib_OrderClientsAdm {

 // > Выборка заказов по клиентам
	static function getClientOrders() {
		global $mysqli; //Используем глобальный объект
	 /*Выборка информации о клиенте (с учетом, что один и тот же человек, не может заказать в одно и тоже время)
	 	т.е. дублирующие записи опустятся т.к. время будет одинаково (связка customerName, datetime),
	 	а сумма произведения полей quantity * bookPrice возьмется по всему заказу,
	 	отсортируем по id, для правильной хронологии поступления заказов*/
		$queryS = "SELECT id, customerName, datetime, address, telefon, SUM( quantity * bookPrice ) AS sumCost
 								FROM orders
 							 GROUP BY customerName, datetime ORDER BY id";
		$resultS = $mysqli->query($queryS);
	 //Передаем рез. выборки в ф-ию Конвертации в массив
		$ClientsGrArr = Lib_PaginatorAdm::selectQuery2Arr($resultS); //Сохр. рез. (массив) -> в переменной (массив)

		$resultS->free(); //Освобождаем память занятую результатами запроса
		$ClientsGrArr; //массив информации о клиентах

	 //Печатаем информацию по клиенту и сумму его заказа
		self::PrnClientOrders($ClientsGrArr);
		$mysqli->close(); //Закр. соединение
	}


 /*____________________________Вывод каталога заказов____________________________*/
 // > Вывод таблицы каталога заказов
	private function PrnClientOrders($selectQueryArr) {
		self::echoTableBox("start"); //Печать шапки таблицы
		$i = 1; //счетчик № клиентов
		foreach ($selectQueryArr as $inArr) {
		 //Значения ячеек будут передаваться ф-ией в ф-ю печати
		  self::echoOrderBox( $inArr['id'], $inArr['customerName'], $inArr['datetime'], $inArr['address'], $inArr['telefon'], $inArr['sumCost'], $i++ );
		}
		self::echoTableBox("end"); //Печать закрывающего тега
	}

 // > Печать шапки таблицы
	private function echoTableBox($str) {
		if( $str == "start" ) {
			echo '
					<table id="AdmTable" align="center" cellpadding="5" cellspacing="0">
						<caption><b>База : </b> <i>Покупатели</i></caption>
						<thead>
						  <tr>
						  	<td id="tabNum">№</td>
						    <td>Имя:</td>
						    <td>Адресс:</td>
						    <td>Телефон:</td>
						    <td>Дата Заказа:</td>
						    <td>Сумма Заказа:</td>
						    <td class="customerOrdDetal">Подробнее:</td>
						    <td class="tabDelOrd">Удалить:</td>
						  </tr>
				    </thead>
			';
		} else if( $str == "end" ) {
			echo '
					</table>
			';
		}
	}

 // > Печать Таблицы Заказов
	private function echoOrderBox( $id, $customerName, $datetime, $address, $telefon, $sumCost, $i ) {
		echo '
						<tr>
							<td>' . $i . '</td>
							<td>' . $customerName . '</td>
							<td>' . $address . '</td>
							<td>' . $telefon . '</td>
							<td>' . date('d-m-Y H:i', $datetime) . '</td>
							<td>' . $sumCost . '</td>
							<td><a href="#?id=' . $id . '" class="customerOrdDetal" title="Подробнее">◊</a></td>
							<td><a href="#?id=' . $id . '" class="delUrl" title="Удалить"></a></td>
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

}