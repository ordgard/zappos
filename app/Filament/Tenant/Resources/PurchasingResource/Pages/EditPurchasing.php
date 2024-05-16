<?php

namespace App\Filament\Tenant\Resources\PurchasingResource\Pages;

use App\Filament\Tenant\Resources\PurchasingResource;
use App\Models\Tenants\Purchasing;
use App\Services\PurchasingService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditPurchasing extends EditRecord
{
    protected static string $resource = PurchasingResource::class;

    private PurchasingService $purchasingService;

    public function __construct()
    {
        $this->purchasingService = new PurchasingService();
    }

    protected function fillFormWithDataAndCallHooks(Model|Purchasing $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');
        $record->load('stocks');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->toArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return '#'.$this->getRecord()->number;
    }

    protected function handleRecordUpdate(Purchasing|Model $record, array $data): Model
    {
        return $this->purchasingService->update($record->getKey(), $data);
    }
}
