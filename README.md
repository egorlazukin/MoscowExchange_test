# MoscowExchange_test
Тестовое задание по Мосбиржи. Задание описана в README. Если эта программа имеет нарушение правил компании Мосбиржи - свяжитесь со мной.
<br>
<h1><b>ЗАДАНИЕ:</b></h1>
Требуется написать скрипт на PHP, который сделает следующие вещи:

(1) Соберет в отдельный .csv файл все эти ссылки в формате
имя ссылки;ссылка
имя ссылки;ссылка
…
Здесь под “именем ссылки” имеется ввиду текст вида “Объемы торгов в *месяц* *год*”, под ссылкой имеется ввиду URL вида “ https://www.moex.com/n51955”
(2) В отдельную папку соберет контент всех документов как набор .txt файлов (можно не целиком всю html страницу, а только внутреннее содержимое первого встретившегося на странице <div class="text-block"> элемента). Файлы текстовые, имена файлов совпадают с именами описанных выше ведущих на них ссылок
(3) В тексте каждого документа найдет объем торгов на срочном рынке (одно и то же однотипное место посреди текста, пример на скриншоте ниже) и приведет их к рублям (умножив на число с соответствующим числом нулей в зависимости от подписи трлн / млрд). Можно найденное число вписывать в csv третьим столбцом напротив каждой ссылки. 