<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

interface ValueConverterInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value);
}
