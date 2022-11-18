# communalServicesProfile
Тестовое задание на позицию Backend-разработчика
<h3>Стэк:</h3>

• PHP 8.1.12;

• Symfony v.5.4.15;

• Nginx 1.23.2;


<h3>Требования к функционалу:</h3>
<p align="justify"> 
Предположим, что мы делаем личный кабинет пользователя услуг ЖКХ. Назовём это “Услуги ЖКХ по подписке”.
Услуги - это, например, вывоз мусора, электричество, лифт. Каждая услуга имеет цену за единицу 
(для лифта это, например, один этаж, для мусора - один проживающий в квартире, для электричества - киловатт).
Каждый месяц 1го числа происходят списания денег за услуги. Назовём это “расчетный день”.
У пользователя есть “баланс” - виртуальный кошелек, с которого оплачиваются услуги.
Мне как пользователю нужна возможность управлять оказываемыми мне услугами, а также управлять своим счетом (балансом). 
Требуется сделать сайт с двумя веб-страницами.
 
<h3>Первая страница</h3>

<b>На ней необходимо:</b>

•	показывать мой текущий баланс;

•	список оказываемых мне услуг (название, количество, цену и общую цену за все);

•	добавить возможность подписаться на услугу или отписаться от существующей (кнопка “Добавить” над списком и “Удалить” в каждой строчке списка).

<b>При подписке показывать форму, где можно:</b>

•	выбирать услугу;

•	вводить количество;

•	с моего баланса нужно списать количество средств, пропорциональное времени, оставшееся до расчетного дня 
(например, услуга стоит 30р, а сегодня 25 число, значит списать нужно 5р).

<b>При отписке:</b>

•	показывать запрос с подтверждением;

•	вернуть на баланс пропорциональную сумму (“неиспользованная” часть до расчетного дня).
 
<h3>Вторая страница</h3>

<b>На ней необходимо:</b>

•	показывать мой текущий баланс;

•	видеть список моих “транзакций”. То есть историю всех моих движений средств по балансу 
(с описанием, за какую услугу было списание, когда, какая сумма списана, какой стал результирующий баланс);

• сделать фильтр по датам (чтобы смотреть транзакции за период) и по конкретной услуге;

•	добавить кнопку, которая имитирует наступление “расчётного дня”. То есть позволит прямо сейчас списать деньги за все услуги;

•	добавить форму для добавления денег на баланс (имитация пополнения счета).

<h3>Дополнительная информация</h3>

Управление услугами (создание и удаление) не требуется. Можно просто наполнить таблицу в базе десятком примеров.
 Все действия пользователя должны проходить разумную валидацию:
 
 <i>Например, нельзя подписаться на новые услуги, если не позволяет состояние баланса.
 Авторизация не требуется (это излишне в данной задаче). Считаем, что тот, кто смотрит на страницу в данный момент - уже пользователь.
 Дизайн (внешний вид) не имеет значения, интересует именно организация базы данных и бэкэнд-код.</i>
</p> 
