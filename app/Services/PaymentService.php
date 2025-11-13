<?php
namespace App\Services;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Models\Payment;
use Razorpay\Api\Api as RazorpayApi;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use PDF;
use Illuminate\Support\Facades\Storage;

class PaymentService {
    protected $paymentRepo;
    protected $submissionRepo;

    public function __construct(
        PaymentRepositoryInterface $paymentRepo,
        SubmissionRepositoryInterface $submissionRepo
    ){
        $this->paymentRepo = $paymentRepo;
        $this->submissionRepo = $submissionRepo;
    }

    public function initiateRazorpay($submission, $user) {
        $amount = $submission->form->fee;
        $payment = $this->paymentRepo->create([
            'user_id' => $user->id,
            'submission_id' => $submission->id,
            'amount' => $amount,
            'status' => 'initiated',
            'gateway' => 'razorpay'
        ]);

        $api = new RazorpayApi(config('services.razorpay.key'), config('services.razorpay.secret'));
        $order = $api->order->create([
            'receipt' => "rcpt_" . $payment->id,
            'amount' => intval($amount * 100),
            'currency' => 'INR'
        ]);

        $orderArray = $order->toArray();

        $this->paymentRepo->update($payment->id, ['meta' => ['order' => $orderArray]]);
        return ['payment' => $this->paymentRepo->find($payment->id), 'order' => $orderArray];
    }

    public function initiateStripe($submission, $user) {
        $amount = $submission->form->fee;
        $payment = $this->paymentRepo->create([
            'user_id' => $user->id,
            'submission_id' => $submission->id,
            'amount' => $amount,
            'status' => 'initiated',
            'gateway' => 'stripe'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::create([
            'amount' => intval($amount * 100),
            'currency' => 'inr',
            'metadata' => ['payment_id' => $payment->id, 'submission_id' => $submission->id]
        ]);
        $this->paymentRepo->update($payment->id, ['meta' => ['payment_intent' => $intent]]);
        return ['payment' => $this->paymentRepo->find($payment->id), 'client_secret' => $intent->client_secret];
    }

    public function markSuccess($paymentId, $transactionId = null, $meta = []) {
        $payment = $this->paymentRepo->find($paymentId);
        if (!$payment) return null;

        $this->paymentRepo->update($paymentId, [
            'status' => 'success',
            'transaction_id' => $transactionId,
            'meta' => array_merge($payment->meta ?? [], $meta)
        ]);
        $submission = $this->submissionRepo->find($payment->submission_id);
        if ($submission) {
            $submission->status = 'paid';
            $submission->save();
        }
        $this->generateReceipt($paymentId);
        return $this->paymentRepo->find($paymentId);
    }

    protected function generateReceipt($paymentId) {
        $payment = $this->paymentRepo->find($paymentId);
        $submission = $this->submissionRepo->find($payment->submission_id);
        $form = $submission->form;
        $user = $submission->user;

        $data = [
            'payment' => $payment->toArray(),
            'submission' => $submission->toArray(),
            'form' => $form->toArray(),
            'user' => $user->toArray(),
            'date' => now()->toDateTimeString()
        ];

        $pdf = PDF::loadView('receipts.payment', $data);
        $filename = "receipts/receipt_{$payment->id}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $this->paymentRepo->update($payment->id, ['meta' => array_merge($payment->meta ?? [], ['receipt_path' => $filename])]);
        return $filename;
    }
}
