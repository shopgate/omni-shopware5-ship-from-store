<?php

namespace Dustin\ImpEx\Transaction;

use Dustin\ImpEx\Encapsulation\NestedEncapsulation;

class Payload extends NestedEncapsulation implements PayloadEmergeInterface
{
    public function emergePayload(): self
    {
        return $this;
    }
}
