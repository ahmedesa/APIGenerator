## Installation
```
composer require essa/api_generator
```



### Exceptions:

`App\Providers\Handler.php`:

```php

use essa\APIGenerator\Exceptions\JsonHandler;

class Handler extends JsonHandler
{    

}


```
 
### Controller:

`App\Http\Controllers\Controller.php`:

```php
use essa\APIGenerator\Http\ApiResponse;

class Controller extends BaseController
{
    use ApiResponse;
}
```



## Usage

Create component
```
php artisan make:module Admin
```

```
php artisan make:module Admin --with-image
```