<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerRequest;
use App\Http\Requests\ContainerTypesRequest;
use App\Http\Requests\UserRequest;
use App\Models\Container;
use App\Models\Container_type;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users() {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function admins() {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.admins', compact('users'));
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
        $containers = Container::orderBy('id', 'desc')->get();
        return view('admin.yard', compact('containers'));
    }

    public function yardAdd(Request $request) {
        $users = User::orderBy('name', 'asc')->get();
        $containerTypes = Container_type::all();
        $clientId = $request->input('user_id', null);
        $client = [
            'id' => '',
            'name' => '',
            'NID' => '',
            'phone' => '',
        ];
        
        if($clientId) {
            $user = User::find($clientId);
            $client = [
                'id' => $user->id,
                'name' => $user->name,
                'NID' => $user->NID,
                'phone' => $user->phone,
            ];
        }

        return view('admin.createContainer', compact('users', 'containerTypes', 'client'));
    }

    public function containerCreate(ContainerRequest $request) {
        $validated = $request->validated();
        Container::create($validated);
        return redirect()->back()->with('success', 'تم إضافة حاوية جديدة بنجاح');
    }

    public function containersTypes() {
        $containerTypes = Container_type::all();
        return view('admin.containersTypes', compact('containerTypes'));
    }

    public function containerTypeCreate(ContainerTypesRequest $request) {
        $validated = $request->validated();
        Container_type::create($validated);
        return redirect()->back()->with('success', 'تم إضافة نوع حاوية جديد بنجاح');
    }

    public function updateContainerType(ContainerTypesRequest $request, $id) {
        $containerType = Container_type::findOrFail($id);
        $validated = $request->validated();
        $containerType->update($validated);
        return redirect()->back()->with('success', 'تم تحديث بيانات الفئــة بنجاح');
    }

    public function deleteContainerType($id) {
        $containerType = Container_type::findOrFail($id);
        $name = $containerType->name;
        $containerType->delete();
        return redirect()->back()->with('success', 'تم حذف ' . $name . ' بنجاح');
    }

    public function contracts() {
        $contracts = Contract::orderBy('id', 'desc')->get();
        $users = User::all();
        return view('admin.contracts', compact('contracts', 'users'));
    }

    public function createContract(Request $request) {
        $users = User::orderBy('name', 'asc')->get();
        $clientId = $request->input('user_id', null);
        $client = [
            'id' => '',
            'name' => '',
            'NID' => '',
            'phone' => '',
        ];
        if($clientId) {
            $user = User::find($clientId);
            $client = [
                'id' => $user->id,
                'name' => $user->name,
                'NID' => $user->NID,
                'phone' => $user->phone,
            ];
        }
        return view('admin.createContract', compact('users', 'client'));
    }

    public function invoices() {
        return view('admin.invoices');
    }

    public function payments() {
        return view('admin.payments');
    }
}
