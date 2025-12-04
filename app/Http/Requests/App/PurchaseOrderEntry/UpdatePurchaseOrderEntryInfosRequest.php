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
            'Batches' => 'present|array',
            'Batches.*.quantity_issued' => 'required|numeric|min:1',
            'Batches.*.production_date' => 'nullable|string',
            'Batches.*.expire_date' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $batches = $this->input('Batches', []);

        $batches = collect($batches)->map(function ($batch) {
            foreach (['production_date', 'expire_date'] as $field) {
                if (($batch[$field] ?? null) === 'N/A') {
                    $batch[$field] = "";
                }
            }

            return $batch;
        })->all();

        $this->merge([
            'Batches' => $batches,
        ]);
    }
}
