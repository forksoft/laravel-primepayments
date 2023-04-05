<?php

namespace Forksoft\PrimePayments;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Forksoft\PrimePayments\Traits\CallerTrait;
use Forksoft\PrimePayments\Traits\ValidateTrait;

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
    public function getPayUrl($sum, $order_id, $email, $comment): string
    {
        // Url to init payment on PrimePayments
        $url = config('primepayments.pay_url');

        // Array of url query
        $query = [];

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

        // User email
        $query['email'] = $email;

        $query['sign'] = $this->getSignature(
            config('primepayments.project_id'),
            config('primepayments.secret_key'),
            config('primepayments.currency'),
            $query
        );

        // Payment description
        $query['comment'] = $comment;

        // Merge url ang query and return
        return $url.'?'.http_build_query($query);
    }

    /**
     * @param string $ip
     * @return bool
     */
    public function allowIP($ip): bool
    {
        // Allow local ip
        if ($ip === '127.0.0.1') {
            return true;
        }

        return \in_array($ip, config('primepayments.allowed_ips'), true);
    }

    /**
     * @param $project_id
     * @param $secret
     * @param $currency
     * @param $params
     * @return string
     */
    public function getSignature($project_id, $secret, $currency, $params): string
    {
        $hashStr = $secret . $params['action'] . $project_id . $params['sum'] . $currency . $params['innerID'] . $params['email'];

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
    public function responseError($error): string
    {
        return config('primepayments.errors.'.$error, $error);
    }

    /**
     * @return string
     */
    public function responseOK(): string
    {
        // Must return 'OK' if paid successful
        // https://primepayments.ru/doc/?page=api

        return 'OK';
    }
}
