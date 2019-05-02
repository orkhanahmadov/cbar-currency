<?php

namespace Orkhanahmadov\CBARCurrency;

use SimpleXMLElement;

class CBARData extends SimpleXMLElement
{
    public function get(string $name)
    {
        $search = $this->xpath("//Valute[@Code='$name']");

        if (count($search) > 0) {
            return [
                'nominal' => (int) $search[0]->Nominal,
                'rate' => (float) $search[0]->Value
            ];
        }
    }
}
