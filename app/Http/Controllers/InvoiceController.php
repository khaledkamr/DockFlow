<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function invoices(Request $request) {
        $invoices = invoice::orderBy('id', 'desc')->get();
        $methodFilter = request()->query('paymentMethod');
        if ($methodFilter && $methodFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($methodFilter) {
                return $invoice->payment_method === $methodFilter;
            });
        }
        $paymentFilter = request()->query('payment');
        if ($paymentFilter && $paymentFilter !== 'all') {
            $invoices = $invoices->filter(function ($invoice) use ($paymentFilter) {
                return $invoice->payment === $paymentFilter;
            });
        }
        $search = $request->input('search', null);
        if($search) {
            $invoices = $invoices->filter(function($invoice) use($search) {
                return stripos($invoice->id, $search) !== false 
                    || stripos($invoice->customer->name, $search) !== false
                    || stripos($invoice->date, $search) !== false;
            });
        }
        $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
            $invoices->forPage(request()->get('page', 1), 50),
            $invoices->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.policies.invoices', compact('invoices'));
    }

    public function storeInvoice(InvoiceRequest $request) {
        $validated = $request->validated();
        if($validated['payment_method'] == 'كريدت') {
            $validated['payment'] = 'لم يتم الدفع';
        } else {
            $validated['payment'] = 'تم الدفع';
        }
        $invoice = Invoice::create($validated);

        $amountBeforeTax = 0;

        foreach($invoice->policy->containers as $container) {
            $period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($invoice->policy->date));
            $storage_price = $invoice->policy->contract->services[0]->pivot->price;
            if($period > $invoice->policy->contract->services[0]->pivot->unit) {
                $days = (int) Carbon::parse($container->date)->addDays($invoice->policy->contract->services[0]->pivot->unit)->diffInDays(Carbon::parse($invoice->policy->date));
                $late_fee = $days * $invoice->policy->contract->services[3]->pivot->price;
            } else {
                $late_fee = 0;
            }
            $storageService = $invoice->policy->contract->services[1]->pivot->price;
            $total = $storage_price + $late_fee + $storageService;
            $amountBeforeTax += $total;
        }

        $tax = $amountBeforeTax * 0.15;
        $invoice->amount = $amountBeforeTax + $tax;
        $invoice->save();

        return redirect()->back()->with('success', 'تم إنشاء فاتورة بنجاح');
    } 

    public function updateInvoice(Request $request, $id) {
        $invoice = Invoice::findOrFail($id);
        $invoice->payment = $request->payment;
        $invoice->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }
}
