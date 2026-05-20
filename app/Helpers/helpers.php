<?php

const DETAILS_REPORT_PER_PAGE = 100; // pagination for accounts reports  url /account-tree/showJournalDetailsFoAccountReports

use App\Jobs\UploadToGoogleDriveJob;
use App\Models\Attachment;
use App\Models\CompanyMenuSerial;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

if (!function_exists('toHijri')) {
    function toHijri($date, $format = 'Y/m/d')
    {
        if (!$date) return '';

        $gregorian = Carbon::parse($date);
        $y = $gregorian->year;
        $m = $gregorian->month;
        $d = $gregorian->day;

        $jd = gregoriantojd($m, $d, $y);

        $l = $jd - 1948440 + 10632;
        $n = (int)(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (int)((10985 - $l) / 5316) * (int)((50 * $l) / 17719)
            + (int)($l / 5670) * (int)((43 * $l) / 15238);
        $l = $l - (int)((30 - $j) / 15) * (int)((17719 * $j) / 50)
            - (int)($j / 16) * (int)((15238 * $j) / 43) + 29;
        $hMonth = (int)(24 * $l / 709);
        $hDay = $l - (int)(709 * $hMonth / 24);
        $hYear = 30 * $n + $j - 30;

        $format = str_replace('Y', str_pad($hYear, 4, '0', STR_PAD_LEFT), $format);
        $format = str_replace('m', str_pad($hMonth, 2, '0', STR_PAD_LEFT), $format);
        $format = str_replace('d', str_pad($hDay, 2, '0', STR_PAD_LEFT), $format);

        return $format;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($upload, $path, $resize_width = null, $resize_height = null)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $filename = time() . rand(11111, 99999) . '.' . $upload->getClientOriginalExtension();
        $upload->move(public_path($path), $filename);
        return $path . '/' . $filename;
    }
}

if (!function_exists('uploadFileWithDisk')) {
    function uploadFileWithDisk($upload, $disk = null, $directory = 'Files', $model_name = null, $resize_width = null, $resize_height = null): string
    {
        $ext = is_string($upload) ? explode('/', mime_content_type($upload))[1] : $upload->getClientOriginalExtension();
        $upload = is_string($upload) ? file_get_contents($upload) : $upload;
        $disk = $disk ?? config('filesystems.default_disk');
        $filename = time() . rand(11111, 99999) . '.' . $ext;
        if ($disk) {
            if ($disk == 'default_public') {
                $directory = $model_name;
            }
            $path = $directory . '/' . $filename; //$disk . '-' .
            $result = Storage::disk($disk)->put($path, $upload);
            return $result;
        } else {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $upload->move(public_path($directory), $filename);
            return $directory . '/' . $filename;
        }
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($path)
    {
        if (file_exists($path)) {
            $delete = File::delete($path);
            if ($delete) return 1;
        }
        return 0;
    }
}

if (!function_exists('getSerial')) {
    function getSerial($branch, $company, $code, $menuCode): string
    {

        $last_card_serial = CompanyMenuSerial::where('branch_id', $branch->branch_id)
            ->where('app_menu_id', $menuCode)->latest()->first();

        if (isset($last_card_serial)) {
            $last_bonds_serial_no = $last_card_serial->serial_last_no;
            $array_number = explode('-', $last_bonds_serial_no);
            $array_number[count($array_number) - 1] = $array_number[count($array_number) - 1] + 1;
            $string_number = implode('-', $array_number);
            $last_card_serial->update(['serial_last_no' => $string_number]);
        } else {
            $string_number = $code . $branch->branch_id . '-1';
            CompanyMenuSerial::create([
                'company_group_id' => $company->company_group_id,
                'company_id' => $company->company_id,
                'branch_id' => $branch->branch_id,
                'app_menu_id' => $menuCode,
                'acc_period_year' => Carbon::now()->format('y'),
                'serial_last_no' => $string_number,
                'created_user' => auth()->user()->parent_id ?? 1,
            ]);
        }
        return $string_number;
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length)
    {
        return config('app.ACCEPT_SMS') ? rand(1111, 9999) : config('app.Fake_OTP');
    }
}

function responseSuccess($data = [], $msg = null, $code = 200)
{
    return response()->json([
        "success" => true,
        "message" => $msg,
        "data" => $data,
        "code" => $code
    ], $code);
}

function responseFail($error_msg = null, $code = 400, $result = null)
{
    return response()->json([
        "message" => $error_msg,
        "errors" => $result,
        "code" => $code
    ], $code);
}

if (!function_exists('getProductImage')) {
    /**
     * Get product image HTML tag
     * @param string|null $imagePath
     * @param int $width
     * @param int $height
     * @param string $alt
     * @return string
     */
    function getProductImage($imagePath = null, $width = 50, $height = 50, $alt = 'Product Image')
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            return '<img src="' . asset($imagePath) . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" style="object-fit: contain;">';
        }
        return '-';
    }
}

if (!function_exists('normalizePlateNumber')) {
    /**
     * Normalize a plate number for DB comparison.
     * Returns array: ['arabic' => ..., 'english' => ...]
     * 'arabic'  = spaces removed, Arabic digits kept as-is
     * 'english' = spaces removed, Arabic digits converted to English
     */
    function normalizePlateNumber(string $plate): array
    {
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $withArabic = preg_replace('/\s+/', '', $plate);
        $withEnglish = preg_replace('/\s+/', '', str_replace($arabicNumbers, $englishNumbers, $plate));

        return ['arabic' => $withArabic, 'english' => $withEnglish];
    }
}

if (!function_exists('getDataFromXml')) {
    /**
     * @param $myXmlString
     * @return array
     */
    function getDataFromXml($myXmlString): array
    {
        $invoice_xml = new DOMDocument();
        $invoice_xml->loadXML($myXmlString);
        // Extract required tags
        $issue_date = $invoice_xml->getElementsByTagName('IssueDate')[0]->textContent;
        $issue_time = $invoice_xml->getElementsByTagName('IssueTime')[0]->textContent;
        $formatted_datetime = Carbon::parse("{$issue_date} {$issue_time}")->format('Y-m-d H:i:s');

        $invoice_total = $invoice_xml->getElementsByTagName('TaxInclusiveAmount')[0]->textContent;
        $VAT_total = 0;
        if ($tax_amount = $invoice_xml->getElementsByTagName('TaxTotal')[0]) {
            $VAT_total = $tax_amount->getElementsByTagName('TaxAmount')[0]->textContent;
        }
        $invoice_amount = $invoice_xml->getElementsByTagName('LineExtensionAmount')[0]->textContent;
        
        return [
            'invoice_amount' => $invoice_amount ?? 0,
            'invoice_vat_amount' => $VAT_total ?? 0,
            'invoice_total' => $invoice_total ?? 0,
            'issue_date' => $formatted_datetime ?? null,
        ];
    }
}