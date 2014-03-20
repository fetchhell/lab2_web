Постановка задачи:

Реализовать блог, в котором есть следующие возможности:
­ просмотр ленты
­ управление (CRUD) сообщениями, 
- комментарии к сообщениям
­ комментарии к комментариям
­ возможность добавить произвольное число тегов к одному сообщению блога
­ управление (CRUD) списком тегов.

Установка пакета phpmyadmin и запуск программы:

1. Установка phpmyadmin (содержит apache, mysql, php)
   sudo apt-get install phpmyadmin

2. Создать единую папку для всех сайтов, например /home/user/www
3. В этой папке создать папку сайта. Например /home/user/www/site
   В эту папку кинуть lab2.php

4. sudo cp /etc/apache2/sites-available/default /etc/apache2/sites-available/site
   Команда создает новую запись виртуального хостинга копируя стандартную запись.

5. sudo gedit /etc/apache2/sites-available/site
   и в этом файле:
   5.1 заменить все /var/www/ на /home/user/www/site/
   5.2 перед строкой "DocumentRoot /home/user/www/site/" добавить строку "ServerName site"
   Должно быть так:
   <VirtualHost *:85>
     ServerName   site
     DocumentRoot   /home/user/www/site/
   </VirtualHost>

6. Изменить порт apache на 85
   6.1  удалить symlink к phpmyadmin.conf
        rm /etc/apache2/conf.d/phpmyadmin.conf

    6.2  добавить строку в ports.conf "Listen 85"
         sudo nano /etc/apache2/ports.conf 

    6.3  создать symlink к директории phpmyadmin 
         cd /home/user/www/site/
         sudo ln -s /usr/share/phpmyadmin/

7.  Изменить порт mysql на 3306
    7.1 Проверить, на каком порту запущен mysql
        sudo netstat -tap | grep mysql

    7.2 Если не на 3306, то изменить значение порта в config-е /etc/mysql/my.cnf
    7.3 Перезагрузить сервер
        sudo service mysql restart

8. sudo a2ensite site1

9. sudo gedit /etc/hosts
   и в этом файле добавить строку
   127.0.0.1      site

10. sudo /etc/init.d/apache2 reload

11. Зайти в админку phpmyadmin http://localhost/phpmyadmin
    11.1 Создать базу данных с именем blog
    11.2 Import-ить blog.sql - таблицы в БД blog

12. Зайти на сайт http://site 

Я описывала установку на ubuntu из мануалов, прочитанных на сайтах:

1. Установка phpmyadmin : http://forum.ubuntu.ru/index.php?topic=46573.0
2. Установка нужного порта для apache : http://ubuntuforums.org/showthread.php?t=1329607
3. Установка нужного порта для mysql  : http://help.ubuntu.ru/wiki/%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE_%D0%BF%D0%BE_ubuntu_server/%D0%B1%D0%B0%D0%B7%D1%8B_%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D1%85/mysql

Я делала работу с помощью xampp под windows для браузера firefox
