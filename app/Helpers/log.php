<?php

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if (!function_exists('logActivity')) {
    function logActivity($action, $description = null, $old = null, $new = null)
    {
        try {
            $diff = null;

            if (is_array($old) && is_array($new)) {
                $diff = [];
                foreach ($old as $key => $value) {
                    if (array_key_exists($key, $new) && $new[$key] != $value) {
                        $diff[$key] = [
                            'old' => $value,
                            'new' => $new[$key],
                        ];
                    }
                }
            }

            UserLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'old_data' => $old,
                'new_data' => $new,
                'diff' => $diff,
                'ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'company_id' => Auth::user()->company_id ?? null,
            ]);

        } catch (\Throwable $e) {
            // Avoid crashing the app if logging fails
            Log::error("LogActivity Failed: " . $e->getMessage());
        }
    }
}
