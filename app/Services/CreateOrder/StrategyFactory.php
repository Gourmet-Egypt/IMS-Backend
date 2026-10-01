<?php

namespace App\Services\CreateOrder;

use App\Enums\TransferRequestTypeEnum;
use App\Services\CreateOrder\Contracts\OrderTypeStrategy;
use App\Services\CreateOrder\Strategies\PurchaseStrategy;
use App\Services\CreateOrder\Strategies\TransferInStrategy;
use App\Services\CreateOrder\Strategies\TransferOutStrategy;

class StrategyFactory
{
    public function for(TransferRequestTypeEnum $type): OrderTypeStrategy
    {
        return match ($type) {
            TransferRequestTypeEnum::TransferIN => new TransferInStrategy(),
            TransferRequestTypeEnum::TransferOut => new TransferOutStrategy(),

            TransferRequestTypeEnum::PO,
            TransferRequestTypeEnum::ReturnToSupplier => new PurchaseStrategy(),
        };
    }
}
