<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Services\PaymentService;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    protected $submissionRepo;
    protected $paymentService;
    protected $paymentRepo;

    public function __construct(
        SubmissionRepositoryInterface $submissionRepo,
        PaymentService $paymentService,
        PaymentRepositoryInterface $paymentRepo
    ) {
        $this->submissionRepo = $submissionRepo;
        $this->paymentService = $paymentService;
        $this->paymentRepo = $paymentRepo;
    }

    public function initiate(Request $r)
    {
        $data = $r->validate([
            'submission_id' => 'required|exists:submissions,id',
            'gateway' => 'required|in:razorpay,stripe'
        ]);

        $submission = $this->submissionRepo->find($data['submission_id']);
        if (!$submission || $submission->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($data['gateway'] === 'razorpay') {
            return response()->json($this->paymentService->initiateRazorpay($submission, auth()->user()));
        }

        return response()->json($this->paymentService->initiateStripe($submission, auth()->user()));
    }

    public function webhook(Request $r)
    {
        $payload = $r->all();

        if ($r->hasHeader('X-Razorpay-Signature') || isset($payload['payload']['payment'])) {
            $orderId = $payload['payload']['payment']['entity']['order_id'] ?? $r->input('razorpay_order_id');
            $txId = $payload['payload']['payment']['entity']['id'] ?? $r->input('razorpay_payment_id');

            $payment = $this->paymentRepo->findByMetaOrderId($orderId);
            if ($payment) {
                $this->paymentService->markSuccess($payment->id, $txId, $payload);
            }
            return response()->json(['ok' => true]);
        }

        if ($r->hasHeader('Stripe-Signature') || isset($payload['type'])) {
            $event = $payload;
            if ($event['type'] === 'payment_intent.succeeded') {
                $intent = $event['data']['object'];
                $paymentId = $intent['metadata']['payment_id'] ?? null;
                if ($paymentId) {
                    $this->paymentService->markSuccess($paymentId, $intent['id'], $intent);
                }
            }
            return response()->json(['ok' => true]);
        }

        return response()->json(['message' => 'unhandled'], 400);
    }

    public function confirmPayment(Request $r)
    {
        $data = $r->validate([
            'payment_id' => 'required|exists:payments,id',
            'transaction_id' => 'required|string'
        ]);
        $payment = $this->paymentRepo->find($data['payment_id']);
        if ($payment->user_id !== auth()->id()) return response()->json(['message' => 'Forbidden'], 403);
        return response()->json($this->paymentService->markSuccess($payment->id, $data['transaction_id']));
    }

    public function downloadReceipt($id)
    {
        $payment = $this->paymentRepo->find($id);
        if (!$payment) return response()->json(['message' => 'Not found'], 404);
        if ($payment->user_id !== auth()->id() && auth()->user()->role !== 'admin') return response()->json(['message' => 'Forbidden'], 403);
        $path = $payment->meta['receipt_path'] ?? null;
        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Receipt not found'], 404);
        }
        return response()->download(storage_path("app/public/{$path}"));
    }
}
