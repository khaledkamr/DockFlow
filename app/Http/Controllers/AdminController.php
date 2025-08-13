<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users() {
        return view('admin.users');
    }

    public function usersCreate() {
        return view('admin.usersCreate');
    }

    public function admins() {
        return view('admin.admins');
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
