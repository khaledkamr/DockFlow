<?php

namespace App\Models\Zacta;

use DOMXPath;
use DOMDocument;
use Ramsey\Uuid\Uuid;

class SimpleTaxInvoice
{
    public $invoiceLines = [];
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
    public $signTime;
    public $xmlContent ='<?xml version="1.0" encoding="UTF-8"?>
    <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
        <ext:UBLExtensions>
            <ext:UBLExtension>
                <ext:ExtensionURI>urn:oasis:names:specification:ubl:dsig:enveloped:xades</ext:ExtensionURI>
                <ext:ExtensionContent>
                    <!-- Please note that the signature values are sample values only -->
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
                                        <ds:DigestValue>v49z5vmG3xRVCo7EaVc6c2Wt1u1l0J9tkUwIyiBs+Pw=</ds:DigestValue>
                                    </ds:Reference>
                                    <ds:Reference Type="http://www.w3.org/2000/09/xmldsig#SignatureProperties" URI="#xadesSignedProperties">
                                        <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                        <ds:DigestValue>ZmM2NDhjZmZhNDJhMGQ3ZTQ0YTc5OWQ3ZWNlNWYzYWE4Yjk3MDRhNGE3YTAxZWM1NDY5YTExMWEyYjA2YzFmYg==</ds:DigestValue>
                                    </ds:Reference>
                                </ds:SignedInfo>
                                <ds:SignatureValue>MEQCIBbHExlZAA4vYOfW6S6mVUfBNYEsbk4QASfGU6h4wWGtAiAgwqJCOot+D3omvwOvTwkQpoJSChfd3D+ejMDC/SRSTA==</ds:SignatureValue>
                                <ds:KeyInfo>
                                    <ds:X509Data>
                                        <ds:X509Certificate>MIIB+jCCAaCgAwIBAgIGAY02HRDCMAoGCCqGSM49BAMCMBUxEzARBgNVBAMMCmVJbnZvaWNpbmcwHhcNMjQwMTIzMTEzODA2WhcNMjkwMTIyMjEwMDAwWjBXMQswCQYDVQQGEwJTQTEQMA4GA1UECwwHTWFxbG9iYTEQMA4GA1UECgwHTWFxbG9iYTEkMCIGA1UEAwwbTWFxbG9iYSBTaW11bGF0aW9uIERldmljZS0yMFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEn/7OSAUPYLYmoVe+xAq8b0sI3FzCfmpsZhZ1UvwQOryNrvF40xbVS9fjuHkMefl0mLiTVTi3DL0lR0LVedSU46OBnDCBmTAMBgNVHRMBAf8EAjAAMIGIBgNVHREEgYAwfqR8MHoxJDAiBgNVBAQMGzEtUmV2ZWx8Mi1SZWFjaHdhcmV8My0wMDAwMjEfMB0GCgmSJomT8ixkAQEMDzMxMDgwMzA3MTkwMDAwMzENMAsGA1UEDAwEMTEwMDEPMA0GA1UEGgwGUml5YWRoMREwDwYDVQQPDAhTZXJ2aWNlczAKBggqhkjOPQQDAgNIADBFAiBOXf0/nw75actIvEAF0znU8riYESW3nkj79IwZNqiZaAIhAKg9Og7XvGWpumYpx5ndYJiSIqnMRA9HhCVuWAPyPVlG</ds:X509Certificate>
                                    </ds:X509Data>
                                </ds:KeyInfo>
                                <ds:Object>
                                    <xades:QualifyingProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Target="signature">
                                        <xades:SignedProperties Id="xadesSignedProperties">
                                            <xades:SignedSignatureProperties>
                                                <xades:SigningTime>2024-01-25T22:23:03Z</xades:SigningTime>
                                                <xades:SigningCertificate>
                                                    <xades:Cert>
                                                        <xades:CertDigest>
                                                            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                                            <ds:DigestValue>ZjYwOGMyNjhkMWIzZmEzMDYyMTgyYzIxNTU3MDYzN2IwNjg5NjAxMmE3NjFiZmU1ODY0ZmE1MWE4MzEyOTY5ZQ==</ds:DigestValue>
                                                        </xades:CertDigest>
                                                        <xades:IssuerSerial>
                                                            <ds:X509IssuerName>CN=eInvoicing</ds:X509IssuerName>
                                                            <ds:X509SerialNumber>1706009891010</ds:X509SerialNumber>
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
        <cbc:InvoiceTypeCode name="0200000">388</cbc:InvoiceTypeCode>
        <cbc:Note languageID="ar">ABC</cbc:Note>
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
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="SAR">@INVOICETAXABLEAMOUNT@</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="SAR">@INVOICEVATAMOUNT@</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeAgencyID="6">S</cbc:ID>
                        <cbc:Percent>15.00</cbc:Percent>
                    <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">VAT</cbc:ID>
                    </cac:TaxScheme>
                    </cac:TaxCategory>
            </cac:TaxSubtotal>
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
        foreach($this->invoiceLines as $count => $invoiceLine) {
            $invoiceLine->createInvoiceLineElement($count+1);
        }
    }
    public function getInvoiceLinesXMLString() {
        $lines =[];
        foreach($this->invoiceLines as $invoiceLine) {
            $lines[] = $invoiceLine->invoiceLineXMLString;
        }
        $this->linesXMLString = implode("\n",$lines);
    }

    public function replaceXMLDocumentInvoiceLine() {
        $this->xmlContent = str_replace("@INVOICELINES@", $this->linesXMLString, $this->xmlContent);
    }

    public function fillXMLTemplate($templatePath) {
        $this->xmlContent = file_get_contents($templatePath);
    }

    public function calculateInvoiceTotals() {
        foreach($this->invoiceLines as $invoiceLine) {
            $this->totalWithoutVat += $invoiceLine->totalLineWithoutVat;
            $this->vatAmount += $invoiceLine->vatAmount;
            $this->totalAfterVat += $invoiceLine->totalAfterVat;
        }
    }

    public function replaceXMLDocumentTotals() {
        $content = $this->xmlContent;
        $content = str_replace("@INVOICEVATAMOUNT@", number_format($this->vatAmount, 2, '.', ''), $content);
        $content = str_replace("@INVOICETAXABLEAMOUNT@", number_format($this->totalWithoutVat, 2, '.', ''), $content);
        $content = str_replace("@INVOICETOTALAMOUNT@", number_format($this->totalAfterVat, 2, '.', ''), $content);
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
        $this->xmlContent = str_replace("@INVOICECUSTOMER@", $this->customer->xmlString, $this->xmlContent);

    }

    public function setSeller($seller) {
        $this->seller = $seller;
        $this->seller->replaceXMLString();
        $this->xmlContent = str_replace("@INVOICESELLER@", $this->seller->xmlString, $this->xmlContent);
    }

    public function getTaxInvoiceDocument() {
        $this->replaceXMLDocumentInfo();
        $this->generateInvoiceLines();
        $this->generateInvoiceTotals();
        $this->document = new DOMDocument();
        //$xmlDocument = file_get_contents($path);
        $this->document->loadXML($this->xmlContent);
    }

    public function getTaxInvoiceDocumentFromFile($path) {
        $this->replaceXMLDocumentInfo();
        $this->generateInvoiceLines();
        $this->generateInvoiceTotals();
        $this->document = new DOMDocument();
        $xmlDocument = file_get_contents($path);
        $this->document->loadXML($xmlDocument);
    }

    public function generateUUID() {
        $uuid = Uuid::uuid4();
        $xpath = $this->createXpath($this->document);
        $selectedNodes = $xpath->query("/default:Invoice/cbc:UUID");
        $uuidNode = $selectedNodes->item(0);
        if(!is_null($uuidNode)){
            $uuidNode->nodeValue = $uuid->toString();
        }
        $this->uuid =  $uuid->toString(). "\n";
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
        // $hash = hash('sha256', trim("a11b6fe587a50f7daffe3a7fb42dcccf32b43ee9b37d9f252d04243e54c11a3f"));
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

        // $currentDateTime = new \DateTime('now', new \DateTimeZone('Asia/Riyadh'));
        $currentDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->signTime = $currentDateTime->format('Y-m-d\TH:i:s\Z');
        $digistNode = $selectedNodes->item(0);
        if(!is_null($digistNode)) {
            $digistNode->nodeValue = $this->signTime;
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
        // echo "Public \n" ;
        // echo "public".base64_decode($public_key);

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

    public function signedPropertiesIndentationFix($templatePath) {
        $template = file_get_contents($templatePath);
        $template = str_replace("@SIGNTIME@", $this->signTime, $template);
        $template = str_replace("@CERTSERIAL@", $this->certitifcateInfo[2], $template);
        $template = str_replace("@ISSUER@", $this->certitifcateInfo[1], $template);
        $template = str_replace("@CERTHASH@", $this->certitifcateInfo[0], $template);
        $hash = openssl_digest($template, 'sha256');
        return base64_encode($hash);
    }

    public function populateUBLExtensions($document, string $invoice_hash, string $cleanUpCertificateString, string $signed_properties_hash, string $digital_signature) {
        // var_dump($document);
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

    public function extractSignature($certPemString) {
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

    public function generateQR(DOMDocument $invoice_xml, string $digital_signature, $public_key, string $invoice_hash, $signature=null) {
        // Extract required tags
        //1
        $seller_name = $invoice_xml->getElementsByTagName('AccountingSupplierParty')[0]
            ->getElementsByTagName('RegistrationName')[0]->textContent;

        //2
        $VAT_number = $invoice_xml->getElementsByTagName('CompanyID')[0]->textContent;

        //3
        $issue_date = $invoice_xml->getElementsByTagName('IssueDate')[0]->textContent;
        $issue_time = $invoice_xml->getElementsByTagName('IssueTime')[0]->textContent;
        $formatted_datetime = date('Y-m-d\TH:i:s', strtotime("{$issue_date} {$issue_time}"));
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
            $signature
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

    //Step 1
    public function generateHash($canonical) {
        $hash = hash('sha256', trim($canonical));
        $hash =  hex2bin($hash);
        $hash = base64_encode($hash);//Step 1
        $this->hash= $hash;
    }

    public function processInvoice($preHash, $templatePath) {
        $this->getTaxInvoiceDocument();
        $this->preHash = $preHash;
        $this->generateUUID();
        $this->setPreDocument();
        $canonical = $this->getPureInvoiceString($this->document, false, false);
        $this->generateHash($canonical);
        $signedInvoice = $this->createInvoiceDigitalSignature($this->privateKey);//step 2
        $this->certitifcateInfo = $this->getCertificateInfo();
        $certificateHash = $this->certitifcateInfo[0];//step 3
        //$certificateHash = $invoice->getCertificateHash($certificate); // step 3
        $publicKey = $this->certitifcateInfo[3];
        $issuerName = $this->certitifcateInfo[1];
        $certificateSerial = $this->certitifcateInfo[2];
        $signture = $this->certitifcateInfo[4];
        // echo $certitifcateInfo[0] ." \n";
        // echo $certificateHash ." \n";
        $step4Document = $this->fillSignedProperties($certificateHash, $issuerName, $certificateSerial);
        $hashedSignedProperties = $this->signedPropertiesIndentationFix($templatePath);//step 5
        $this->step6Document = $this->populateUBLExtensions($step4Document, $signedInvoice, $this->certitifcateInfo[5], $hashedSignedProperties, $this->hash);

        $xpath = $this->createXpath($this->step6Document);

        $query = "/default:Invoice/ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sig:UBLDocumentSignatures/sac:SignatureInformation/ds:Signature/ds:Object/xades:QualifyingProperties/xades:SignedProperties";

        $node = $xpath->query($query)->item(0);

        dd($this->step6Document->saveXML($node));

        $this->qr = $this->generateQR($this->step6Document, $signedInvoice, $publicKey, $this->hash, $signture);
        $this->pobulateQR($this->step6Document, $this->qr);
        $this->encodedInvoice = base64_encode($this->step6Document->saveXML());
    }

    public function processFileInvoice($preHash, $path, $templatePath) {
        $this->getTaxInvoiceDocumentFromFile($path);
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
        $signture = $this->certitifcateInfo[4];
        // echo $certitifcateInfo[0] ." \n";
        // echo $certificateHash ." \n";

        $step4Document = $this->fillSignedProperties($certificateHash, $issuerName, $certificateSerial);
        $hashedSignedProperties = $this->signedPropertiesIndentationFix($templatePath, $step4Document);//step 5
        // dd($hashedSignedProperties);
        $this->step6Document = $this->populateUBLExtensions($step4Document, $signedInvoice, $this->certitifcateInfo[5], $hashedSignedProperties, $this->hash);
        $this->qr = $this->generateQR($this->step6Document, $signedInvoice, $publicKey, $this->hash,$signture);
        $this->pobulateQR($this->step6Document, $this->qr);
        $this->encodedInvoice = base64_encode($this->step6Document->saveXML());
    }

    public function getFinalXML() {
       return $this->step6Document->saveXML();
    }
}
