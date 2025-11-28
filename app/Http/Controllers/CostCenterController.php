<?php

namespace App\Http\Controllers;

use App\Http\Requests\CostCenterRequest;
use App\Models\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function costCenters() {
        $costCenters = CostCenter::where('parent_id', null)->with('children')->orderBy('id', 'desc')->get();
        return view('pages.accounting.cost_centers.cost_centers', compact('costCenters'));
    }

    public function storeCostCenter(CostCenterRequest $request) {
        $validated = $request->validated();

        $new = CostCenter::create($validated);
        logActivity("إنشاء مركز تكلفة", "تم إنشاء مركز تكلفة جديد: " . $new->name, null, $new->toArray());

        return redirect()->back()->with('success', 'تم إنشاء مركز التكلفة بنجاح');
    }

    public function updateCostCenter(Request $request, CostCenter $costCenter) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
        ]);
        $old = $costCenter->toArray();

        $costCenter->update($validated);
        logActivity("تعديل مركز تكلفة", "تم تعديل مركز تكلفة: " . $costCenter->name, $old, $costCenter->toArray());

        return redirect()->back()->with('success', 'تم تعديل مركز التكلفة بنجاح');
    }

    public function deleteCostCenter(CostCenter $costCenter) {
        if($costCenter->children()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف مركز تكلفة يحتوي على مراكز تكلفة فرعية');
        }
        if($costCenter->expenseInvoiceItems()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف مركز تكلفة مرتبط بعناصر فواتير مصاريف');
        }

        $name = $costCenter->name;
        $old = $costCenter->toArray();
        $costCenter->delete();
        logActivity("حذف مركز تكلفة", "تم حذف مركز تكلفة: " . $name, $old);

        return redirect()->back()->with('success', 'تم حذف مركز التكلفة بنجاح');
    }
}
