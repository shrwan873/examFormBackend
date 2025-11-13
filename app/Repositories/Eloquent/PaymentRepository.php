<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data)
    {
        return Payment::create($data);
    }
    public function find($id)
    {
        return Payment::find($id);
    }
    public function update($id, array $data)
    {
        $p = Payment::findOrFail($id);
        $p->update($data);
        return $p;
    }
    public function findByMetaOrderId($orderId)
    {
        return Payment::where('meta->order->id', $orderId)->first();
    }
}
