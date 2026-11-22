<?php

namespace App\Http\Requests\Dashboard;

use App\Repositories\Reports\ReportFilters;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }

    public function filters(): ReportFilters
    {
        return ReportFilters::fromArray($this->validated());
    }
}
