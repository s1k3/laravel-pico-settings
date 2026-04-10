# Create a laravel settings package

Basically it will be laravel 13 and php 8.3+ based package for key value pair and can be user specific or model specific

# The service Provider Class name should be Pico/Settings


# Schema for the table

There should be **settings** table that will have the following fields

- id autoincreament primary key
- user_id this will be foreign_key for users table id. users table should be fetched from config file pico-settings.php from attribute
- model name of the model class
```php

return [
    'user_table' => 'users
];

```
- key field
- value field that has to be long text

**By default user_id and model could be null**

# Primary interface

To set the value you should have something like this

```php

    Settings::for($user)->model($model)->get('key', 'default') // to get single value
    Settings::for($user)->model($model)->get(['key', 'value'], 'default') // to get multiple value
    Settings::for($user)->model($model)->set('key', 'value') // set single valuee
    Settings::for($user)->model($model)->set(['key' => 'value', 'key1' => 'value1']) // set single valuee

```

Should also have **settings()** helper 

```php

    settings()->for($user)->model($model)->get('key', 'default') // to get single value
    settings()->for($user)->model($model)->get(['key', 'value'], 'default') // to get multiple value
    settings()->for($user)->model($model)->set('key', 'value') // set single valuee
    settings()->for($user)->model($model)->set(['key' => 'value', 'key1' => 'value1']) // set single valuee
```

# Ability to cache the value

 This package should a ability to cache the value to json file after it read once that value seperated by user_id.
 After an set operation this will update the json and update the database

Use php 8.3+ syntax if possible use match instead of with

