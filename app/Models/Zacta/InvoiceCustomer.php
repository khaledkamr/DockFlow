<?php
namespace App\Models\Zacta;

class InvoiceCustomer
{
    public $name;
    public $crn;
    public $vat;
    public $street;
    public $city;
    public $subDivision;
    public $building;
    public $plot;
    public $postal;
    public $xmlString = '<cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="CRN">@CRN@</cbc:ID>
            </cac:PartyIdentification>
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
                <cbc:CompanyID>@VAT@</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>@NAME@</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>';
        
    public function __construct($name,$vat,$crn,$street,$city,$subDivision,$building,$plot,$postal)
    {
        $this->name = htmlspecialchars(trim($name), ENT_XML1, 'UTF-8');
        $this->vat = $vat;
        $this->crn = $crn;
        $this->street = htmlspecialchars(trim($street), ENT_XML1, 'UTF-8');
        $this->city = htmlspecialchars(trim($city) ,ENT_XML1, 'UTF-8');
        $this->subDivision = htmlspecialchars(trim($subDivision),ENT_XML1, 'UTF-8');
        $this->building = $building;
        $this->plot = $plot;
        $this->postal = $postal;
    }

    public function replaceXMLString(){
        $content = $this->xmlString;
        $content = str_replace("@CRN@",$this->crn,$content);
        $content = str_replace("@VAT@",$this->vat,$content);
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
