<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function findByMetaOrderId($orderId);
}
