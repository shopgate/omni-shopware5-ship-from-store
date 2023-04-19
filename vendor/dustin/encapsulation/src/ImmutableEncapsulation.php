<?php

namespace Dustin\Encapsulation;

/**
 * An {@see} ArrayEncapsulation which cannot be changed after initialization.
 */
class ImmutableEncapsulation extends ArrayEncapsulation
{
    use ImmutableTrait;
}
