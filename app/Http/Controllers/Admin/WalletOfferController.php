<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletOfferController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $query = WalletOffer::query();

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $walletOffers = $query
            ->orderByRaw('CAST(amount AS DECIMAL(12,2)) ASC')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.wallet-offers.index',
            compact('walletOffers')
        );
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'add_on_percentage' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'required',
                'string',
            ],
        ]);

        $amount = (float) $validated['amount'];

        $percentage =
            (float) $validated['add_on_percentage'];

        $bonusAmount =
            ($amount * $percentage) / 100;

        $finalAmount =
            $amount + $bonusAmount;

        WalletOffer::create([
            'title' => $validated['title'],

            'amount' => number_format(
                $amount,
                2,
                '.',
                ''
            ),

            'add_on_percentage' => number_format(
                $percentage,
                2,
                '.',
                ''
            ),

            'final_amount' => number_format(
                $finalAmount,
                2,
                '.',
                ''
            ),

            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('admin.wallet-offers.index')
            ->with(
                'success',
                'Wallet offer created successfully.'
            );
    }

    public function update(
        Request $request,
        $id
    ) {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $walletOffer =
            WalletOffer::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'add_on_percentage' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'required',
                'string',
            ],
        ]);

        $amount =
            (float) $validated['amount'];

        $percentage =
            (float) $validated['add_on_percentage'];

        $bonusAmount =
            ($amount * $percentage) / 100;

        $finalAmount =
            $amount + $bonusAmount;

        $walletOffer->update([
            'title' => $validated['title'],

            'amount' => number_format(
                $amount,
                2,
                '.',
                ''
            ),

            'add_on_percentage' => number_format(
                $percentage,
                2,
                '.',
                ''
            ),

            'final_amount' => number_format(
                $finalAmount,
                2,
                '.',
                ''
            ),

            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('admin.wallet-offers.index')
            ->with(
                'success',
                'Wallet offer updated successfully.'
            );
    }

    public function destroy($id)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $walletOffer =
            WalletOffer::findOrFail($id);

        $walletOffer->delete();

        return redirect()
            ->route('admin.wallet-offers.index')
            ->with(
                'success',
                'Wallet offer deleted successfully.'
            );
    }
}
