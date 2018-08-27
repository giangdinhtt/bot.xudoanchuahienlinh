# bot.xudoanchuahienlinh

Copy .env.example to .env

Edit COUCHDB_* variable in .env

`php composer.phar update`

Create database: `php artisan db:seed`

Initialize dummy students: `php artisan db:seed --class=FakeUsersSeeder`

# Telegram message format
```
(
  'update_id' => 155580231,
  'message' =>
  array (
    'message_id' => 13,
    'from' =>
    array (
      'id' => 286025420,
      'is_bot' => false,
      'first_name' => 'Giang',
      'last_name' => 'Dinh',
      'username' => 'giangdinhtt',
      'language_code' => 'en-US',
    ),
    'chat' =>
    array (
      'id' => 286025420,
      'first_name' => 'Giang',
      'last_name' => 'Dinh',
      'username' => 'giangdinhtt',
      'type' => 'private',
    ),
    'date' => 1535001394,
    'text' => 'Hi',
  ),
)
```

```
(
  'update_id' => 155580237,
  'edited_message' =>
  array (
    'message_id' => 22,
    'from' =>
    array (
      'id' => 286025420,
      'is_bot' => false,
      'first_name' => 'Giang',
      'last_name' => 'Dinh',
      'username' => 'giangdinhtt',
      'language_code' => 'en-US',
    ),
    'chat' =>
    array (
      'id' => 286025420,
      'first_name' => 'Giang',
      'last_name' => 'Dinh',
      'username' => 'giangdinhtt',
      'type' => 'private',
    ),
    'date' => 1535001926,
    'edit_date' => 1535001955,
    'text' => '/diemdanh giang dinh 123',
    'entities' =>
    array (
      0 =>
      array (
        'offset' => 0,
        'length' => 9,
        'type' => 'bot_command',
      ),
    ),
  ),
)
```

Contact format:

```
'contact' => 
    array (
      'phone_number' => '8493XXXX',
      'first_name' => 'Giang',
      'last_name' => 'Dinh',
      'user_id' => ,
    ),
```