<?php

namespace Commission\Contracts;

interface ExchangeRateApiClientInterface
{
    public function getRateByCurrency($currency);
}