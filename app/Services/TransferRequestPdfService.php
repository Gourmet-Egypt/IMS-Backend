<?php

namespace App\Services;

use App\Models\TransferRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferRequestPdfService
{
    /**
     * Generate all 3 PDFs for transfer request
     *
     * @param TransferRequest $transferRequest
     * @return array ['transfer' => path, 'condition' => path, 'info' => path]
     */
    public function generateAllPdfs(TransferRequest $transferRequest): array
    {
        // Load relationships
        $transferRequest->load([
            'fromStore',
            'toStore',
            'requestedBy',
            'items.product'
        ]);

        // Create base directory
        $baseDirectory = $this->getBaseDirectory($transferRequest);
        Storage::makeDirectory($baseDirectory);

        // Generate all 3 PDFs
        $paths = [
            'transfer' => $this->generateTransferPdf($transferRequest, $baseDirectory),
            'condition' => $this->generateConditionPdf($transferRequest, $baseDirectory),
            'info' => $this->generateInfoPdf($transferRequest, $baseDirectory),
        ];

        Log::info('All PDFs generated successfully', [
            'transfer_request_id' => $transferRequest->id,
            'paths' => $paths,
        ]);

        return $paths;
    }

    /**
     * Generate Transfer PDF (main document with items list)
     */
    protected function generateTransferPdf(TransferRequest $transferRequest, string $directory): string
    {
        $pdf = Pdf::loadView('pdfs.transfer-request', [
            'transfer' => $transferRequest,
            'items' => $transferRequest->items,
            'fromStore' => $transferRequest->fromStore,
            'toStore' => $transferRequest->toStore,
            'requestedBy' => $transferRequest->requestedBy,
            'referenceNumber' => $transferRequest->getReferenceNumber(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Arial');

        $filename = $directory . '/transfer_' . $this->generateBaseFilename($transferRequest);
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate Condition PDF (terms and conditions document)
     */
    protected function generateConditionPdf(TransferRequest $transferRequest, string $directory): string
    {
        $pdf = Pdf::loadView('pdfs.transfer-condition', [
            'transfer' => $transferRequest,
            'fromStore' => $transferRequest->fromStore,
            'toStore' => $transferRequest->toStore,
            'referenceNumber' => $transferRequest->getReferenceNumber(),
            'conditions' => $this->getTransferConditions($transferRequest),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Arial');

        $filename = $directory . '/condition_' . $this->generateBaseFilename($transferRequest);
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate Info PDF (additional information document)
     */
    protected function generateInfoPdf(TransferRequest $transferRequest, string $directory): string
    {
        $pdf = Pdf::loadView('pdfs.transfer-info', [
            'transfer' => $transferRequest,
            'fromStore' => $transferRequest->fromStore,
            'toStore' => $transferRequest->toStore,
            'requestedBy' => $transferRequest->requestedBy,
            'referenceNumber' => $transferRequest->getReferenceNumber(),
            'shippingInfo' => $this->getShippingInfo($transferRequest),
            'contactInfo' => $this->getContactInfo($transferRequest),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Arial');

        $filename = $directory . '/info_' . $this->generateBaseFilename($transferRequest);
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Get base directory for PDFs
     */
    protected function getBaseDirectory(TransferRequest $transferRequest): string
    {
        return sprintf(
            'transfer-requests/%s/%s/TR-%d',
            date('Y'),
            date('m'),
            $transferRequest->id
        );
    }

    /**
     * Generate base filename
     */
    protected function generateBaseFilename(TransferRequest $transferRequest): string
    {
        return sprintf(
            '%s_%d.pdf',
            $transferRequest->getReferenceNumber(),
            time()
        );
    }

    /**
     * Get transfer conditions
     */
    protected function getTransferConditions(TransferRequest $transferRequest): array
    {
        return [
            'All items must be inspected upon receipt',
            'Damaged items must be reported within 24 hours',
            'Transfer must be completed within 7 business days',
            'Receiving store must confirm receipt',
            'Items cannot be returned after acceptance',
            'Both stores must maintain proper documentation',
        ];
    }

    /**
     * Get shipping information
     */
    protected function getShippingInfo(TransferRequest $transferRequest): array
    {
        return [
            'estimated_delivery' => now()->addDays(3)->format('Y-m-d'),
            'shipping_method' => 'Internal Transfer',
            'tracking_number' => 'TRK-' . $transferRequest->id . '-' . strtoupper(uniqid()),
            'priority' => $transferRequest->total_amount > 10000 ? 'High' : 'Normal',
        ];
    }

    /**
     * Get contact information
     */
    protected function getContactInfo(TransferRequest $transferRequest): array
    {
        return [
            'from_contact' => [
                'name' => $transferRequest->requestedBy->name,
                'email' => $transferRequest->requestedBy->email,
                'phone' => $transferRequest->fromStore->phone,
            ],
            'to_contact' => [
                'name' => $transferRequest->toStore->manager->name ?? 'Store Manager',
                'email' => $transferRequest->toStore->email,
                'phone' => $transferRequest->toStore->phone,
            ],
        ];
    }

    /**
     * Delete all PDFs for a transfer request
     */
    public function deletePdfs(TransferRequest $transferRequest): void
    {
        $paths = array_filter([
            $transferRequest->transfer_pdf_path,
            $transferRequest->condition_pdf_path,
            $transferRequest->info_pdf_path,
        ]);

        foreach ($paths as $path) {
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        Log::info('PDFs deleted', [
            'transfer_request_id' => $transferRequest->id,
            'deleted_count' => count($paths),
        ]);
    }
}
