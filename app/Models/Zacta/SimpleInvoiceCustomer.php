<?php
namespace App\Models\Zacta;


class SimpleInvoiceCustomer
{
    public $name;
    public $xmlString = '
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>@NAME@</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>';

    public function __construct($name) {
        $this->name = $name;
    }

    public function replaceXMLString() {
        $content = $this->xmlString;
        $content = str_replace("@NAME@",$this->name,$content);
        $this->xmlString = $content;
    }
}
