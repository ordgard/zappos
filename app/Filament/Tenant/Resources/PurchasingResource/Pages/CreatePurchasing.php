<?php

namespace App\Filament\Tenant\Resources\PurchasingResource\Pages;

use App\Filament\Tenant\Resources\PurchasingResource;
use App\Services\PurchasingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreatePurchasing extends CreateRecord
{
    protected static string $resource = PurchasingResource::class;

    private PurchasingService $purchasingService;

    public function __construct()
    {
        $this->purchasingService = new PurchasingService();
    }

    public function mount(): void
    {
        parent::mount();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['number'] = $this->purchasingService->generateNumber();

        return $this->purchasingService->create($data);
    }

    public function getTitle(): string|Htmlable
    {
        return '#'.$this->purchasingService->generateNumber();
    }

    protected function getRedirectUrl(): string
    {
        return PurchasingResource::getNavigationUrl();
    }
}
