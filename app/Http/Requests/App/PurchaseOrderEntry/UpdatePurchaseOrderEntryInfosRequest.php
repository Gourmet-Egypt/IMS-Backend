<?php

namespace App\Http\Requests\App\PurchaseOrderEntry;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderEntryInfosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'Batches' => 'required|array|min:1',
            'Batches.*.quantity_issued' => 'required|numeric|min:1',
            'Batches.*.production_date' => 'required|date',
            'Batches.*.expire_date' => 'required|date',
        ];
    }
}
