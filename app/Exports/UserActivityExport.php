<?php

namespace App\Exports;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserActivityExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $filters;

    public function __construct(array $filters) {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = UserLog::query();

        if(!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if(!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }
        if(!empty($this->filters['user_id']) && $this->filters['user_id'] !== 'all') {
            $query->where('user_id', $this->filters['user_id']);
        }
        if(!empty($this->filters['action']) && $this->filters['action'] !== 'all') {
            $query->where('action', $this->filters['action']);
        }
        $query->where('company_id', Auth::user()->company_id);

        $query->with('user');

        return $query->get()->map(function ($activity) {
            return [
                $activity->user ? $activity->user->name : '-',
                $activity->action,
                $activity->description ?? '-',
                $activity->created_at ? $activity->created_at->format('Y/m/d H:i') : '-',
            ];
        });
    }

    public function headings(): array {
        return [
            'المستخدم',
            'النشاط',
            'التفاصيل',
            'التاريخ',
        ];
    }
}

