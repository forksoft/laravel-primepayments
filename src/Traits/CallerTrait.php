<?php

namespace DexiLandazel\PrimePayments\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use DexiLandazel\PrimePayments\Exceptions\InvalidPaidOrder;
use DexiLandazel\PrimePayments\Exceptions\InvalidSearchOrder;

trait CallerTrait
{
    /**
     * @param Request $request
     * @return mixed
     *
     * @throws InvalidSearchOrder
     */
    public function callSearchOrder(Request $request)
    {
        if (is_null(config('primepayments.searchOrder'))) {
            throw new InvalidSearchOrder();
        }

        return App::call(config('primepayments.searchOrder'), ['order_id' => $request->input('innerID')]);
    }

    /**
     * @param Request $request
     * @param $order
     * @return mixed
     * @throws InvalidPaidOrder
     */
    public function callPaidOrder(Request $request, $order)
    {
        if (is_null(config('primepayments.paidOrder'))) {
            throw new InvalidPaidOrder();
        }

        return App::call(config('primepayments.paidOrder'), ['order' => $order]);
    }
}
