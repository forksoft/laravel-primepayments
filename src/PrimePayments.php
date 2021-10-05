<?php

namespace DexiLandazel\PrimePayments;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DexiLandazel\PrimePayments\Traits\CallerTrait;
use DexiLandazel\PrimePayments\Traits\ValidateTrait;

class PrimePayments
{
    use ValidateTrait;
    use CallerTrait;

    //

    /**
     * PrimePayments constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $sum
     * @param $order_id
     * @param $email
     * @param $comment
     * @return string
     */
    public function getPayUrl($sum, $order_id, $email, $comment)
    {


        // Array of url query
        $query = [];

        // Url to init payment on PrimePayments
        $query['url'] = config('primepayments.pay_url');

        // Action of payment
        $query['action'] = 'initPayment';

        // Project id (merchat id)
        $query['project'] = config('primepayments.project_id');

        // Sum of payment
        $query['sum'] = $sum;

        // Payment currency
        $query['currency'] = config('primepayments.currency');

        // Order id
        $query['innerID'] = $order_id;

        // User email (optional)
        if (! is_null($email)) {
            $query['email'] = $email;
        }

        $query['sign'] = $this->getFormSignature(
            config('primepayments.project_id'),
            $sum,
            config('primepayments.secret_key'),
            $order_id,
            config('primepayments.currency'),
            $email
        );

        // Payment description
        $query['comment'] = $comment;
        // Merge url ang query and return
        return $query;
    }

    /**
     * @param $sum
     * @param $order_id
     * @param $email
     * @param $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToPayUrl($sum, $order_id, $email, $comment)
    {
        return view('posts.primepayments', $this->getPayUrl($sum, $order_id, $email, $comment));
//        return redirect()->away($this->getPayUrl($sum, $order_id, $email, $comment));
    }

    /**
     * @param string $ip
     * @return bool
     */
    public function allowIP($ip)
    {
        // Allow local ip
        if ($ip == '127.0.0.1') {
            return true;
        }

        return in_array($ip, config('primepayments.allowed_ips'));
    }

    /**
     * @param $project_id
     * @param $sum
     * @param $secret
     * @param $order_id
     * @param $currency
     * @param $email
     * @return string
     */
    public function getFormSignature($project_id, $sum, $secret, $order_id, $currency, $email)
    {
        $hashStr = $secret . 'initPayment' . $project_id . $sum . $currency . $order_id . $email;

        return md5($hashStr);
    }

    /**
     * @param $sum
     * @param $secretSecond
     * @param $order_id
     * @param $payWay
     * @param $webmaster_profit
     * @return string
     */
    public function getSignature($sum, $secretSecond, $order_id, $payWay, $webmaster_profit)
    {
        $hashStr = $secretSecond . $order_id . $payWay . $order_id . $sum . $webmaster_profit;

        return md5($hashStr);
    }

    /**
     * @param Request $request
     * @return string
     * @throws Exceptions\InvalidPaidOrder
     * @throws Exceptions\InvalidSearchOrder
     */
    public function handle(Request $request)
    {
        // Validate request from PrimePayments
        if (! $this->validateOrderFromHandle($request)) {
            return $this->responseError('validateOrderFromHandle');
        }

        // Search and get order
        $order = $this->callSearchOrder($request);

        if (! $order) {
            return $this->responseError('searchOrder');
        }

        // If order already paid return success
        if (Str::lower($order['_orderStatus']) === 'order_payed') {
            return $this->responseOK();
        }

        // PaidOrder - update order info
        // if return false then return error
        if (! $this->callPaidOrder($request, $order)) {
            return $this->responseError('paidOrder');
        }

        // Order is paid and updated, return success
        return $this->responseOK();
    }

    /**
     * @param $error
     * @return string
     */
    public function responseError($error)
    {
        return config('primepayments.errors.'.$error, $error);
    }

    /**
     * @return string
     */
    public function responseOK()
    {
        // Must return 'OK' if paid successful
        // https://primepayments.ru/doc/?page=api

        return 'OK';
    }
}
