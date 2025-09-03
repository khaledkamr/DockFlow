<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\invoice;
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
        Invoice::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء فاتورة بنجاح');
    } 

    public function updateInvoice(Request $request, $id) {
        $invoice = Invoice::findOrFail($id);
        $invoice->payment = $request->payment;
        $invoice->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات الفاتورة');
    }
}
