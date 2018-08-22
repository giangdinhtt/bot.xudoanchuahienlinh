# bot.xudoanchuahienlinh
Copy .env.example to .env
Edit COUCHDB_* variable in .env

`php composer.phar update`

Create database: `php artisan db:seed`
Initialize dummy students: `php artisan db:seed --class=FakeUsersSeeder`