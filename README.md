Как пользоваться API
==============
Общие положения
-----------
Все успешные результаты запросов отдаеются с http-кодом 200. Все неуспешние с кодом 500.
В обоих случаях возвращается json. Это дает возможность разбирать ошибки и успешные обращения в разных колбеках. 
При ошибке ошибке воз вращается json с полем error, в котором описанием ошибки.
Для запросов используются GET, PUT, POST, DELETE http-методы.
Пример ниже демонстрирует удаление комментария и использованием jQuery.
 
```javascript
 $.ajax({
            url: '/api/comment',
            method: 'DELETE',
            data: {uid: '123123123'},
            success: function (data) {
            	console.log(data);
            },
            error: function (data) {
                console.log(data.responseJSON);
            }
        })
```

Если все прошло успешно, в консоль напечатается объект _{"success": true}_
Если нет, мо получим сообщение с причиной. Например:  _{"error": "Comment was not found"}_

Получение последних записей
---------------------------
10 последних записей поступны по GET запросу на /api/bookmarks :
```javascript
 $.ajax({
        'url': '/api/bookmarks',
        method: 'GET',
        success: function (data) {
              console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Запрос возвращает массив из 10 объектов отсортированных по времени добавления. Самый новый - первый. 
Если закладок всего меньше 10ти - возвращается сколько есть.
Каждая bookmark предатавлена объектом с такими полями:
* uid: Уникальный индетификатор. 
* created_at: Дата в формате стандарта ISO 8601. Пример - "2016-10-01T01:20:40+03:00". Время и часовой пояс бекенда. 
* url: Добавленная ссылка

Получение закладки с комментариями по ссылке
--------------------------------------------
GET запрос на /api/bookmark ссылка передается в переменной url :
```javascript
 $.ajax({
        'url': '/api/bookmark',
        method: 'GET',
        data: {url: 'http://google.com/'},
        success: function (data) {
                    	console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Возвращает объект bookmark. К стандартным свойствам будет добавлен массив comments c такими полями:
* uid: Уникальный индетификатор.
* created_at: Дата в формате стандарта ISO 8601. Пример - "2016-10-01T01:20:40+03:00". Время и часовой пояс бекенда.
* ip: Адрес пользователя, которй добавил комментарий
* text: Сам комментрий



Добавление bookmark
--------------------------------------------
PUT запрос на /api/bookmark ссылка передается в переменной url :
```javascript
 $.ajax({
        'url': '/api/bookmark',
        method: 'PUT',
        data: {url: 'http://google.com/'},
        success: function (data) {
                    	console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Возвращает объект со свойством uid - индетификатором bookmark

Добавление comment
--------------------------------------------
PUT запрос на /api/comment uid-bookmark передается в переменной uid, текст комментраия в text:
```javascript
 $.ajax({
        'url': '/api/comment',
        method: 'PUT',
        data: {uid: '123123123', text: 'Comment'},
        success: function (data) {
                    	console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Возвращает объект со свойством uid - индетификатором comment


Редактирование comment
--------------------------------------------
POST запрос на /api/comment uid-comment передается в переменной uid, сам комментарий в text:
```javascript
 $.ajax({
        'url': '/api/comment',
        method: 'POST',
        data: {uid: '123123123', text: 'new comment'},
        success: function (data) {
            console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Возвращает объект со свойством success:true. При редактировании проверяется ip-адрес, он должен совпадать с тем, с которым комментарий создавался.
Комментарий можно редакрировать не позже одного часа после создания.


Удаление comment
--------------------------------------------
DELETE запрос на /api/comment uid-comment передается в переменной uid:
```javascript
 $.ajax({
        'url': '/api/comment',
        method: 'DELETE',
        data: {uid: '123123123'},
        success: function (data) {
            console.log(data);
		},
        error: function (data) {
        	console.log(data.responseJSON);
        }
    })
```
Возвращает объект со свойством success:true. При редактировании проверяется ip-адрес, он должен совпадать с тем, с которым комментарий создавался.
Комментарий можно редакрировать не позже одного часа после создания.