<?php

namespace App\Models\Zacta;

class InvoiceLineNoVat
{
    public $itemName;
    public $price;
    public $qty;
    public $uom;
    public $vat;
    public $lineId;
    public $totalLineWithoutVat = 0;
    public $vatAmount = 0;
    public $totalAfterVat = 0;
    public $taxCategory = "Z";
    public $XMLNode;
    public $invoiceLineXMLString = '<cac:InvoiceLine>
        <cbc:ID>LINEID</cbc:ID>
        <cbc:InvoicedQuantity unitCode="LINEUOM">LINEQTY</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="SAR">LINEWITHOUTVAT</cbc:LineExtensionAmount>
        <cac:TaxTotal>
             <cbc:TaxAmount currencyID="SAR">0</cbc:TaxAmount>
             <cbc:RoundingAmount currencyID="SAR">LINETOTAL</cbc:RoundingAmount>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Name>LINENAME</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>Z</cbc:ID>
                <cbc:Percent>0.00</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="SAR">LINEPRICE</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>';
    public function __construct($itemName, $price, $qty, $vat=15.00, $uom="PCE")
    {
        $this->itemName =  htmlspecialchars(trim($itemName), ENT_XML1, 'UTF-8');
        $this->price = $price;
        $this->qty = $qty;
        $this->uom = $uom;
        $this->vat = $vat;
    }

    public function calculateLine() {
        $this->totalLineWithoutVat = round($this->price * $this->qty,2);
        $this->vatAmount = round($this->totalLineWithoutVat * ($this->vat /100),2);
        $this->totalAfterVat = round($this->totalLineWithoutVat +  $this->vatAmount,2) ;
    }

    public function populateXMLValues() {
        $string = $this->invoiceLineXMLString;
        $string = str_replace("LINEID", $this->lineId, $string);
        $string = str_replace("LINEUOM", $this->uom, $string);
        $string = str_replace("LINEQTY", number_format($this->qty, 5, '.', ''), $string);
        $string = str_replace("LINEWITHOUTVAT", number_format($this->totalLineWithoutVat, 2, '.', ''), $string);
        $string = str_replace("LINEVATAMOUNT", number_format($this->vatAmount, 2, '.', ''), $string);
        $string = str_replace("LINETOTAL", number_format($this->totalAfterVat, 2, '.', ''), $string);
        $string = str_replace("LINEVAT", number_format($this->vat, 2, '.', ''), $string);
        $string = str_replace("LINENAME", $this->itemName, $string);
        $string = str_replace("LINEPRICE", number_format($this->price, 5, '.', ''), $string);
        $this->invoiceLineXMLString = $string;

    }
    public function createInvoiceLineElement($lineId) {
        $this->lineId = $lineId;
        $this->calculateLine();
        $this->populateXMLValues();
    }
}
