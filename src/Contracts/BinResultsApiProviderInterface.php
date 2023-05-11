<?php

namespace Commission\Contracts;

interface BinResultsApiProviderInterface
{
    public function getBinResults(string $bin): array;
}