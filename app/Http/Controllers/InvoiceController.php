<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicNumberConverter;
use App\Http\Requests\ClaimRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Claim;
use App\Models\Container;
use App\Models\Customer;
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
        return view('admin.invoices.invoices', compact('invoices'));
    }

    public function createInvoice(Request $request) {
        $customers = Customer::all();
        $customer_id = $request->input('customer_id');
        $containers = Container::where('status', 'تم التسليم')->where('customer_id', $customer_id)->get();
        return view('admin.invoices.createInvoice', compact('customers', 'containers'));
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
            $total = $storage_price + $late_fee;
            $amountBeforeTax += $total;
        }

        $tax = $amountBeforeTax * 0.15;
        $invoice->amount = $amountBeforeTax + $tax;
        $invoice->save();

        return redirect()->back()->with('success', 'تم إنشاء فاتورة بنجاح');
    } 

    public function invoiceDetails($code) {
        $invoice = Invoice::where('code', $code)->firstOrFail();

        $amountBeforeTax = 0;

        foreach($invoice->policy->containers as $container) {
            $container->period = (int) Carbon::parse($container->date)->diffInDays(Carbon::parse($invoice->policy->date));
            $container->storage_price = $invoice->policy->contract->services[0]->pivot->price;
            if($container->period > $invoice->policy->contract->services[0]->pivot->unit) {
                $days = (int) Carbon::parse($container->date)->addDays($invoice->policy->contract->services[0]->pivot->unit)->diffInDays(Carbon::parse($invoice->policy->date));
                $container->late_days = $days;
                $container->late_fee = $days * $invoice->policy->contract->services[3]->pivot->price;
            } else {
                $container->late_days = 'لا يوجد';
                $container->late_fee = 0;
            }
            $container->total = $container->storage_price + $container->late_fee;
            $amountBeforeTax += $container->total;  
        }

        $invoice->subtotal = $amountBeforeTax;
        $invoice->tax = $amountBeforeTax * 0.15;
        $invoice->discount = 0;
        $invoice->total = $amountBeforeTax + $invoice->tax;

        $hatching_total = ArabicNumberConverter::numberToArabicWords((int)$invoice->total) . " ريالاً لا غير";

        return view('admin.invoices.invoiceDetails', compact('invoice', 'hatching_total'));
    }

    public function updateInvoice(Request $request, $id) {
        $invoice = Invoice::findOrFail($id);
        $invoice->payment = $request->payment;
        $invoice->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }
}
