<?php

namespace Forksoft\PrimePayments\Exceptions;

use Throwable;

class InvalidPaidOrder extends \Exception
{
    /**
     * InvalidPaidOrder constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (empty($message)) {
            $message = 'PrimePayments config: paidOrder callback not set';
        }

        parent::__construct($message, $code, $previous);
    }
}
