<?php

namespace App\Services;

class PurchaseOrderTracker
{
    private array $expectedPurchaseOrders = [];


    public function expect(string $purchaseOrderNumber, int $transferRequestId): void
    {
        $this->expectedPurchaseOrders[$purchaseOrderNumber] = $transferRequestId;
    }


    public function getTransferRequestId(string $purchaseOrderNumber): ?int
    {
        return $this->expectedPurchaseOrders[$purchaseOrderNumber] ?? null;
    }


    public function isExpected(string $purchaseOrderNumber): bool
    {
        return isset($this->expectedPurchaseOrders[$purchaseOrderNumber]);
    }


    public function forget(string $purchaseOrderNumber): void
    {
        unset($this->expectedPurchaseOrders[$purchaseOrderNumber]);
    }


    public function clear(): void
    {
        $this->expectedPurchaseOrders = [];
    }
}
