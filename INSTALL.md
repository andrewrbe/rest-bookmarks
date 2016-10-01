Установка
==============
Пример конфига для nginx
--------
```nginx

server {
    server_name rest-bookmarks.lo;
    root /home/andrew/projects/rest-bookmarks/public_html;

    location / {
        # try to serve file directly, fallback to front controller
        try_files $uri /index.php$is_args$args;
    }

    # If you have 2 front controllers for dev|prod use the following line instead
    # location ~ ^/(index|index_dev)\.php(/|$) {
    location ~ ^/index\.php(/|$) {
        # the ubuntu default
	fastcgi_pass unix:/var/run/php5-fpm.sock;

        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
	fastcgi_param APPLICATION_ENV  development;

        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Enable the internal directive to disable URIs like this
        # internal;
    }

    #return 404 for all php files as we do have a front controller
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/rest-bookmarks.error.log;
    access_log /var/log/nginx/rest-bookmarks.access.log;
}
```
Sqlite таблицы
--------
```sql
CREATE TABLE `bookmarks` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL ,
  `uid` varchar(15),
  `url` varchar(2000) NOT NULL,
  `created_at` DATETIME
);
CREATE UNIQUE INDEX bookmarks_uid_uindex ON bookmarks(uid);
CREATE UNIQUE INDEX bookmarks_url_uindex ON bookmarks(url);

CREATE TABLE `comments` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL ,
    `bookmark_id` INT NULL,
    `uid` varchar(15),
    `text` TEXT,
    `ip` varchar(15),
    `created_at` DATETIME
);
CREATE UNIQUE INDEX comments_uid_uindex ON comments(uid);
```