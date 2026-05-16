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
                'message' => 'Pago procesado exitosamente',
                'payment' => $paymentResult,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
}
