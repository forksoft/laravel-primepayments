<?php

return [

    /*
     * Project`s id
     */
    'project_id' => env('PRIMEPAYMENTS_PROJECT_ID', ''),

    /*
     * First project`s secret key
     */
    'secret_key' => env('PRIMEPAYMENTS_SECRET_KEY', ''),

    /*
     * Second project`s secret key
     */
    'secret_key_second' => env('PRIMEPAYMENTS_SECRET_KEY_SECOND', ''),

    /*
     * Allowed currenc'ies https://primepayments.ru/doc/?page=api
     *
     * If currency = null, that parameter doesn`t be setted
     */
    'currency' => env('PRIMEPAYMENTS_CURRENCY', 'RUB'),

    /*
     * Allowed ip's
     */
    'allowed_ips' => [
        '37.1.217.38'
    ],

    /*
     *  SearchOrder
     *  Search order in the database and return order details
     *  Must return array with:
     *
     *  _orderStatus
     *  _orderSum
     */
    'searchOrder' => null, //  'App\Http\Controllers\PrimePaymentsController@searchOrder',

    /*
     *  PaidOrder
     *  If current _orderStatus from DB != paid then call PaidOrderFilter
     *  update order into DB & other actions
     */
    'paidOrder' => null, //  'App\Http\Controllers\PrimePaymentsController@paidOrder',

    /*
     * Customize error messages
     */
    'errors' => [
        'validateOrderFromHandle' => 'Validate Order Error',
        'searchOrder' => 'Search Order Error',
        'paidOrder' => 'Paid Order Error',
    ],

    /*
     * Url to init payment on PrimePayments
     * https://primepayments.ru/doc/?page=api
     */
    'pay_url' => 'https://pay.primepayments.ru/API/v1/',
];
