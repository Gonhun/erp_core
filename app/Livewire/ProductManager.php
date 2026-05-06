<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Brand;
use App\Models\UomCategory;
use App\Models\ItemCategory;
use App\Models\SubItemCategory;
use App\Models\ProductSubstitute;
use Illuminate\Support\Facades\DB;

class ProductManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;
    public $activeTab = 'general'; // general, substitutes

    // Product Fields
    public $product_code = '';
    public $product_name = '';
    public $product_type_id = '';
    public $invoicing_policy = 'Ordered';
    public $brand_id = '';
    public $uom_category_id = '';
    public $purchase_uom_category_id = '';
    public $item_category_id = '';
    public $sub_item_id = '';
    public $part_number = '';
    public $product_notes = '';
    public $is_active = true;

    // Substitutes
    public $substitutes = [];

    public function render()
    {
        $products = Product::with(['productType', 'brand', 'uomCategory', 'itemCategory'])->orderBy('product_code')->get();
        $types = ProductType::where('is_active', true)->orderBy('product_type_name')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();
        $uoms = UomCategory::where('is_active', true)->orderBy('base_uom')->get();
        $categories = ItemCategory::where('is_active', true)->orderBy('category_name')->get();

        $subCategories = [];
        if ($this->item_category_id) {
            $subCategories = SubItemCategory::where('item_category_id', $this->item_category_id)->where('is_active', true)->orderBy('sub_category_name')->get();
        }

        // For substitute selection, exclude current product
        $allProducts = Product::where('is_active', true)
            ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
            ->orderBy('product_name')
            ->get();

        return view('livewire.product-manager', [
            'products' => $products,
            'types' => $types,
            'brands' => $brands,
            'uoms' => $uoms,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'allProducts' => $allProducts,
        ])->layout('layouts.app');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetFields();
        $this->activeTab = 'general';
        $this->iteration++;
    }

    private function resetFields()
    {
        $this->product_code = '';
        $this->product_name = '';
        $this->product_type_id = '';
        $this->invoicing_policy = 'Ordered';
        $this->brand_id = '';
        $this->uom_category_id = '';
        $this->purchase_uom_category_id = '';
        $this->item_category_id = '';
        $this->sub_item_id = '';
        $this->part_number = '';
        $this->product_notes = '';
        $this->is_active = true;
        $this->substitutes = [];
    }

    public function addSubstituteRow()
    {
        $this->substitutes[] = [
            'substitute_product_id' => '',
            'priority' => count($this->substitutes) + 1,
            'is_active' => true
        ];
    }

    public function removeSubstituteRow($index)
    {
        unset($this->substitutes[$index]);
        $this->substitutes = array_values($this->substitutes);
    }

    public function save()
    {
        $rules = [
            'product_code' => 'required|string|unique:products,product_code,' . ($this->editingId ?: 'NULL') . ',id',
            'product_name' => 'required|string|max:255',
            'product_type_id' => 'required|exists:product_types,id',
            'uom_category_id' => 'required|exists:uom_categories,id',
            'purchase_uom_category_id' => 'required|exists:uom_categories,id',
            'item_category_id' => 'required|exists:item_categories,id',
            'substitutes.*.substitute_product_id' => 'required|exists:products,id',
        ];

        $this->validate($rules);

        DB::transaction(function () {
            $data = [
                'product_code' => $this->product_code,
                'product_name' => $this->product_name,
                'product_type_id' => $this->product_type_id,
                'invoicing_policy' => $this->invoicing_policy,
                'brand_id' => $this->brand_id ?: null,
                'uom_category_id' => $this->uom_category_id,
                'purchase_uom_category_id' => $this->purchase_uom_category_id,
                'item_category_id' => $this->item_category_id,
                'sub_item_id' => $this->sub_item_id ?: null,
                'part_number' => $this->part_number,
                'product_notes' => $this->product_notes,
                'is_active' => (bool) $this->is_active,
            ];

            if ($this->editingId) {
                $product = Product::findOrFail($this->editingId);
                $product->update($data);
            } else {
                $product = Product::create($data);
            }

            // Sync Substitutes
            ProductSubstitute::where('product_id', $product->id)->delete();
            foreach ($this->substitutes as $sub) {
                ProductSubstitute::create([
                    'product_id' => $product->id,
                    'substitute_product_id' => $sub['substitute_product_id'],
                    'priority' => $sub['priority'],
                    'is_active' => $sub['is_active'],
                ]);
            }
        });

        $this->isCreating = false;
        $this->editingId = null;
        $this->iteration++;
    }

    public function edit($id)
    {
        $p = Product::with('substitutes')->findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;
        
        $this->product_code = $p->product_code;
        $this->product_name = $p->product_name;
        $this->product_type_id = $p->product_type_id;
        $this->invoicing_policy = $p->invoicing_policy;
        $this->brand_id = $p->brand_id;
        $this->uom_category_id = $p->uom_category_id;
        $this->purchase_uom_category_id = $p->purchase_uom_category_id;
        $this->item_category_id = $p->item_category_id;
        $this->sub_item_id = $p->sub_item_id;
        $this->part_number = $p->part_number;
        $this->product_notes = $p->product_notes;
        $this->is_active = $p->is_active;

        $this->substitutes = $p->substitutes->map(fn($s) => [
            'substitute_product_id' => $s->substitute_product_id,
            'priority' => $s->priority,
            'is_active' => $s->is_active,
        ])->toArray();

        $this->activeTab = 'general';
        $this->iteration++;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->editingId = null;
        $this->resetFields();
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) Product::findOrFail($id)->delete();
    }
}
