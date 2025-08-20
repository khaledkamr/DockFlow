<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Contract;
use App\Models\invoice;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    

    public function invoices() {
        $invoices = invoice::orderBy('id', 'desc')->get();
        return view('admin.invoices', compact('invoices'));
    }

    public function createInvoice(InvoiceRequest $request) {
        $validated = $request->validated();
        $contract = Contract::findOrFail($validated['contract_id']);
        $contract->status = 'منتهي';
        $contract->actual_end_date = $validated['date'];
        $contract->save();
        invoice::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء الفاتوره بنجاح');
    }

    public function exitPermission(Request $request) {
        $contract = Contract::findOrFail($request->contract_id);
        foreach($contract->containers as $container) {
            $container->status = 'غير متوفر';
            $container->save();
        }
        return redirect()->back()->with('success', 'تم إنشاء إذن خروج للحاويات');
    }
}
