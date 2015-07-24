<!DOCTYPE HTML>
<HTML>
<HEAD>
	<META charset="utf-8">
	<TITLE>BookShop</TITLE>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="shortcut icon" href="img/favicon.ico">
</HEAD>
<BODY>
<DIV id="box">
 <!--Только Шапка-->
	<div class="header">
		<span class="header text">BoOkS &nbsp &nbsp &nbsp  &nbsp  &nbsp  &nbsp  &nbsp ShOp</span>
	</div>

 <!--Левое Меню-->
 <!--Контейнер для левого блока-->
	<DIV id="leftBox">
	 <!--Меню-->
		<div id="menu">			
			<a href="index.php" class="urlMenu urlMenuMain urlMenuSelect">Главная</a>
			<div id="headMenu">Категории Книг:
				<a href="pages/bookCatalog.php?category=1" class="urlMenu categoryMenu">HTML&CSS</a>
				<a href="pages/bookCatalog.php?category=2" class="urlMenu categoryMenu">jS&jQ</a>
				<a href="pages/bookCatalog.php?category=3" class="urlMenu categoryMenu">PHP&MySQL</a>
				<a href="pages/bookCatalog.php?category=4" class="urlMenu categoryMenu">Все категории</a>
			</div>
		</div>
	</DIV>

 <!--Контейнер для правого блока(Контента)-->
	<DIV id="rightBox">
		<div id="content">
		 <!--Контент страницы Главная-->
		 	 
			<h3 class="contentHead">
				Демонстрационный сайт с упрощенным дизайном, реализующий стандартную функциональную модель, на примере книжного магазина с использованием преимущественно PHP&MySQL.
			</h3>
			<h4 class="contentLowHead">Реализованный функционал:</h4>			
			<OL class="contentLine">
				<li>Пользовательская часть</li>
				<ol class="contentMainText">
					<li type="square" style="color: navy;"><b>Вывод товара:</b></li>
					<ol class="contentMainText">
						<li>Вывод товаров из БД по категориям (в соотв. с пунктом меню).</li>
						<li>Вывод товаров, оформлен с учетом кол-ва блоков на странице (но не более 4-х).</li>
						<li>Подсветка пунктов меню (в зависимости от типа категории).</li>
						<li>Разбивка базового шаблона на подключаемые блоки.</li>
						<li>Реализация пагинации страниц вывода товара (в соотв. с текущ. категорией).</li>
						<li>Страница подробнее для товара.</li>
					</ol>
					<li type="square" style="color: navy;"><b>Корзина покупателя:</b></li>
					<ol class="contentMainText">
						<li>Вывод кол-ва заказанного товара (в панели шапки).</li>
						<li><i>Страница обзора данных о текущем заказе:</i></li>
						<div class="subDescribe">- Обзор корзины, удаление заказа, очистка заказа.</div>
						<div class="subDescribe">- Реализация возврата на то место сайта, с которого перешли в корзину.</div>
						<li>Страница оформления заказа (с вводом captcha).</li>
					</ol>
				</ol>
				<li>Администраторская часть:</li>
				<ol class="contentMainText">
					<li type="square" style="color: navy;"><b>Таблица 'books' (табл. записей товара):</b></li>
					<ol class="contentMainText">
						<li>Вывод данных (подобно PhpMyAdim), оформлен табл. по 10-ть записей на стр.(с пагинацией).</li>
						<li>Удаления товара из БД.</li>
						<li>Добавления нового товара в БД и редактирование текущих данных.</li>
						<li>Валидация полей при добавлении/редактировании данных товара и авторедиректы с задержкой.</li>
						<li><i>Реализация возвратов (при обработке записей таблицы):</i></li>
						<div class="subDescribe">- при удалении/добавлении → в конец страницы пагинации (таблицы 'books').</div>
						<div class="subDescribe">- при редактировании → на ту позицию пагинации, с которой началось редактирование.</div>
						<li style="">Подсветка пунктов меню и подменю.</li>
					</ol>
					<li type="square" style="color: navy;"><b>Таблица 'orders' (табл. записей заказов):</b></li>
					<ol class="contentMainText">
						<li>Оформлена таблицей по 10-ть записей на стр. (с пагинацией).</li>
						<li>Возможность удалить запись.</li>
						<li>Возможность посмотреть таблицу заказов, но обобщенно по покупателям.</li>
					</ol>
				</ol>
			</OL>
			<br>

			<table id="techTab" align="center" cellpadding="5" cellspacing="0">
				<caption><b>Таблица.</b> <i>Версий используемых технологий:</i></caption>
				<thead>
				    <tr>
					     <td>Технология:</td>
					     <td>Версия:</td>
				    </tr>
		   </thead>
				<tr>
					<td>Open Server</td>
					<td>4.8.7</td>
				</tr>
				<tr>
					<td>Apache</td>
					<td>2.4.4</td>
				</tr>
				<tr>
					<td>PHP</td>
					<td>5.5.0</td>
				</tr>
				<tr>
					<td>MySQL</td>
					<td>5.6.12</td>
				</tr>
				<tr>
					<td>PhpMyAdmin</td>
					<td>4.0.4</td>
				</tr>
			</table>

		</div>
	</DIV>
</DIV>
</BODY>
</HTML>