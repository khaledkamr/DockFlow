<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function companies() {
        $companies = Company::all();
        return view('admin.companies', compact('companies'));
    }

    public function storeCompany() {
        //
    }

    public function companyDetails() {
        return view('admin.company_details');
    }

    public function users() {
        $users = User::all();
        return view('admin.users', compact('users'));
    }
}
