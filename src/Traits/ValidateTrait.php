<?php

namespace DexiLandazel\PrimePayments\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidateTrait
{
    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:order_payed,order_cancel',
            'orderID' => 'required',
            'date_pay' => 'required',
            'payWay' => 'required',
            'innerID' => 'required',
            'sum' => 'required',
            'currency' => 'required',
            'email' => 'required',
            'webmaster_profit' => 'required',
            'sign' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validateSignature(Request $request)
    {
        $sign = $this->getSignature($request->input('sum'), config('primepayments.secret_key_second'), $request->input('orderID'), $request->input('innerID'), $request->input('payWay'), $request->input('webmaster_profit'));

        if ($request->input('sign') != $sign) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validateOrderFromHandle(Request $request)
    {
        return $this->AllowIP($request->ip())
                    && $this->validate($request)
                    && $this->validateSignature($request);
    }
}
