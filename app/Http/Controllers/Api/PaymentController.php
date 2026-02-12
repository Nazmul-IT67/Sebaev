<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\DonationHistory;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Traits\ApiResponse;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function donate(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'movement_id' => 'required|exists:movements,id',
            'amount' => 'required|numeric',
        ]);

        if ($validateData->fails()) {
            return $this->error($validateData->errors(), $validateData->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found.', 200);
        }

        $movement = Movement::with('user')->find($request->movement_id);
        if (!$movement || !$movement->user || !$movement->user->stripe_account_id) {
            return $this->error([], 'Movement or Stripe account not found.', 200);
        }

        try {
            $amountInCents = (int) ($request->amount * 100);

            $cms = Cms::find(1);

            $percentageToTake = $cms->donation_amount ?? 0; // e.g., 10
            $percentageToTake = min($percentageToTake, 100); // Just to be safe

            $applicationFeeAmount = (int) ($amountInCents * ($percentageToTake / 100));

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => $movement->title,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'customer_email' => $user->email,
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
                'payment_intent_data' => [
                    'application_fee_amount' => $applicationFeeAmount,
                    'transfer_data' => [
                        'destination' => $movement->user->stripe_account_id, // Movement owner
                    ],
                ],
                'metadata' => [
                    'movement_id' => $movement->id,
                    'movement_title' => $movement->title,
                    'amount' => $request->amount,
                    'user_id' => $user->id,
                    'success_redirect_url' => $request->get('success_redirect_url'),
                    'cancel_redirect_url' => $request->get('cancel_redirect_url'),
                ],
            ]);

            return $this->success($checkoutSession->url, 'Checkout session created successfully.', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function checkoutSuccess(Request $request)
    {
        if (!$request->query('session_id')) {
            return $this->error([], 'Session ID not found.', 200);
        }

        DB::beginTransaction();
        try {
            $sessionId = $request->query('session_id');
            $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
            $metadata = $checkoutSession->metadata;

            $success_redirect_url = $metadata->success_redirect_url ?? null;
            $user_id = $metadata->user_id ?? null;
            $movement_id = $metadata->movement_id ?? null;
            $movement_title = $metadata->movement_title ?? null;
            $amount = $metadata->amount ?? null;

            $user = User::find($user_id);

            if (!$user) {
                return $this->error([], 'User not found.', 200);
            }

            $donationHistory = DonationHistory::create([
                'user_id' => $user_id,
                'movement_id' => $movement_id,
                'amount' => $amount,
            ]);

            DB::commit();
            return redirect($success_redirect_url);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function checkoutCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect($request->redirect_url ?? null);
        }

        $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
        $metadata = $checkoutSession->metadata;

        $cancel_redirect_url = $metadata->cancel_redirect_url ?? null;

        return redirect($cancel_redirect_url);
    }
}
