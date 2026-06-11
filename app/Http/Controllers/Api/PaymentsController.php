<?php

namespace App\Http\Controllers\Api;


use App\Core\UseCases\Payments\PaymentMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\CreatePaymentRequest as PaymentsCreatePaymentRequest;
use App\Models\Payments\Transaction;
use App\Models\Therapists\Booking;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function createPaymentIntent(PaymentsCreatePaymentRequest $request)
    {

        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($request->booking_id);

            $paymentAction = PaymentMethodFactory::create($request->payment_method);

            $paymentResult = $paymentAction->processPayment(
                transaction: $booking->transaction
            );
            DB::commit();
            return response()->json([
                'message' => 'Payment initiated',
                'payment_url' => $paymentResult->payment_url,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Payment processing error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'No se pudo procesar el pago. Por favor intentá de nuevo.',
            ], 500);
        }
    }
}
