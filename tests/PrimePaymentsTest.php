<?php

namespace DexiLandazel\PrimePayments\Test;

use Illuminate\Http\Request;
use DexiLandazel\PrimePayments\Exceptions\InvalidPaidOrder;
use DexiLandazel\PrimePayments\Exceptions\InvalidSearchOrder;
use DexiLandazel\PrimePayments\Test\Fake\Order;

class PrimePaymentsTest extends TestCase
{
    /** @test */
    public function test_env()
    {
        $this->assertEquals('testing', $this->app['env']);
    }

    /**
     * Create test request with custom method and add signature.
     *
     * @param bool $signature
     * @return Request
     */
    protected function create_test_request($signature = false)
    {
        $params = [
            'SUM' => '100',
            'intid' => '11',
            'MERCHANT_ORDER_ID' => '10',
            'PAY_WAY' => '1',
            'WEBMASTER_PROFIT' => '10',
        ];

        if ($signature === false) {
            $params['sign'] = $this->primepayments->getSignature($params['SUM'], $this->app['config']->get('primepayments.secret_key_second'), $params['MERCHANT_ORDER_ID'],$params['PAY_WAY'], $params['WEBMASTER_PROFIT']);
        } else {
            $params['sign'] = $signature;
        }

        $request = new Request($params);

        return $request;
    }

    /** @test */
    public function check_if_allow_remote_ip()
    {
        $this->assertTrue(
            $this->primepayments->allowIP('127.0.0.1')
        );

        $this->assertFalse(
            $this->primepayments->allowIP('0.0.0.0')
        );
    }

    /** @test */
    public function compare_form_signature()
    {
        $this->assertEquals(
            'e9759d5cbc80ceb8716d06d7e2adc348',
            $this->primepayments->getFormSignature('12345', '100', 'secret_key', '10','RUB', 'example@mail')
        );
    }

    /** @test */
    public function compare_signature()
    {
        $this->assertEquals(
            '7f590bc40563dc9ff96269e586ba6e65',
            $this->primepayments->getSignature('100', 'secret_key_second', '12345', '1','10')
        );
    }

    /** @test */
    public function generate_pay_url()
    {
        $url = $this->primepayments->getPayUrl(100, 10, 'example@gmail.com', 'Example comment');

        $this->assertStringStartsWith($this->app['config']->get('primepayments.pay_url'), $url);
    }

    /** @test */
    public function compare_request_signature()
    {
        $params = [
            'SUM' => '100',
            'MERCHANT_ORDER_ID' => '10',
            'PAY_WAY' => '1',
            'WEBMASTER_PROFIT' => '10',
        ];
        $this->assertEquals(
            '7f590bc40563dc9ff96269e586ba6e65',
            $this->primepayments->getSignature($params['SUM'], $this->app['config']->get('primepayments.secret_key_second'), $params['MERCHANT_ORDER_ID'],$params['PAY_WAY'], $params['WEBMASTER_PROFIT'] )
        );
    }

    /** @test */
    public function pay_order_form_validate_request()
    {
        $request = $this->create_test_request();
        $this->assertTrue($this->primepayments->validate($request));
    }

    /** @test */
    public function validate_signature()
    {
        $request = $this->create_test_request();
        $this->assertTrue($this->primepayments->validate($request));
        $this->assertTrue($this->primepayments->validateSignature($request));

        $request = $this->create_test_request('invalid_signature');
        $this->assertTrue($this->primepayments->validate($request));
        $this->assertFalse($this->primepayments->validateSignature($request));
    }

    /** @test */
    public function test_order_need_callbacks()
    {
        $request = $this->create_test_request();
        $this->expectException(InvalidSearchOrder::class);
        $this->primepayments->callSearchOrder($request);

        $request = $this->create_test_request();
        $this->expectException(InvalidPaidOrder::class);
        $this->primepayments->callPaidOrder($request, ['order_id' => '12345']);
    }

    /** @test */
    public function search_order_has_callbacks_fails()
    {
        $this->app['config']->set('primepayments.searchOrder', [Order::class, 'SearchOrderFilterFails']);
        $request = $this->create_test_request();
        $this->assertFalse($this->primepayments->callSearchOrder($request));
    }

    /** @test */
    public function paid_order_has_callbacks()
    {
        $this->app['config']->set('primepayments.searchOrder', [Order::class, 'SearchOrderFilterPaid']);
        $this->app['config']->set('primepayments.paidOrder', [Order::class, 'PaidOrderFilter']);
        $request = $this->create_test_request();
        $this->assertTrue($this->primepayments->callPaidOrder($request, ['order_id' => '12345']));
    }

    /** @test */
    public function paid_order_has_callbacks_fails()
    {
        $this->app['config']->set('primepayments.paidOrder', [Order::class, 'PaidOrderFilterFails']);
        $request = $this->create_test_request();
        $this->assertFalse($this->primepayments->callPaidOrder($request, ['order_id' => '12345']));
    }

    /** @test */
    public function payOrderFromGate_SearchOrderFilter_fails()
    {
        $this->app['config']->set('primepayments.searchOrder', [Order::class, 'SearchOrderFilterFails']);
        $request = $this->create_test_request('error');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $this->assertEquals($this->app['config']->get('primepayments.errors.validateOrderFromHandle'), $this->primepayments->handle($request));
    }

    /** @test */
    public function payOrderFromGate_method_pay_SearchOrderFilterPaid()
    {
        $this->app['config']->set('primepayments.searchOrder', [Order::class, 'SearchOrderFilterPaidforPayOrderFromGate']);
        $this->app['config']->set('primepayments.paidOrder', [Order::class, 'PaidOrderFilter']);
        $request = $this->create_test_request();

        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $this->assertEquals('OK', $this->primepayments->handle($request));
    }
}
