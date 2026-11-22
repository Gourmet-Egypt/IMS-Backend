<?php

namespace App\Repositories\Reports;

use Carbon\Carbon;

final class ReportFilters
{
    public function __construct(
        public readonly ?int $storeId,
        public readonly Carbon $from,
        public readonly Carbon $to,
    ) {
    }

    public static function fromArray(array $input): self
    {
        return new self(
            storeId: isset($input['store_id']) ? (int) $input['store_id'] : null,
            from: isset($input['date_from']) ? Carbon::parse($input['date_from'])->startOfDay() : now()->startOfMonth(),
            to: isset($input['date_to']) ? Carbon::parse($input['date_to'])->endOfDay() : now()->endOfDay(),
        );
    }

    public function toArray(): array
    {
        return [
            'store_id' => $this->storeId,
            'date_from' => $this->from->toDateString(),
            'date_to' => $this->to->toDateString(),
        ];
    }
}
