<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function categories(Request $request) {
        $categories = Category::query();
        $search = $request->input('search');

        if ($search) {
            $categories->where('name_ar', 'like', "%{$search}%");
        }

        $categories = $categories->paginate(100)->onEachSide(1)->withQueryString();

        return view('pages.inventory.categories', compact('categories'));
    }

    public function storeCategory(Request $request) {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $category = Category::create($request->only('name_ar', 'parent_id', 'user_id'));

        logActivity('إضافة فئة جديدة', 'تم إضافة الفئة الجديدة: ' . $request->name_ar, null, $category->toArray());

        return redirect()->route('inventory.categories')->with('success', 'تم إضافة الفئة بنجاح.');
    }

    public function updateCategory(Request $request, Category $category) {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $old = $category->toArray();
        $category->update($request->only('name_ar', 'parent_id'));
        $new = $category->toArray();

        logActivity('تحديث فئة', 'تم تحديث الفئة: ' . $request->name_ar, $old, $new);

        return redirect()->route('inventory.categories')->with('success', 'تم تحديث الفئة بنجاح.');
    }

    public function deleteCategory(Category $category) {
        $old = $category->toArray();
        $category->delete();
        logActivity('حذف فئة', 'تم حذف الفئة: ' . $category->name_ar, $old, null);
        return redirect()->route('inventory.categories')->with('success', 'تم حذف الفئة بنجاح.');
    }

    public function products(Request $request) {
        $categories = Category::all();
        $products = Product::query();
        $search = $request->input('search');

        if ($search) {
            $products->where('name_ar', 'like', "%{$search}%");
        }

        $products = $products->paginate(100)->onEachSide(1)->withQueryString();

        return view('pages.inventory.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request) {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $product = Product::create($request->only('name_ar', 'name_en', 'category_id', 'sku', 'unit', 'description', 'user_id'));
        logActivity('إضافة منتج جديد', 'تم إضافة المنتج الجديد: ' . $request->name_ar, null, $product->toArray());
        return redirect()->route('inventory.products')->with('success', 'تم إضافة المنتج بنجاح.');
    }

    public function updateProduct(Request $request, Product $product) {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $old = $product->toArray();
        $product->update($request->only('name_ar', 'name_en', 'category_id', 'sku', 'unit', 'description'));
        $new = $product->toArray();

        logActivity('تحديث منتج', 'تم تحديث المنتج: ' . $request->name_ar, $old, $new);

        return redirect()->route('inventory.products')->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function deleteProduct(Product $product) {
        $old = $product->toArray(); 
        $product->delete();
        logActivity('حذف منتج', 'تم حذف المنتج: ' . $product->name_ar, $old, null);
        return redirect()->route('inventory.products')->with('success', 'تم حذف المنتج بنجاح.');
    }
}
