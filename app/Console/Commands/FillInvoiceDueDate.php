<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FillInvoiceDueDate extends Command
{
    protected $signature = 'invoices:fill-due-date';

    protected $description = 'Fill due dates for old invoices';

    public function handle()
    {
        Invoice::with('customer.contract')->whereNull('due_date')->chunkById(100, function($invoices) {
            foreach($invoices as $invoice) {
                $gracePeriod = optional($invoice->customer?->contract)->payment_grace_period ?? 15;
                $dueDate = Carbon::parse($invoice->date)->addDays((int) $gracePeriod);
                $invoice->update(['due_date' => $dueDate]);
            }
        });

        $this->info('Invoice due dates filled successfully.');
    }
}
