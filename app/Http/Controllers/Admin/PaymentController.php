<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        if (! $admin->hasPermission('view-payments')) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Payments Query
        |--------------------------------------------------------------------------
        */

        $query = Payment::query()
            ->with('member');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($query) use ($search) {

                $query->where(
                    'payment_id',
                    'like',
                    "%{$search}%"
                )
                    ->orWhereHas('member', function ($memberQuery) use ($search) {

                        $memberQuery
                            ->where(
                                'profile_id',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'full_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'mobile_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Plan Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('plan_id')) {

            $query->where(
                'plan_id',
                $request->plan_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'payment_date',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'payment_date',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        $payments = $query
            ->orderByDesc('payment_date')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalPayments = Payment::count();

        $totalAmount = Payment::sum('amount');

        $todayAmount = Payment::whereDate(
            'payment_date',
            today()
        )->sum('amount');

        $thisMonthAmount = Payment::whereBetween(
            'payment_date',
            [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ]
        )->sum('amount');

        return view(
            'admin.payments.index',
            compact(
                'payments',
                'totalPayments',
                'totalAmount',
                'todayAmount',
                'thisMonthAmount'
            )
        );
    }
}
