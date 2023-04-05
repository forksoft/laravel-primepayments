<?php

namespace Forksoft\PrimePayments\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string handle(Request $request)
 * @method static string getPayUrl($sum, $order_id, $email, $comment)
 * @method static string redirectToPayUrl($sum, $order_id, $email, $comment)
 * @method static string getSignature($project_id, $sum, $secret, $order_id, $currency, $email)
 *
 * @see \Forksoft\PrimePayments\PrimePayments
 */
class PrimePayments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'primepayments';
    }
}
