<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Customer;
use App\Models\ExpenseInvoice;
use App\Models\Invoice;
use App\Models\InvoiceStatement;
use App\Models\JournalEntry;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\TransportOrder;
use App\Models\ShippingPolicy;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    private $codeTypes = [
        'IN' => ['name' => 'فاتورة', 'icon' => 'fa-file-invoice-dollar', 'model' => Invoice::class, 'route' => ['تخزين' => 'invoices.details', 'خدمات' => 'invoices.services.details', 'تخليص' => 'invoices.clearance.details', 'شحن' => 'invoices.shipping.details']],
        'EI' => ['name' => 'فاتورة مصاريف', 'icon' => 'fa-file-invoice', 'model' => ExpenseInvoice::class, 'route' => 'expense.invoices.details'],
        'IS' => ['name' => 'مطالبة فواتير', 'icon' => 'fa-file-invoice', 'model' => InvoiceStatement::class, 'route' => 'invoices.statements.details'],
        'JD' => ['name' => 'قيد يومي', 'icon' => 'fa-money-bill-transfer', 'model' => JournalEntry::class, 'route' => 'journal.details'],
        'VR' => ['name' => 'سند صرف', 'icon' => 'fa-money-bill', 'model' => Voucher::class, 'route' => 'voucher.details'],
        'VP' => ['name' => 'سند قبض', 'icon' => 'fa-hand-holding-dollar', 'model' => Voucher::class, 'route' => 'voucher.details'],
        'CT' => ['name' => 'معاملة تخليص', 'icon' => 'fa-ship', 'model' => Transaction::class, 'route' => 'transactions.details'],
        'TO' => ['name' => 'اشعار نقل', 'icon' => 'fa-truck-fast', 'model' => TransportOrder::class, 'route' => 'transactions.transportOrders.details'],
        'SP' => ['name' => 'بوليصة شحن', 'icon' => 'fa-truck-fast', 'model' => ShippingPolicy::class, 'route' => 'shipping.policies.details'],
        'ST' => ['name' => 'بوليصة تخزين', 'icon' => 'fa-warehouse', 'model' => Policy::class, 'route' => 'policies.storage.details'],
        'RE' => ['name' => 'بوليصة تسليم', 'icon' => 'fa-handshake', 'model' => Policy::class, 'route' => 'policies.receive.details'],
        'SV' => ['name' => 'بوليصة خدمات', 'icon' => 'fa-tools', 'model' => Policy::class, 'route' => 'policies.services.details'],
    ];

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'results' => [],
                'message' => 'الرجاء إدخال حرفين على الأقل للبحث'
            ]);
        }

        // Extract the code type from the search query (e.g., "2025IN00003" => "IN")
        $codeType = $this->extractCodeType($query);

        $companyId = Auth::user()->company_id;

        if ($codeType && isset($this->codeTypes[$codeType])) {
            // Search in specific model based on code type
            $typeConfig = $this->codeTypes[$codeType];
            $results = $this->searchInModel($typeConfig, $query, $companyId);
        } else {
            // Search across all models
            foreach ($this->codeTypes as $type => $config) {
                $modelResults = $this->searchInModel($config, $query, $companyId);
                $results = array_merge($results, $modelResults);
            }
            
            // Also search for customers by name
            $customerResults = $this->searchCustomers($query, $companyId);
            $results = array_merge($results, $customerResults);
            
            // Also search for containers by code
            $containerResults = $this->searchContainers($query, $companyId);
            $results = array_merge($results, $containerResults);
        }

        // Sort by relevance (exact matches first)
        usort($results, function($a, $b) use ($query) {
            $aExact = stripos($a['code'], $query) === 0;
            $bExact = stripos($b['code'], $query) === 0;
            if ($aExact && !$bExact) return -1;
            if (!$aExact && $bExact) return 1;
            return 0;
        });

        // Limit results
        // $results = array_slice($results, 0, 10);

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    private function extractCodeType($query)
    {
        // Pattern: YYYY{CODE}XXXXX or just {CODE} anywhere
        // Extract 2-letter code type from the query
        foreach (array_keys($this->codeTypes) as $code) {
            if (stripos($query, $code) !== false) {
                return $code;
            }
        }
        return null;
    }

    private function searchInModel($config, $query, $companyId)
    {
        $results = [];
        $model = $config['model'];
        
        try {
            $items = $model::where('company_id', $companyId)
                ->where('code', 'like', "%{$query}%")
                ->get();

            foreach ($items as $item) {
                if($config['name'] == 'فاتورة') {
                    $results[] = [
                        'code' => $item->code,
                        'type' => $config['name'] . ' ' . $item->type,
                        'icon' => $config['icon'],
                        'url' => route($config['route'][$item->type], $item->uuid),
                        'date' => $item->created_at ? $item->created_at->format('Y/m/d') : null,
                    ];
                } else {
                    $results[] = [
                        'code' => $item->code,
                        'type' => $config['name'],
                        'icon' => $config['icon'],
                        'url' => route($config['route'], $item->uuid),
                        'date' => $item->created_at ? $item->created_at->format('Y/m/d') : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the search
            Log::error('Search error: ' . $e->getMessage());
        }

        return $results;
    }

    private function searchCustomers($query, $companyId)
    {
        $results = [];
        
        try {
            $customers = Customer::where('company_id', $companyId)
                ->where('name', 'like', "%{$query}%")
                ->get();

            foreach ($customers as $customer) {
                $results[] = [
                    'code' => $customer->name,
                    'type' => 'عميل',
                    'icon' => 'fa-user',
                    'url' => route('users.customer.profile', $customer->uuid),
                    'date' => $customer->created_at ? $customer->created_at->format('Y/m/d') : null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage());
        }

        return $results;
    }

    private function searchContainers($query, $companyId)
    {
        $results = [];
        
        try {
            $containers = Container::where('company_id', $companyId)
                ->where('code', 'like', "%{$query}%")
                ->get();

            foreach ($containers as $container) {
                $results[] = [
                    'code' => $container->code,
                    'type' => 'حاوية',
                    'icon' => 'fa-box',
                    'url' => route('container.details', $container->uuid),
                    'date' => $container->created_at ? $container->created_at->format('Y/m/d') : null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Container search error: ' . $e->getMessage());
        }

        return $results;
    }
}
