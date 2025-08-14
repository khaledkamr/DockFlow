<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users() {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function admins() {
        return view('admin.admins');
    }

    public function createUser(UserRequest $request) {
        $validated = $request->validated();
        User::create($validated);
        return redirect()->back()->with('success', 'تم إنشاء عميل جديد بنجاح');
    }

    public function updateUser(UserRequest $request, $id) {
        $user = User::findOrFail($id);
        $validated = $request->validated();
        $user->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function deleteUser($id) {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();
        return redirect()->back()->with('success', 'تم حذف العميل ' . $name . ' بنجاح');
    }

    public function yard() {
        $yardData = [
            [
                'id' => 1,
                'name' => 'container 1',
                'location' => 'A12',
                'capacity' => 100,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'container 2',
                'location' => 'B34',
                'capacity' => 200,
                'status' => 'inactive',
            ],
            [
                'id' => 3,
                'name' => 'container 3',
                'location' => 'C23',
                'capacity' => 150,
                'status' => 'active',
            ],
        ];
        return view('admin.yard', compact('yardData'));
    }

    public function yardAdd() {
        return view('admin.yardAdd');
    }

    public function contracts() {
        return view('admin.contracts');
    }

    public function contractsCreate() {
        return view('admin.contractsCreate');
    }

    public function invoices() {
        return view('admin.invoices');
    }

    public function payments() {
        return view('admin.payments');
    }
}
