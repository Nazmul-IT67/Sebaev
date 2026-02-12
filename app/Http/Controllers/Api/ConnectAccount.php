<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Traits\ApiResponse;
use Stripe\Account;
use Stripe\AccountLink;

class ConnectAccount extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function connectAccount(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found.', 200);
        }

        $userStripeAccountId = $user ? $user->stripe_account_id : null;

        if (!$userStripeAccountId) {
            $account = $stripe->accounts->create([
                'type' => 'express',
                'capabilities' => [
                    'transfers' => ['requested' => true],
                ],
            ]);

            $user->update(['stripe_account_id' => $account->id]);
        } else {
            $account = $stripe->accounts->retrieve($userStripeAccountId);
        }

        // return $account;

        if ($user->status == 'Enabled') {
            return $this->error([], 'Your account is already connected.', 200);
        }
        if ($account && $account->payouts_enabled == true) {
            return $this->error([], 'Your account is already connected.', 200);
        }

        $accountLink = $stripe->accountLinks->create([
            'account' => $account->id,
            'refresh_url' => route('connect.cancel') . "?id=" . $account->id . "&userId=" . $user->id . "&success_redirect_url=" . $request->success_redirect_url . "&cancel_redirect_url=" . $request->cancel_redirect_url,
            'return_url' => route('connect.success') . "?id=" . $account->id . "&userId=" . $user->id . "&success_redirect_url=" . $request->success_redirect_url . "&cancel_redirect_url=" . $request->cancel_redirect_url,
            'type' => 'account_onboarding',
        ]);

        return response()->json(['url' => $accountLink]);
    }

    public function success(Request $request)
    {
        $account = Account::retrieve($request->id);

        $user = User::find($request->get('userId'));

        if (!$user) {
            return $this->error([], 'User not found.', 200);
        }

        if (!$account->details_submitted || !$account->payouts_enabled) {
            $user->update([
                'status' => 'Rejected'
            ]);
            return redirect()->away($request->get('cancel_redirect_url'));
        }

        $user->update([
            'status' => 'Enabled'
        ]);

        return redirect($request->get('success_redirect_url'));
    }

    public function cancel(Request $request)
    {
        $link = AccountLink::create([
            'account' => $request->id,
            'refresh_url' => route('connect.cancel') . "?id=" . $request->id . "&userId=" . $request->userId . "&success_redirect_url=" . $request->success_redirect_url . "&cancel_redirect_url=" . $request->cancel_redirect_url,
            'return_url' => route('connect.success') . "?id=" . $request->id . "&userId=" . $request->userId . "&success_redirect_url=" . $request->success_redirect_url . "&cancel_redirect_url=" . $request->cancel_redirect_url,
            'type' => 'account_onboarding',
        ]);
        return redirect($link->url);
    }
}
