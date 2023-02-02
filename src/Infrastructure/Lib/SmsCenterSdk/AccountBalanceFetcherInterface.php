<?php

namespace App\Infrastructure\Lib\SmsCenterSdk;

interface AccountBalanceFetcherInterface
{
    /**
     * @throws SailPlayApiException
     */
    public function fetch(): float;
}
