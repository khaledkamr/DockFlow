<?php

namespace App\Models\Zacta;

use DOMXPath;
use DOMDocument;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Http;

class TaxClearanceInvoice
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
        $this->invoiceDate = str_replace(["/", "."], "-", $date);
        $this->invoiceTime = $time;
        $this->invoiceCounter = $counter;
        $this->deliveryDate = str_replace(["/", "."], "-", $deliveryDate);
    }

    public function addInvoiceLine($invoiceLine) {
        $this->invoiceLines[] = $invoiceLine;
    }

    public function populateInvoiceLines() {
        foreach($this->invoiceLines as $count => $invoiceLine){
            $invoiceLine->createInvoiceLineElement($count + 1);
        }
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

    public function fillXMLTemplate($templatePath) {
        $this->xmlContent = file_get_contents($templatePath);
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

        if(isset($this->groupedInvoiceLines['O'])) {
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

    public function replaceXMLDocumentInfo() {
        $content = $this->xmlContent;
        $content = str_replace("@INVOICENUMBER@", $this->invoiceNo, $content);
        $content = str_replace("@INVOICEDATE@", $this->invoiceDate, $content);
        $content = str_replace("@INVOICETIME@", $this->invoiceTime, $content);
        $content = str_replace("@COUNTER@", $this->invoiceCounter, $content);
        $content = str_replace("@DELIVERYDATE@", $this->deliveryDate, $content);
        $this->xmlContent =  $content;
    }

    public function generateInvoiceLines() {
        $this->populateInvoiceLines();
        $this->getInvoiceLinesXMLString();
        $this->replaceXMLDocumentInvoiceLine();
    }

    public function generateInvoiceTotals() {
        $this->calculateInvoiceTotals();
        $this->replaceXMLDocumentTotals();
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
        $this->customer->replaceXMLString();
        $this->xmlContent = str_replace("@INVOICECUSTOMER@",$this->customer->xmlString,$this->xmlContent);
    }

    public function setSeller($seller) {
        $this->seller = $seller;
        $this->seller->replaceXMLString();
        $this->xmlContent = str_replace("@INVOICESELLER@",$this->seller->xmlString,$this->xmlContent);

    }

    public function getTaxInvoiceDocument() {
        $this->replaceXMLDocumentInfo();
        $this->generateInvoiceLines();
        $this->generateInvoiceTotals();
        $this->document = new DOMDocument();
        //$xmlDocument = file_get_contents($path);
        $this->document->loadXML($this->xmlContent);
    }

    public function generateUUID() {
        $uuid = Uuid::uuid4();
        $this->uuid = $uuid->toString();
        $xpath = $this->createXpath($this->document);
        $selectedNodes = $xpath->query("/default:Invoice/cbc:UUID");
        $uuidNode = $selectedNodes->item(0);
        if(!is_null($uuidNode)){
            $uuidNode->nodeValue = $this->uuid;
        }
    }

    // public function generateCounter()
    // {
    //     $xpath = $this->createXpath();
    //     $selectedNodes = $xpath->query("/default:Invoice/cac:AdditionalDocumentReference/cbc:UUID");
    //     $counterNode = $selectedNodes->item(0);
    //     if(!is_null($counterNode)){
    //         $counterNode->nodeValue = $this->counter;
    //     }
    // }

    public function setPreDocument() {
        $xpath = $this->createXpath();
        $selectedNodes = $xpath->query("/default:Invoice/cac:AdditionalDocumentReference[2]/cac:Attachment/cbc:EmbeddedDocumentBinaryObject");
        $node = $selectedNodes->item(0);
        if(!is_null($node)){
            $node->nodeValue = $this->preHash;
        }
    }

    public function canonicalizeInvoice($pure_invoice_string) {
       // $pure_invoice_string = $this->getPureInvoiceString($invoice_xml);
        $pure_invoice_string = str_replace('<?xml version="1.0" encoding="UTF-8"?>' . "\n", '', $pure_invoice_string);
        $pure_invoice_string = str_replace('<?xml version="1.0"?>' . "\n", '', $pure_invoice_string);
        $pure_invoice_string = str_replace('<cac:AccountingCustomerParty/>', '<cac:AccountingCustomerParty></cac:AccountingCustomerParty>', $pure_invoice_string);
        $document = new DOMDocument();
        $document->loadXML($pure_invoice_string);
        // $canonicalized = $document->C14N(true, false, null, null);
        return $document;
    }

    public function getInvoiceHash($canonicalString) {
        //$stringToHash = "a11b6fe587a50f7daffe3a7fb42dcccf32b43ee9b37d9f252d04243e54c11a3f";

        // Hash the string using SHA-256 (you can choose a different algorithm if needed)
       // $hashedString = hash('sha256', $stringToHash);

        // Convert the hexadecimal hash to Base64
        //echo  base64_encode(hex2bin($stringToHash)) . "\n";
        // echo "----------\n";
        // echo $canonicalString;
        // echo "------------\n";
       $hash = hash('sha256', trim($canonicalString));
    //     $hash = hash('sha256', trim("a11b6fe587a50f7daffe3a7fb42dcccf32b43ee9b37d9f252d04243e54c11a3f"));
        $hash =  hex2bin($hash);
        // $hash = pack('H*', $hash);
        return $hash;
       // return base64_encode($hash);
    }

    function canonicalizeXml($xmlString) {
        // Create a new DOMDocument
        $doc = new DOMDocument();

        // Load the XML string
        $doc->loadXML($xmlString);

        // Create a new DOMXPath object
        $xpath = new DOMXPath($doc);

        // Register the namespace for C14N 1.1
        $xpath->registerNamespace('c14n11', 'http://www.w3.org/2006/12/xml-c14n11');

        // Canonicalize the document using C14N 1.1 with no indentation
        $canonicalXml = $doc->C14N(false, false,);

        return $canonicalXml;
    }

    function convertToSingleLine($xmlString) {
        // Remove line breaks and indentation
        $singleLineXml = preg_replace('/\s+/', ' ', $xmlString);

        return $singleLineXml;
    }

    public function getPureInvoiceString(DOMDocument $invoice_xml,$withoutIndetation = true,$singleLine = false) {
        $document = new DOMDocument();
        $document->loadXML($invoice_xml->saveXML() );
        $node = $document->getElementsByTagName("UBLExtensions")->item(0);
        $node->parentNode->removeChild($node);

        $xpath = $this->createXpath($document);
        $path = "//cac:AdditionalDocumentReference//cbc:ID[text()='QR']";
        $nodes = $xpath->query($path);
        $node = $nodes->item(0);
        if(!is_null($node)){
            $parnet = $node->parentNode;
            $parnet->parentNode->removeChild($parnet);
        }

        $path = "//cac:Signature";
        $nodes = $xpath->query($path);
        $node = $nodes->item(0);
        if(!is_null($node)){
            $node->parentNode->removeChild($node);
        }
        $xmlString= $document->saveXML();
        $xmlString = str_replace('<?xml version="1.0" encoding="UTF-8"?>' . "\n", '', $xmlString);
        $xmlString = trim($xmlString);
        $out = $this->canonicalizeXml($xmlString);

        if($withoutIndetation){
            $canonicalXml = preg_replace('/^\s*/m', '', $out);
        }else{
            $canonicalXml = $out;
        }
        if($singleLine){
            $canonicalXml = $this->convertToSingleLine($canonicalXml);
        }
        // file_put_contents('can.xml',$xmlString);
      // file_put_contents('can.xml',$canonicalXml);

        return trim($canonicalXml);
    }

    public function createInvoiceDigitalSignature( string $private_key) {
        //echo "\n{$invoice_hash}\n";
        $cleanedup_private_key_string = $this->cleanUpPrivateKeyString($private_key);
        $wrapped_private_key_string = "-----BEGIN EC PRIVATE KEY-----\n{$cleanedup_private_key_string}\n-----END EC PRIVATE KEY-----";
        base64_encode(openssl_sign($this->hash, $binary_signature, $wrapped_private_key_string, 'sha256'));
        return base64_encode($binary_signature);
    }

    public static function cleanUpPrivateKeyString(string $private_key) {
        $private_key = str_replace('-----BEGIN EC PRIVATE KEY-----', '', $private_key);
        $private_key = str_replace('-----END EC PRIVATE KEY-----', '', $private_key);

        return trim($private_key);
    }

    public static function cleanUpCertificateString(string $certificate_string): string
    {
        $certificate_string = str_replace('-----BEGIN CERTIFICATE-----', '', $certificate_string);
        $certificate_string = str_replace('-----END CERTIFICATE-----', '', $certificate_string);

        return trim($certificate_string);
    }

    private function createXpath($document=null) {
        if(is_null($document)) {
            $xpath = new DOMXPath($this->document);
        } else {
            $xpath = new DOMXPath($document);
        }
        $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
        $xpath->registerNamespace('sig', 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2');
        $xpath->registerNamespace('sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
        $xpath->registerNamespace('sbc', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2');
        $xpath->registerNamespace('xades', 'http://uri.etsi.org/01903/v1.3.2#');
        $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $cacNamespace = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
        $xpath->registerNamespace('cac', $cacNamespace);
        $xpath->registerNamespace('default', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
        return $xpath;
    }

    public function fillSignedProperties($digestValue, $issuer, $serial) {
        $digestQuery = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties/xades:SignedSignatureProperties/xades:SigningCertificate/xades:Cert/xades:CertDigest/ds:DigestValue";
        $signingTimeQuery = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties/xades:SignedSignatureProperties/xades:SigningTime";
        $certIssuerQuery = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties/xades:SignedSignatureProperties/xades:SigningCertificate/xades:Cert/xades:IssuerSerial/ds:X509IssuerName";
        $certSerialQuery = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties//xades:SignedSignatureProperties/xades:SigningCertificate/xades:Cert/xades:IssuerSerial/ds:X509SerialNumber";
        $xpath = $this->createXpath();
        $selectedNodes = $xpath->query($digestQuery);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $digestValue;
        }
        $selectedNodes = $xpath->query($signingTimeQuery);

        $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Riyadh'));
        //$currentDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $iso8601Format = $currentDateTime->format('Y-m-d\TH:i:s\Z');
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $iso8601Format;
        }

        $selectedNodes = $xpath->query($certIssuerQuery);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $issuer;
        }

        $selectedNodes = $xpath->query($certSerialQuery);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $serial;
        }
        return $this->document;
    //    echo $docuemnt->saveXML();
    }

    public function getCertificateHash($cleanup_certificate_string): string
    {
        $hash = openssl_digest($cleanup_certificate_string, 'sha256');
        //$hash = openssl_digest("69a95fc237b42714dc4457a33b94cc452fd9f110504c683c401144d9544894fb", 'sha256');
        //echo $hash . "\n";
       // echo base64_encode($hash);
        return base64_encode($hash);
    }

    public function getCertificateInfo(): array
    {
        $cleaned_certificate_string = $this->cleanUpCertificateString($this->certificate);
        $wrapped_certificate_string = "-----BEGIN CERTIFICATE-----\n{$cleaned_certificate_string}\n-----END CERTIFICATE-----";

        $hash = $this->getCertificateHash($cleaned_certificate_string);
        //echo $hash;
        $x509 = openssl_x509_parse($wrapped_certificate_string);
        $issuer = $x509['issuer'];
        $cn = "CN=".$issuer['CN'];
        $issuerArray = [$cn];
        if(isset($issuer['DC']) ){
            foreach(array_reverse($issuer['DC']) as $dcItem){
                $issuerArray[] = 'DC='. $dcItem;
            }
        }
        $issuerString = implode(', ', $issuerArray);
        // Signature, and public key extraction from x509 PEM certificate (asn1 rfc5280)
        // Crypto module does not have those functionalities so i'm the crypto boy now :(
        // https://github.com/nodejs/node/blob/main/src/crypto/crypto_x509.cc
        // https://linuxctl.com/2017/02/x509-certificate-manual-signature-verification/
        // https://github.com/junkurihara/js-x509-utils/blob/develop/src/x509.js
        // decode binary x509-formatted object

        $res = openssl_get_publickey($wrapped_certificate_string);
        $cert = openssl_pkey_get_details($res);

        $public_key = str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----'], '', $cert['key']);
        $serialNumber = $this->hexStringToDecimalString($x509['serialNumberHex']);
        // echo $x509['serialNumberHex'] ."\n";
        // echo $serialNumber;
        return [
            $hash,
            $issuerString,
            $serialNumber,
            base64_decode($public_key),
            $this->getCertificateSignature($wrapped_certificate_string),
            $cleaned_certificate_string
        ];
    }

    function hexStringToDecimalString($hexString) {
        $decimalValue = '0';
        for ($i = 0, $len = strlen($hexString); $i < $len; $i++) {
            $decimalValue = bcmul($decimalValue, '16', 0);
            $decimalValue = bcadd($decimalValue, hexdec($hexString[$i]), 0);
        }

        return $decimalValue;
    }

    public function signedPropertiesIndentationFix($document)
    {
        $xpath = $this->createXpath($document);
        $signedPropertyPath = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties";
        $selectedNodes = $xpath->query($signedPropertyPath);
        $digistNode = $selectedNodes->item(0);
        $linearizedXml= $this->linearizeXmlNode($digistNode);

        $hash = openssl_digest($linearizedXml, 'sha256');
        $encodedHash = base64_encode($hash);
        return $encodedHash;
    }

    private function linearizeXmlNode($node) {
        $linearized = '';
        foreach ($node->childNodes as $child) {
            $linearized .= $child->ownerDocument->saveXML($child);
        }
        $linearized = preg_replace('/\s+/', '', $linearized);
        return $linearized;
    }

    public function populateUBLExtensions($document,string $invoice_hash, string $cleanUpCertificateString, string $signed_properties_hash, string $digital_signature) {
        $xpath = $this->createXpath($document);
        $step2Path = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:SignatureValue";
        $selectedNodes = $xpath->query($step2Path);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $invoice_hash;
        }

        $certificatePath = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509Certificate";
        $selectedNodes = $xpath->query($certificatePath);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $cleanUpCertificateString;
        }

        $step5Path = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:SignedInfo/ds:Reference[@URI='#xadesSignedProperties']/ds:DigestValue";
        $selectedNodes = $xpath->query($step5Path);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $signed_properties_hash;
        }

        $step1Path = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:SignedInfo/ds:Reference[@Id='invoiceSignedData']/ds:DigestValue";
        $selectedNodes = $xpath->query($step1Path);
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $digital_signature;
        }

        return $document;
    }

    public function createAuthorizationToken($binaryToken, $secret) {
        $token = "{$binaryToken}:{$secret}";
        return base64_encode($token);
    }
    public function getCertificateSignature(string $cer): string
    {
        $res = openssl_x509_read($cer);
        openssl_x509_export($res, $out, FALSE);
        $signatureHex = '';
        $out = explode('Signature Algorithm:', $out);
        $out = explode('-----BEGIN CERTIFICATE-----', $out[2]);
        $out = explode("\n", $out[0]);
        foreach ($out as $line) {
            $trimmedLine = trim($line);
            $noColons = str_replace(':', '', $trimmedLine);
            if (!empty($noColons) && ctype_xdigit($noColons)) {
                $signatureHex .= $noColons;
            }
        }
        return pack('H*', $signatureHex);
    }

    public function extractSignature($certPemString)
    {

        $bin = ($certPemString);

        if (empty($certPemString) || empty($bin)) {
            return false;
        }

        $bin = substr($bin, 4);

        while (strlen($bin) > 1) {
            $seq = ord($bin[0]);
            if ($seq == 0x03 || $seq == 0x30) {
                $len = ord($bin[1]);
                $bytes = 0;

                if ($len & 0x80) {
                    $bytes = ($len & 0x0f);
                    $len = 0;
                    for ($i = 0; $i < $bytes; $i++) {
                        $len = ($len << 8) | ord($bin[$i + 2]);
                    }
                }

                if ($seq == 0x03) {
                    return substr($bin, 3 + $bytes, $len);
                } else {
                    $bin = substr($bin, 2 + $bytes + $len);
                }
            } else {
                return false;
            }
        }
        return false;
    }

    public function generateQR(DOMDocument $invoice_xml, string $digital_signature, $public_key, string $invoice_hash, $signature=null)
    {
        // Extract required tags
        //1
        $seller_name = $invoice_xml->getElementsByTagName('AccountingSupplierParty')[0]
            ->getElementsByTagName('RegistrationName')[0]->textContent;

        //2
        $VAT_number = $invoice_xml->getElementsByTagName('CompanyID')[0]->textContent;

        //3
        $issue_date = $invoice_xml->getElementsByTagName('IssueDate')[0]->textContent;
        $issue_time = $invoice_xml->getElementsByTagName('IssueTime')[0]->textContent;
        $formatted_datetime = date('Y-m-d\TH:i:s\Z', strtotime("{$issue_date} {$issue_time}"));
        //4
        $invoice_total = $invoice_xml->getElementsByTagName('TaxInclusiveAmount')[0]->textContent;
        //5
        $VAT_total = 0;
        if ($tax_amount = $invoice_xml->getElementsByTagName('TaxTotal')[0]) {
            $VAT_total = $tax_amount->getElementsByTagName('TaxAmount')[0]->textContent;
        }

        // Detect if simplified invoice or not (not used currently assuming all simplified tax invoice)
        //$invoice_type = $invoice_xml->getElementsByTagName('Invoice/cbc:InvoiceTypeCode')[0]['@_name'];

        $qr_tlv = $this->TLV([
            $seller_name,
            $VAT_number,
            $formatted_datetime,
            $invoice_total,
            $VAT_total,
            $invoice_hash,
            $digital_signature,
            $public_key,
           // $signature
        ]);

        return base64_encode($qr_tlv);
    }

    private function TLV(array $tags): string
    {
        $__toHex = function ($value) {
            return pack('H*', sprintf('%02X', $value));
        };

        $__toString = function ($__tag, $__value, $__length) use ($__toHex) {
            $value = (string)$__value;
            return $__toHex($__tag) . $__toHex($__length) . $value;
        };

        foreach ($tags as $i => $tag)
            $__TLVS[] = $__toString($i + 1, $tag, strlen($tag));


        return implode('', $__TLVS) ?? '';
    }

    public function pobulateQR($document, $qr) {
        $xpath = $this->createXpath($document);
        $path = "//cac:AdditionalDocumentReference//cbc:ID[text()='QR']//following-sibling::cac:Attachment/cbc:EmbeddedDocumentBinaryObject";
        $selectedNodes = $xpath->query($path);
        $digistNode = $selectedNodes->item(0);

        if(!is_null($digistNode)){
           $digistNode->nodeValue = $qr;
        }
        return $document;
    }

    public function defaultUBLExtensionsSignedPropertiesForSigning(array $signed_properties_props): string
    {
        $populated_template = require ROOT_PATH . '/src/templates/ubl_signature_signed_properties_for_signing_template.php';

        $populated_template = str_replace('SET_SIGN_TIMESTAMP', $signed_properties_props['sign_timestamp'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_HASH', $signed_properties_props['certificate_hash'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_ISSUER', $signed_properties_props['certificate_issuer'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_SERIAL_NUMBER', $signed_properties_props['certificate_serial_number'], $populated_template);

        return $populated_template;
    }

    public function defaultUBLExtensionsSignedProperties(array $signed_properties_props): string
    {
        $populated_template = require ROOT_PATH . '/src/templates/ubl_signature_signed_properties_template.php';

        $populated_template = str_replace('SET_SIGN_TIMESTAMP', $signed_properties_props['sign_timestamp'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_HASH', $signed_properties_props['certificate_hash'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_ISSUER', $signed_properties_props['certificate_issuer'], $populated_template);
        $populated_template = str_replace('SET_CERTIFICATE_SERIAL_NUMBER', $signed_properties_props['certificate_serial_number'], $populated_template);

        return $populated_template;
    }

    private function parseLineItems(array $line_items) {
        // BT-110
        $total_taxes = 0;
        $total_subtotal = 0;

        $invoice_line_items = [];

        array_map(function ($line_item) use (&$total_taxes, &$total_subtotal, &$invoice_line_items) {

            list($line_item_xml, $line_item_totals) = $this->constructLineItem($line_item);

            $total_taxes += $line_item_totals['taxes_total'];
            $total_subtotal += (float)$line_item_totals['subtotal'];

            $invoice_line_items[] = $line_item_xml;
        }, $line_items);

//        if(props.cancelation) {
//            // Invoice canceled. Tunred into credit/debit note. Must have PaymentMeans
//            // BR-KSA-17
//            $this->invoice_xml.set('Invoice/cac:PaymentMeans', false, {
//                'cbc:PaymentMeansCode': props.cancelation.payment_method,
//                'cbc:InstructionNote': props.cancelation.reason ?? 'No note Specified'
//            });
//        }

        /*
         * <cac:TaxTotal>
         *      </cac:TaxSubtotal> ...
         * set invoice lines
         */
        $tax_total_template = require ROOT_PATH . '/src/templates/tax_total_template.php';

        $item_lines = $this->constructTaxTotal($line_items);

        $lines = '';
        foreach ($item_lines[0]['cac:TaxSubtotal'] as $line) {

            $l = $tax_total_template['tax_sub_total'];
            $l = str_replace('46.00', $line['cbc:TaxableAmount']['#text'], $l);
            $l = str_replace('_6.89', $line['cbc:TaxAmount']['#text'], $l);
            $l = str_replace('__S', $line['cac:TaxCategory']['cbc:ID']['#text'], $l);
            $l = str_replace('15.00', $line['cac:TaxCategory']['cbc:Percent'], $l);

            $lines .= $l;
        }

        $tax_total_template['tax_total'] = str_replace('__158.67', $item_lines[0]['cbc:TaxAmount']['#text'], $tax_total_template['tax_total']);
        $tax_total_template['tax_total'] = str_replace('___tax_amount', $item_lines[1]['cbc:TaxAmount']['#text'], $tax_total_template['tax_total']);
        $tax_total_template = str_replace('__TaxSubtotal', $lines, $tax_total_template['tax_total']);

        /*
         * <cac:LegalMonetaryTotal>
         * $legal_monetary_total_template tags set
         */
        $legal_monetary_total_template = require ROOT_PATH . '/src/templates/legal_monetary_total_template.php';

        $constructLegalMonetaryTotal = $this->constructLegalMonetaryTotal($total_subtotal, $total_taxes);

        $legal_monetary_total_template = str_replace('_LineExtensionAmount', $constructLegalMonetaryTotal['cbc:LineExtensionAmount']['#text'], $legal_monetary_total_template);
        $legal_monetary_total_template = str_replace('_TaxExclusiveAmount', $constructLegalMonetaryTotal['cbc:TaxExclusiveAmount']['#text'], $legal_monetary_total_template);
        $legal_monetary_total_template = str_replace('_TaxInclusiveAmount', $constructLegalMonetaryTotal['cbc:TaxInclusiveAmount']['#text'], $legal_monetary_total_template);
        $legal_monetary_total_template = str_replace('_AllowanceTotalAmount', $constructLegalMonetaryTotal['cbc:AllowanceTotalAmount']['#text'], $legal_monetary_total_template);
        $legal_monetary_total_template = str_replace('_PrepaidAmount', $constructLegalMonetaryTotal['cbc:PrepaidAmount']['#text'], $legal_monetary_total_template);
        $legal_monetary_total_template = str_replace('_PayableAmount', $constructLegalMonetaryTotal['cbc:PayableAmount']['#text'], $legal_monetary_total_template);

        /*
         * <cac:InvoiceLine> ...
         * set invoice lines
         */
        $invoice_line_template = require_once ROOT_PATH . '/src/templates/invoice_line_template.php';

        $invoice_line = '';
        foreach ($invoice_line_items as $item) {

            $invoice_line_template_copy = $invoice_line_template['invoice_line'];

            $invoice_line_template_copy = str_replace('__ID', $item['cbc:ID'], $invoice_line_template_copy);
            $invoice_line_template_copy = str_replace('__InvoicedQuantity', $item['cbc:InvoicedQuantity']['#text'], $invoice_line_template_copy);
            $invoice_line_template_copy = str_replace('__LineExtensionAmount', $item['cbc:LineExtensionAmount']['#text'], $invoice_line_template_copy);
            $invoice_line_template_copy = str_replace('__TaxAmount', $item['cac:TaxTotal']['cbc:TaxAmount']['#text'], $invoice_line_template_copy);
            $invoice_line_template_copy = str_replace('__RoundingAmount', $item['cac:TaxTotal']['cbc:RoundingAmount']['#text'], $invoice_line_template_copy);

            $invoice_line_template_copy = str_replace('__Name', $item['cac:Item']['cbc:Name'], $invoice_line_template_copy);

            /*
             *
             */
            $iit = '';
            foreach ($item['cac:Item']['cac:ClassifiedTaxCategory'] as $ClassifiedTaxCategory) {
                $invoice_item_template = $invoice_line_template['invoice_item'];
                $invoice_item_template = str_replace('___S', $ClassifiedTaxCategory['cbc:ID'], $invoice_item_template);
                $invoice_item_template = str_replace('___Percent', $ClassifiedTaxCategory['cbc:Percent'], $invoice_item_template);

                $iit .= $invoice_item_template;
            }
            $invoice_line_template_copy = str_replace('ClassifiedTaxCategory', $iit, $invoice_line_template_copy);

            /*
             *
             */
            $ipt = '';
            foreach ($item['cac:Price']['cac:AllowanceCharge'] as $AllowanceCharge) {
                $invoice_price_template = $invoice_line_template['invoice_price'];
                $invoice_price_template = str_replace('___AllowanceChargeReason', $AllowanceCharge['cbc:AllowanceChargeReason'], $invoice_price_template);
                $invoice_price_template = str_replace('___Amount', $AllowanceCharge['cbc:Amount']['#text'], $invoice_price_template);

                $ipt .= $invoice_price_template;
            }
            $invoice_line_template_copy = str_replace('AllowanceCharge', $ipt, $invoice_line_template_copy);

            $invoice_line .= $invoice_line_template_copy;
        }

        return $tax_total_template . $legal_monetary_total_template . $invoice_line;
    }

    private function constructLineItem($line_item): array
    {
        [
            $cacAllowanceCharges,
            $cacClassifiedTaxCategories, $cacTaxTotal,
            $line_item_total_tax_exclusive,
            $line_item_total_taxes,
            $line_item_total_discounts
        ] = $this->constructLineItemTotals($line_item);

        return [
            /*'line_item_xml' => */ [
                'cbc:ID' => $line_item['id'],
                'cbc:InvoicedQuantity' => [
                    '@_unitCode' => 'PCE',
                    '#text' => $line_item['quantity']
                ],
                // BR-DEC-23
                'cbc:LineExtensionAmount' => [
                    '@_currencyID' => 'SAR',
                    '#text' => number_format($line_item_total_tax_exclusive, 2, '.', '')
                ],
                'cac:TaxTotal' => $cacTaxTotal,
                'cac:Item' => [
                    'cbc:Name' => $line_item['name'],
                    'cac:ClassifiedTaxCategory' => $cacClassifiedTaxCategories
                ],
                'cac:Price' => [
                    'cbc:PriceAmount' => [
                        '@_currencyID' => 'SAR',
                        '#text' => $line_item['tax_exclusive_price']
                    ],
                    'cac:AllowanceCharge' => $cacAllowanceCharges
                ]
            ],
            /*'line_item_totals' => */ [
                'taxes_total' => $line_item_total_taxes,
                'discounts_total' => $line_item_total_discounts,
                'subtotal' => $line_item_total_tax_exclusive
            ]
        ];
    }

    private function constructLineItemTotals($line_item): array
    {
        $line_item_total_discounts = 0;
        $line_item_total_taxes = 0;

        $cacAllowanceCharges = [];

        // VAT
        // BR-KSA-DEC-02
        $VAT = [
            'cbc:ID' => $line_item['VAT_percent'] ? 'S' : 'O',
            // BT-120, KSA-121
            'cbc:Percent' => number_format($line_item['VAT_percent'] ? ($line_item['VAT_percent'] * 100) : 0, 2, '.', ''),
            'cac:TaxScheme' => [
                'cbc:ID' => 'VAT'
            ],
        ];
        $cacClassifiedTaxCategories[] = $VAT;

        // Calc total discounts
        array_map(function ($discount) use (&$line_item_total_discounts, &$cacAllowanceCharges) {
            $line_item_total_discounts += $discount['amount'];
            $cacAllowanceCharges[] = [
                'cbc:ChargeIndicator' => 'false',
                'cbc:AllowanceChargeReason' => $discount['reason'],
                'cbc:Amount' => [
                    '@_currencyID' => 'SAR',
                    // BR-DEC-01
                    '#text' => number_format($discount['amount'], 2, '.', '')
                ]
            ];
        }, $line_item['discounts'] ?? []);


        // Calc item subtotal
        $line_item_subtotal = ($line_item['tax_exclusive_price'] * $line_item['quantity']) - $line_item_total_discounts;

        // Calc total taxes
        // BR-KSA-DEC-02
        $line_item_total_taxes = $line_item_total_taxes + ($line_item_subtotal * $line_item['VAT_percent']);

        array_map(function ($tax) use (&$line_item_total_taxes, $line_item_subtotal, &$cacClassifiedTaxCategories) {
            $line_item_total_taxes = $line_item_total_taxes + (floatval($tax['percent_amount']) * $line_item_subtotal);

            $cacClassifiedTaxCategories[] = [
                'cbc:ID' => 'S',
                'cbc:Percent' => number_format($tax['percent_amount'] * 100, 2, '.', ''),
                'cac:TaxScheme' => [
                    'cbc:ID' => 'VAT'
                ]
            ];

        }, $line_item['other_taxes'] ?? [])[0] ?? [0, 0];

        // BR-KSA-DEC-03, BR-KSA-51
        $cacTaxTotal = [
            'cbc:TaxAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($line_item_total_taxes, 2, '.', '')
            ],
            'cbc:RoundingAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($line_item_subtotal + $line_item_total_taxes, 2, '.', '')
            ]
        ];


        return [
            $cacAllowanceCharges,
            $cacClassifiedTaxCategories, $cacTaxTotal,
            $line_item_subtotal,
            $line_item_total_taxes,
            $line_item_total_discounts
        ];
    }

    private function constructLegalMonetaryTotal(float $total_subtotal, float $total_taxes)
    {
        return [
            // BR-DEC-09
            'cbc:LineExtensionAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($total_subtotal, 2, '.', '')
            ],
            // BR-DEC-12
            'cbc:TaxExclusiveAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($total_subtotal, 2, '.', '')
            ],
            // BR-DEC-14, BT-112
            'cbc:TaxInclusiveAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($total_subtotal + $total_taxes, 2, '.', '')
            ],
            'cbc:AllowanceTotalAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => 0
            ],
            'cbc:PrepaidAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => 0
            ],
            // BR-DEC-18, BT-112
            'cbc:PayableAmount' => [
                '@_currencyID' => 'SAR',
                '#text' => number_format($total_subtotal + $total_taxes, 2, '.', '')
            ]
        ];
    }

    private function constructTaxTotal(array $line_items) {
        $cacTaxSubtotal = [];
        // BR-DEC-13, MESSAGE : [BR-DEC-13]-The allowed maximum number of decimals for the Invoice total VAT amount (BT-110) is 2.
        $addTaxSubtotal = function ($taxable_amount, $tax_amount, $tax_percent) use (&$cacTaxSubtotal) {
            $cacTaxSubtotal[] = [
                // BR-DEC-19
                'cbc:TaxableAmount' => [
                    '@_currencyID' => 'SAR',
                    '#text' => number_format((float)($taxable_amount), 2, '.', '')
                ],
                'cbc:TaxAmount' => [
                    '@_currencyID' => 'SAR',
                    '#text' => number_format((float)($tax_amount), 2, '.', '')
                ],
                'cac:TaxCategory' => [
                    'cbc:ID' => [
                        '@_schemeAgencyID' => 6,
                        '@_schemeID' => 'UN/ECE 5305',
                        '#text' => $tax_percent ? 'S' : 'O'
                    ],
                    'cbc:Percent' => number_format((float)$tax_percent * 100.00, 2, '.', ''),
                    // BR-O-10
                    'cbc:TaxExemptionReason' => $tax_percent ? '' : 'Not subject to VAT',
                    'cac:TaxScheme' => [
                        'cbc:ID' => [
                            '@_schemeAgencyID' => 6,
                            '@_schemeID' => 'UN/ECE 5153',
                            '#text' => 'VAT'
                        ]
                    ],
                ]
            ];
        };

        $taxes_total = 0;
        array_map(function ($line_item) use (&$addTaxSubtotal, &$taxes_total) {
            $total_line_item_discount = array_reduce($line_item['discounts'], function ($p, $c) {
                return $p + $c['amount'];
            }, 0);
            $taxable_amount = ($line_item['tax_exclusive_price'] * $line_item['quantity']) - ($total_line_item_discount ?? 0);

            $tax_amount = ((float)$line_item['VAT_percent']) * ((float)$taxable_amount);
            $addTaxSubtotal($taxable_amount, $tax_amount, $line_item['VAT_percent']);
            $taxes_total += $tax_amount;
            array_map(function ($tax) use (&$taxable_amount, &$addTaxSubtotal, &$taxes_total) {
                $tax_amount = $tax['percent_amount'] * $taxable_amount;
                $addTaxSubtotal($taxable_amount, $tax_amount, $tax['percent_amount']);
                $taxes_total += $tax_amount;
            }, $line_item['other_taxes']);
        }, $line_items);

        // BT-110
        $taxes_total = number_format($taxes_total, 2, '.', '');

        // BR-DEC-13, MESSAGE : [BR-DEC-13]-The allowed maximum number of decimals for the Invoice total VAT amount (BT-110) is 2.
        return [
            [
                // Total tax amount for the full invoice
                'cbc:TaxAmount' => [
                    '@_currencyID' => 'SAR',
                    '#text' => $taxes_total
                ],
                'cac:TaxSubtotal' => $cacTaxSubtotal,
            ],
            [
                // KSA Rule for VAT tax
                'cbc:TaxAmount' => [
                    '@_currencyID' => 'SAR',
                    '#text' => $taxes_total
                ]
            ]
        ];
    }

    //Step 1
    public function generateHash($canonical){
        $hash = hash('sha256', trim($canonical));
        $hash =  hex2bin($hash);
        $hash = base64_encode($hash);//Step 1
        $this->hash= $hash;
    }

    public function processInvoice($preHash, $path=null) {
        $this->getTaxInvoiceDocument();
        $this->preHash = $preHash;
        $this->generateUUID();
        $this->setPreDocument();
        $canonical = $this->getPureInvoiceString($this->document,false,false);
        $this->generateHash($canonical);
        $signedInvoice = $this->createInvoiceDigitalSignature($this->privateKey);//step 2
        $this->certitifcateInfo = $this->getCertificateInfo();
        $certificateHash = $this->certitifcateInfo[0];//step 3
        //$certificateHash = $invoice->getCertificateHash($certificate); // step 3
        $publicKey = $this->certitifcateInfo[3];
        $issuerName = $this->certitifcateInfo[1];
        $certificateSerial = $this->certitifcateInfo[2];
        // echo $certitifcateInfo[0] ." \n";
        // echo $certificateHash ." \n";
        $step4Document = $this->fillSignedProperties($certificateHash,$issuerName,$certificateSerial);
        $hashedSignedProperties = $this->signedPropertiesIndentationFix($step4Document);//step 5
        $this->step6Document = $this->populateUBLExtensions($step4Document,$signedInvoice,$this->certitifcateInfo[5],$hashedSignedProperties,$this->hash);
        $this->qr = $this->generateQR($this->step6Document,$signedInvoice,$publicKey,$this->hash);
        $this->pobulateQR($this->step6Document,$this->qr);
        $this->encodedInvoice = base64_encode($this->step6Document->saveXML());
    }

    public function getFinalXML() {
       return $this->step6Document->saveXML();
    }

    public static function submitSinvoice($userKey, $userSecret, $url, $hash, $uuid, $encodedInvoice) {
        // dd($url);
        $response = Http::withBasicAuth($userKey,$userSecret)
        ->withHeaders([
            'Accept-Version'=>'V2',
            'Content-Type'=>'application/json',
            'Accept'=>'application/json',
            'Accept-Language'=>'ar'
        ])->post($url,[
            'invoiceHash'=>trim($hash),
            'uuid'=>trim($uuid),
            'invoice'=>$encodedInvoice
        ]);
        return $response;
    }
}
