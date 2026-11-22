<?php

namespace App\Http\Resources\Dashboard\Reports;

use App\Models\GoodsType;

final class StandardTemperature
{
    public static function label(?GoodsType $goodsType): ?string
    {
        if ($goodsType?->min_temp === null || $goodsType?->max_temp === null) {
            return null;
        }

        return (float) $goodsType->min_temp . ' to ' . (float) $goodsType->max_temp . ' °C';
    }
}
