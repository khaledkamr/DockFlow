<?php

namespace App\Http\Controllers;

use App\Models\BulkInventory;
use App\Models\BulkItem;
use App\Models\Customer;
use Illuminate\Http\Request;

class BulkInventoryController extends Controller
{
    public function items() {
        $items = BulkItem::all();
        return view('pages.bulk_inventory.bulk_items', compact('items'));
    }

    public function addItem(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        $item = BulkItem::create($validated);
        logActivity('إضافة بضاعة جديدة', "تمت إضافة البضاعة الجديدة: {$item->name} بوحدة: {$item->unit}", null, $item->toArray());

        return redirect()->back()->with('success', 'تم إضافة البضاعة الجديدة بنجاح.');
    }

    public function updateItem(Request $request, BulkItem $item) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        $old = $item->toArray();
        $item->update($validated);
        $new = $item->toArray();

        logActivity('تحديث بيانات بضاعة', "تم تحديث بيانات البضاعة: {$item->name} بوحدة: {$item->unit}", $old, $new);
        return redirect()->back()->with('success', 'تم تحديث بيانات البضاعة بنجاح.');
    }

    public function deleteItem(BulkItem $item) {
        if($item->inventories()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف هذه البضاعة لأنها مرتبطة بمخزون موجود.');
        }
        $old = $item->toArray();
        $item->delete();
        logActivity('حذف بضاعة', "تم حذف البضاعة: {$item->name} بوحدة: {$item->unit}", $old, null);

        return redirect()->back()->with('success', 'تم حذف البضاعة بنجاح.');
    }

    public function inventory() {
        $inventories = BulkInventory::with(['customer', 'item'])->get();
        $items = BulkItem::all();
        $customers = Customer::all();
        return view('pages.bulk_inventory.bulk_inventory', compact('inventories', 'items', 'customers'));
    }

    public function addInventory(Request $request) {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'item_id' => 'required|exists:bulk_items,id',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        $exists = BulkInventory::where('customer_id', $validated['customer_id'])
            ->where('item_id', $validated['item_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'هذا العميل لديه بالفعل مخزون لهذه البضاعة.');
        }

        $inventory = BulkInventory::create($validated);
        logActivity('إضافة مخزون جديد', "تمت إضافة المخزون الجديد للعميل : {$inventory->customer->name}، البضاعة : {$inventory->item->name}، الرصيد: {$inventory->balance}، سعر الوحدة: {$inventory->price_per_unit}، نوع السعر: {$inventory->price_type}", null, $inventory->toArray());

        return redirect()->back()->with('success', 'تم إضافة المخزون الجديد بنجاح.');
    }

    public function updateInventory(Request $request, BulkInventory $inventory) {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'item_id' => 'required|exists:bulk_items,id',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        $old = $inventory->toArray();
        $inventory->update($validated);
        $new = $inventory->toArray();

        logActivity('تحديث بيانات مخزون', "تم تحديث بيانات المخزون ID: {$inventory->id} للعميل : {$inventory->customer->name}، البضاعة : {$inventory->item->name}، الرصيد: {$inventory->balance}، سعر الوحدة: {$inventory->price_per_unit}، نوع السعر: {$inventory->price_type}", $old, $new);
        return redirect()->back()->with('success', 'تم تحديث بيانات المخزون بنجاح.');
    }

    public function deleteInventory(BulkInventory $inventory) {
        $old = $inventory->toArray();
        $inventory->delete();
        logActivity('حذف مخزون', "تم حذف المخزون ID: {$inventory->id} للعميل : {$inventory->customer->name}، البضاعة : {$inventory->item->name}، الرصيد: {$inventory->balance}، سعر الوحدة: {$inventory->price_per_unit}، نوع السعر: {$inventory->price_type}", $old, null);

        return redirect()->back()->with('success', 'تم حذف المخزون بنجاح.');
    }

    public function inventoryDetails(BulkInventory $inventory) {
        $inventory->load(['customer', 'item', 'transactions.policy']);
        return view('pages.bulk_inventory.inventory_details', compact('inventory'));
    }

    public function reports() {
        $inventories = BulkInventory::with(['customer', 'item'])->get();
        return view('pages.bulk_inventory.reports', compact('inventories'));
    }
}
