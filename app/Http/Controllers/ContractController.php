<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function contracts() {
        $contracts = Contract::all();
        return view('admin.contracts', compact('contracts'));
    }

    public function createContract() {
        return view('admin.createContract');
    }
}
