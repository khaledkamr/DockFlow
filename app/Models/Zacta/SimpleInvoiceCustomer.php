<?php
namespace App\Zacta;


class SimpleInvoiceCustomer
{
    public $name;
    public $street;
    public $city;
    public $subDivision;
    public $building;
    public $plot;
    public $postal;
    public $xmlString = '<cac:Party>
            <cac:PostalAddress>
                <cbc:StreetName>@STREET@</cbc:StreetName>
                <cbc:BuildingNumber>@BUILDING@</cbc:BuildingNumber>
                <cbc:PlotIdentification>@PLOT@</cbc:PlotIdentification>
                <cbc:CitySubdivisionName>@SUBDIVISION@</cbc:CitySubdivisionName>
                <cbc:CityName>@CITY@</cbc:CityName>
                <cbc:PostalZone>@POSTAL@</cbc:PostalZone>
                <cac:Country>
                    <cbc:IdentificationCode>SA</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>@NAME@</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>';
    public function __construct($name,$street,$city,$subDivision,$building,$plot,$postal)
    {
        $this->name = $name;
        $this->street = $street;
        $this->city = $city;
        $this->subDivision = $subDivision;
        $this->building = $building;
        $this->plot = $plot;
        $this->postal = $postal;
    }

    public function replaceXMLString(){
        $content = $this->xmlString;
        $content = str_replace("@STREET@",$this->street,$content);
        $content = str_replace("@BUILDING@",$this->building,$content);
        $content = str_replace("@PLOT@",$this->plot,$content);
        $content = str_replace("@SUBDIVISION@",$this->subDivision,$content);
        $content = str_replace("@CITY@",$this->city,$content);
        $content = str_replace("@POSTAL@",$this->postal,$content);
        $content = str_replace("@NAME@",$this->name,$content);
        $this->xmlString = $content;
    }
}
