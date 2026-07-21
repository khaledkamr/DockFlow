<?php
namespace App\Models\Zacta;
use DOMDocument;
class TaxInvoice
{
    public $invoiceLines = [];
    public $groupedInvoiceLines = [];
    public $linesXMLString = "";
    public $totalWithoutVat = 0;
    public $vatAmount = 0;
    public $totalAfterVat = 0;
    public $invoiceDate;
    public $invoiceTime;
    public $invoiceNo;
    public $invoiceCounter;
    public $deliveryDate;
    public $customer;
    public $seller;
    public $document;
    public $uuid;
    public $preHash;
    public $hash;
    public $privateKey;
    public $certificate;
    public $certitifcateInfo;
    public $encodedInvoice;
    public $qr;
    public $step6Document;
    public $zactaInvoice;
    public $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
    <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
        <ext:UBLExtensions>
            <ext:UBLExtension>
                <ext:ExtensionURI>urn:oasis:names:specification:ubl:dsig:enveloped:xades</ext:ExtensionURI>
                <ext:ExtensionContent>
                    <sig:UBLDocumentSignatures xmlns:sig="urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2" xmlns:sac="urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2" xmlns:sbc="urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2">
                        <sac:SignatureInformation>
                            <cbc:ID>urn:oasis:names:specification:ubl:signature:1</cbc:ID>
                            <sbc:ReferencedSignatureID>urn:oasis:names:specification:ubl:signature:Invoice</sbc:ReferencedSignatureID>
                            <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="signature">
                                <ds:SignedInfo>
                                    <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2006/12/xml-c14n11"/>
                                    <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256"/>
                                    <ds:Reference Id="invoiceSignedData" URI="">
                                        <ds:Transforms>
                                            <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
                                                <ds:XPath>not(//ancestor-or-self::ext:UBLExtensions)</ds:XPath>
                                            </ds:Transform>
                                            <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
                                                <ds:XPath>not(//ancestor-or-self::cac:Signature)</ds:XPath>
                                            </ds:Transform>
                                            <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
                                                <ds:XPath>not(//ancestor-or-self::cac:AdditionalDocumentReference[cbc:ID="QR"])</ds:XPath>
                                            </ds:Transform>
                                            <ds:Transform Algorithm="http://www.w3.org/2006/12/xml-c14n11"/>
                                        </ds:Transforms>
                                        <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                        <ds:DigestValue></ds:DigestValue>
                                    </ds:Reference>
                                    <ds:Reference Type="http://www.w3.org/2000/09/xmldsig#SignatureProperties" URI="#xadesSignedProperties">
                                        <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                        <ds:DigestValue></ds:DigestValue>
                                    </ds:Reference>
                                </ds:SignedInfo>
                                <ds:SignatureValue></ds:SignatureValue>
                                <ds:KeyInfo>
                                    <ds:X509Data>
                                        <ds:X509Certificate></ds:X509Certificate>
                                    </ds:X509Data>
                                </ds:KeyInfo>
                                <ds:Object>
                                    <xades:QualifyingProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Target="signature">
                                        <xades:SignedProperties Id="xadesSignedProperties">
                                            <xades:SignedSignatureProperties>
                                                <xades:SigningTime>2023-12-20T12:43:14Z</xades:SigningTime>
                                                <xades:SigningCertificate>
                                                    <xades:Cert>
                                                        <xades:CertDigest>
                                                            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                                            <ds:DigestValue></ds:DigestValue>
                                                        </xades:CertDigest>
                                                        <xades:IssuerSerial>
                                                            <ds:X509IssuerName></ds:X509IssuerName>
                                                            <ds:X509SerialNumber></ds:X509SerialNumber>
                                                        </xades:IssuerSerial>
                                                    </xades:Cert>
                                                </xades:SigningCertificate>
                                            </xades:SignedSignatureProperties>
                                        </xades:SignedProperties>
                                    </xades:QualifyingProperties>
                                </ds:Object>
                            </ds:Signature>
                        </sac:SignatureInformation>
                    </sig:UBLDocumentSignatures>
                </ext:ExtensionContent>
            </ext:UBLExtension>
        </ext:UBLExtensions>
        <cbc:ProfileID>reporting:1.0</cbc:ProfileID>
        <cbc:ID>@INVOICENUMBER@</cbc:ID>
        <cbc:UUID>807a7f88-b33a-4740-b689-98143ebc7c38</cbc:UUID>
        <cbc:IssueDate>@INVOICEDATE@</cbc:IssueDate>
        <cbc:IssueTime>@INVOICETIME@</cbc:IssueTime>
        <cbc:InvoiceTypeCode name="0100000">388</cbc:InvoiceTypeCode>
        <cbc:DocumentCurrencyCode>SAR</cbc:DocumentCurrencyCode>
        <cbc:TaxCurrencyCode>SAR</cbc:TaxCurrencyCode>
        <cac:AdditionalDocumentReference>
            <cbc:ID>ICV</cbc:ID>
            <cbc:UUID>@COUNTER@</cbc:UUID>
        </cac:AdditionalDocumentReference>
        <cac:AdditionalDocumentReference>
            <cbc:ID>PIH</cbc:ID>
            <cac:Attachment>
                <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain">@PIH@</cbc:EmbeddedDocumentBinaryObject>
            </cac:Attachment>
        </cac:AdditionalDocumentReference>
        <cac:AdditionalDocumentReference>
            <cbc:ID>QR</cbc:ID>
            <cac:Attachment>
                <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain"></cbc:EmbeddedDocumentBinaryObject>
            </cac:Attachment>
        </cac:AdditionalDocumentReference>
        <cac:Signature>
            <cbc:ID>urn:oasis:names:specification:ubl:signature:Invoice</cbc:ID>
            <cbc:SignatureMethod>urn:oasis:names:specification:ubl:dsig:enveloped:xades</cbc:SignatureMethod>
        </cac:Signature>
        <cac:AccountingSupplierParty>
            @INVOICESELLER@
        </cac:AccountingSupplierParty>
        <cac:AccountingCustomerParty>
            @INVOICECUSTOMER@
        </cac:AccountingCustomerParty>
        <cac:Delivery>
            <cbc:ActualDeliveryDate>@DELIVERYDATE@</cbc:ActualDeliveryDate>
        </cac:Delivery>
        <cac:PaymentMeans>
            <cbc:PaymentMeansCode>10</cbc:PaymentMeansCode>
        </cac:PaymentMeans>
        <cac:AllowanceCharge>
            <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
            <cbc:AllowanceChargeReason>discount</cbc:AllowanceChargeReason>
            <cbc:Amount currencyID="SAR">0.00</cbc:Amount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeAgencyID="6">S</cbc:ID>
                <cbc:Percent>15</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:AllowanceCharge>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="SAR">@INVOICEVATAMOUNT@</cbc:TaxAmount>
        </cac:TaxTotal>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="SAR">@INVOICEVATAMOUNT@</cbc:TaxAmount>
            @TAXSUBTOTALS@
        </cac:TaxTotal>
        <cac:LegalMonetaryTotal>
            <cbc:LineExtensionAmount currencyID="SAR">@INVOICETAXABLEAMOUNT@</cbc:LineExtensionAmount>
            <cbc:TaxExclusiveAmount currencyID="SAR">@INVOICETAXABLEAMOUNT@</cbc:TaxExclusiveAmount>
            <cbc:TaxInclusiveAmount currencyID="SAR">@INVOICETOTALAMOUNT@</cbc:TaxInclusiveAmount>
            <cbc:AllowanceTotalAmount currencyID="SAR">0.00</cbc:AllowanceTotalAmount>
            <cbc:PrepaidAmount currencyID="SAR">0.00</cbc:PrepaidAmount>
            <cbc:PayableAmount currencyID="SAR">@INVOICETOTALAMOUNT@</cbc:PayableAmount>
        </cac:LegalMonetaryTotal>
        @INVOICELINES@
    </Invoice>';

    public function __construct($invoiceNumber, $date, $time, $counter, $deliveryDate) {
        $this->invoiceNo = $invoiceNumber;
        $this->invoiceDate = str_replace(["/","."],"-",$date);
        $this->invoiceTime = $time;
        $this->invoiceCounter = $counter;
        $this->deliveryDate = str_replace(["/","."],"-",$deliveryDate);
    }

    public function addInvoiceLine($invoiceLine) {
        $this->invoiceLines[] = $invoiceLine;
    }

    public function replaceXMLDocumentInfo() {
        $content = $this->xmlContent;
        $content = str_replace("@INVOICENUMBER@", $this->invoiceNo, $content);
        $content = str_replace("@INVOICEDATE@", $this->invoiceDate, $content);
        $content = str_replace("@INVOICETIME@", $this->invoiceTime, $content);
        $content = str_replace("@COUNTER@", $this->invoiceCounter, $content);
        $content = str_replace("@DELIVERYDATE@", $this->deliveryDate, $content);
        $this->xmlContent =  $content;
    }

    public function getInvoiceLinesXMLString() {
        $lines =[];
        foreach($this->invoiceLines as $invoiceLine) {
            $lines[] = $invoiceLine->invoiceLineXMLString;
        }
        $this->linesXMLString = implode("\n", $lines);
    }

    public function replaceXMLDocumentInvoiceLine() {
        $this->xmlContent = str_replace("@INVOICELINES@", $this->linesXMLString, $this->xmlContent);
    }

    public function generateInvoiceLines() {
        $this->populateInvoiceLines();
        $this->getInvoiceLinesXMLString();
        $this->replaceXMLDocumentInvoiceLine();
    }

    public function getTaxInvoiceDocument() {
        $this->replaceXMLDocumentInfo();
        $this->generateInvoiceLines();
        $this->generateInvoiceTotals();
        $this->document = new DOMDocument();
        //$xmlDocument = file_get_contents($path);
        $this->document->loadXML($this->xmlContent);
    }

    public function calculateInvoiceTotals() {
        foreach($this->invoiceLines as $invoiceLine) {
            $category = $invoiceLine->taxCategory;
            
            if(!isset($this->groupedInvoiceLines[$category])) {
                $this->groupedInvoiceLines[$category] = [
                    'taxable' => 0,
                    'tax' => 0,
                    'percent' => $invoiceLine->vat,
                ];
            }

            $this->groupedInvoiceLines[$category]['taxable'] += $invoiceLine->totalLineWithoutVat;
            $this->groupedInvoiceLines[$category]['tax'] += $invoiceLine->vatAmount;

            $this->totalWithoutVat += $invoiceLine->totalLineWithoutVat;
            $this->vatAmount += $invoiceLine->vatAmount;
            $this->totalAfterVat += $invoiceLine->totalAfterVat;
        }
    }

    public function generateTaxSubtotals() {
        $xml = '';

        if(isset($this->groupedInvoicedLines['O'])) {
            $taxable = number_format($this->groupedInvoiceLines['O']['taxable'], 2, '.', '');
            $xml .= "
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID=\"SAR\">{$taxable}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID=\"SAR\">0.00</cbc:TaxAmount>
    
                <cac:TaxCategory>
                    <cbc:ID schemeID=\"UN/ECE 5305\" schemeAgencyID=\"6\">O</cbc:ID>
                    <cbc:Percent>0.00</cbc:Percent>
    
                    <cbc:TaxExemptionReasonCode>VATEX-SA-OOS</cbc:TaxExemptionReasonCode>
    
                    <cbc:TaxExemptionReason>
                        Disbursement paid on behalf of customer.
                    </cbc:TaxExemptionReason>
    
                    <cac:TaxScheme>
                        <cbc:ID schemeID=\"UN/ECE 5153\" schemeAgencyID=\"6\">VAT</cbc:ID>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>";
        }

        if(isset($this->groupedInvoiceLines['S'])) {
            $taxable = number_format($this->groupedInvoiceLines['S']['taxable'], 2, '.', '');
            $tax = number_format($this->groupedInvoiceLines['S']['tax'], 2, '.', '');
            $xml .= "
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID=\"SAR\">{$taxable}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID=\"SAR\">{$tax}</cbc:TaxAmount>
    
                <cac:TaxCategory>
                    <cbc:ID schemeID=\"UN/ECE 5305\" schemeAgencyID=\"6\">S</cbc:ID>
                    <cbc:Percent>15.00</cbc:Percent>
    
                    <cac:TaxScheme>
                        <cbc:ID schemeID=\"UN/ECE 5153\" schemeAgencyID=\"6\">VAT</cbc:ID>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>";
        }

        return $xml;
    }

    public function replaceXMLDocumentTotals() { 
        $content = $this->xmlContent;
        $content = str_replace("@INVOICEVATAMOUNT@", number_format($this->vatAmount, 2, '.', ''), $content);
        $content = str_replace("@INVOICETAXABLEAMOUNT@", number_format($this->totalWithoutVat, 2, '.', ''), $content);
        $content = str_replace("@INVOICETOTALAMOUNT@", number_format($this->totalAfterVat, 2, '.', ''), $content);
        $content = str_replace("@TAXSUBTOTALS@", $this->generateTaxSubtotals(), $content);
        $this->xmlContent =  $content;
    }

    public function generateInvoiceTotals() {
        $this->calculateInvoiceTotals();
        $this->replaceXMLDocumentTotals();
    }

    public function processInvoice($preHash, $path=null) {
        $this->getTaxInvoiceDocument();
    }
}