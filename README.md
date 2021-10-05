# Laravel payment processor package for PrimePayments gateway

[![Latest Stable Version](https://poser.pugx.org/dexilandazel/laravel-primepayments/v/stable)](https://packagist.org/packages/dexilandazel/laravel-primepayments)
[![Build Status](https://travis-ci.org/dexilandazel/laravel-primepayments.svg?branch=master)](https://travis-ci.org/dexilandazel/laravel-primepayments)
[![StyleCI](https://github.styleci.io/repos/165751650/shield?branch=master)](https://github.styleci.io/repos/165751650)
[![CodeFactor](https://www.codefactor.io/repository/github/dexilandazel/laravel-primepayments/badge)](https://www.codefactor.io/repository/github/dexilandazel/laravel-primepayments)
[![Total Downloads](https://img.shields.io/packagist/dt/dexilandazel/laravel-primepayments.svg?style=flat-square)](https://packagist.org/packages/dexilandazel/laravel-primepayments)
[![License](https://poser.pugx.org/dexilandazel/laravel-primepayments/license)](https://packagist.org/packages/dexilandazel/laravel-primepayments)

Accept payments via PrimePayments ([primepayments.ru](https:/primepayments.ru/)) using this Laravel framework package ([Laravel](https://laravel.com)).

- receive payments, adding just the two callbacks

#### Laravel >= 8.*, PHP >= 7.3

> To use the package for Laravel 7.* use the [3.x](https://github.com/dexilandazel/laravel-primepayments/tree/3.x) branch

> To use the package for Laravel 6.* use the [2.x](https://github.com/dexilandazel/laravel-primepayments/tree/2.x) branch

> To use the package for Laravel 5.* use the [1.x](https://github.com/dexilandazel/laravel-primepayments/tree/1.x) branch

## Installation

Require this package with composer.

``` bash
composer require dexilandazel/laravel-primepayments
```

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`

```php
DexiLandazel\PrimePayments\PrimePaymentsServiceProvider::class,
```

Add the `PrimePayments` facade to your facades array:

```php
'PrimePayments' => DexiLandazel\PrimePayments\Facades\PrimePayments::class,
```

Copy the package config to your local config with the publish command:
``` bash
php artisan vendor:publish --provider="DexiLandazel\PrimePayments\PrimePaymentsServiceProvider"
```

## Configuration

Once you have published the configuration files, please edit the config file in `config/primepayments.php`.

- Create an account on [primepayments.ru](http://primepayments.ru)
- Add your project, copy the `project_id`, `secret_key` and `secret_key_second` params and paste into `config/primepayments.php`
- After the configuration has been published, edit `config/primepayments.php`
- Set the callback static function for `searchOrder` and `paidOrder`
- Create route to your controller, and call `PrimePayments::handle` method
 
## Usage

1) Generate a payment url or get redirect:

```php
$sum = 100; // Payment`s sum

$url = PrimePayments::getPayUrl($sum, $order_id, $email, $comment);

$redirect = PrimePayments::redirectToPayUrl($sum, $order_id, $email, $comment);
```

You can add custom fields to your payment:

```php

$url = PrimePayments::getPayUrl($sum, $order_id, $email, $comment);

$redirect = PrimePayments::redirectToPayUrl($sum, $order_id, $email, $comment);
```

`$email` and `$phone` can be null.

2) Process the request from PrimePayments:
``` php
PrimePayments::handle(Request $request)
```

## Important

You must define callbacks in `config/primepayments.php` to search the order and save the paid order.


``` php
'searchOrder' => null  // PrimePaymentsController@searchOrder(Request $request)
```

``` php
'paidOrder' => null  // PrimePaymentsController@paidOrder(Request $request, $order)
```

## Example

The process scheme:

1. The request comes from `primepayments.ru` `GET` / `POST` `http://yourproject.com/primepayments/result` (with params).
2. The function`PrimePaymentsController@handlePayment` runs the validation process (auto-validation request params).
3. The method `searchOrder` will be called (see `config/primepayments.php` `searchOrder`) to search the order by the unique id.
4. If the current order status is NOT `order_payed` in your database, the method `paidOrder` will be called (see `config/primepayments.php` `paidOrder`).

Add the route to `routes/web.php`:
``` php
 Route::get('/primepayments/result', 'PrimePaymentsController@handlePayment');
```

> **Note:**
don't forget to save your full route url (e.g. http://example.com/primepayments/result ) for your project on [primepayments.ru](primepayments.ru).

Create the following controller: `/app/Http/Controllers/PrimePaymentsController.php`:

``` php
class PrimePaymentsController extends Controller
{
    /**
     * Search the order in your database and return that order
     * to paidOrder, if status of your order is 'order_payed'
     *
     * @param Request $request
     * @param $order_id
     * @return bool|mixed
     */
    public function searchOrder(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        if($order) {
            $order['_orderSum'] = $order->sum;

            // If your field can be `order_payed` you can set them like string
            $order['_orderStatus'] = $order['status'];

            // Else your field doesn` has value like 'order_payed', you can change this value
            $order['_orderStatus'] = ('1' == $order['status']) ? 'order_payed' : false;

            return $order;
        }

        return false;
    }

    /**
     * When paymnet is check, you can paid your order
     *
     * @param Request $request
     * @param $order
     * @return bool
     */
    public function paidOrder(Request $request, $order)
    {
        $order->status = 'order_payed';
        $order->save();

        //

        return true;
    }

    /**
     * Start handle process from route
     *
     * @param Request $request
     * @return mixed
     */
    public function handlePayment(Request $request)
    {
        return PrimePayments::handle($request);
    }
}
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please send me an email at mail@gmail.com instead of using the issue tracker.

## Credits

- [DexiLandazel](https://github.com/DexiLandazel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
