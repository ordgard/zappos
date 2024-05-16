<?php

namespace App\Services;

use App\Models\Tenants\Product;
use App\Models\Tenants\Purchasing;
use App\Models\Tenants\Stock;

class PurchasingService
{
    public function generateNumber($prefix = 'PO', $length = 8): string
    {
        $date = now()->format('Ymd');

        $countForToday = Purchasing::whereDate('created_at', today())->count();
        $sequentialNumber = str_pad($countForToday + 1, $length, '0', STR_PAD_LEFT);

        return $prefix.'-'.$date.$sequentialNumber;
    }

    public function create(array $data): Purchasing
    {
        $collection = collect($data['stocks']);
        $total_selling_price = $collection->sum('selling_price');
        $total_initial_price = $collection->sum('initial_price');
        $data['total_initial_price'] = $total_initial_price;
        $data['total_selling_price'] = $total_selling_price;
        $record = Purchasing::create($data);
        $collection->each(function ($item) use ($data, $record) {
            $item['date'] = $data['date'] ?? now();
            $stock = new Stock();
            $item['init_stock'] = $item['stock'];
            $stock->fill($item);
            $stock->product()->associate(Product::find($item['product_id']));
            $stock->purchasing()->associate($record);
            $stock->save();
        });

        return $record;
    }

    public function update(mixed $id, $data): Purchasing
    {
        /* TODO: please update this logic*/
        $collection = collect($data['stocks']);
        $total_selling_price = $collection->sum('selling_price');
        $total_initial_price = $collection->sum('initial_price');
        $data['total_initial_price'] = $total_initial_price;
        $data['total_selling_price'] = $total_selling_price;
        $record = Purchasing::find($id);
        $record->update($data);
        $collection->each(function ($item, $index) use ($data, $record) {
            $item['date'] = $data['date'] ?? now();
            dump($record->stocks->toArray()[$index]['id']);
            // $stock = Stock::find($record->stocks->toArray()[$index]['id']);
            // $stock->fill($item);
            // $stock->product()->associate(Product::find($item['product_id']));
            // $stock->purchasing()->associate($record);
            // $stock->save();
        });
        dd('OK');

        return $record;
    }
}
