## About Moodle Analytics

Сервис предназначен для уведомления пользователей Moodle о возможности изучения конспектов на основании пройденных тестов.
Админ панель:  /dashboard/
Регламентные задачи выполняются через планировщик задач.

## Установка на сторое Moodle
Установить плагин в систему moodle. Скопировать содержимое ./scripts/moodle_plugins в ./local/

## Установка на сторое сервиса
1) git clone https://github.com/burcev-alex/moodle_analytics.git
2) npm install
3) composer install
4) php artisan migrate
5) php artisan orchid:admin
6) php artisan storage:link
7) Установка на крон планировщика задач * * * * * php artisan schedule:run >> /dev/null 2>&1

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
