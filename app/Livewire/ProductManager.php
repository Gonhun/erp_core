<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Brand;
use App\Models\Uom;
use App\Models\ItemCategory;
use App\Models\SubItemCategory;

class ProductManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    // Fields
    public $new_product_code = '';
    public $new_product_name = '';
    public $new_product_type_id = '';
    public $new_invoicing_policy = 'Ordered';
    public $new_brand_id = '';
    public $new_uom_id = '';
    public $new_purchase_uom_id = '';
    public $new_item_category_id = '';
    public $new_sub_item_id = '';
    public $new_part_number = '';
    public $new_product_notes = '';

    public $edit_product_code = '';
    public $edit_product_name = '';
    public $edit_product_type_id = '';
    public $edit_invoicing_policy = '';
    public $edit_brand_id = '';
    public $edit_uom_id = '';
    public $edit_purchase_uom_id = '';
    public $edit_item_category_id = '';
    public $edit_sub_item_id = '';
    public $edit_part_number = '';
    public $edit_product_notes = '';

    public function render()
    {
        $products = Product::with(['productType', 'brand', 'uom', 'itemCategory'])->orderBy('product_code')->get();
        $types = ProductType::where('is_active', true)->orderBy('product_type_name')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();
        $uoms = Uom::where('is_active', true)->orderBy('uom_name')->get();
        $categories = ItemCategory::where('is_active', true)->orderBy('category_name')->get();
        
        $newSubCategories = [];
        if ($this->new_item_category_id) {
            $newSubCategories = SubItemCategory::where('item_category_id', $this->new_item_category_id)->where('is_active', true)->orderBy('sub_category_name')->get();
        }

        $editSubCategories = [];
        if ($this->edit_item_category_id) {
            $editSubCategories = SubItemCategory::where('item_category_id', $this->edit_item_category_id)->where('is_active', true)->orderBy('sub_category_name')->get();
        }

        return view('livewire.product-manager', [
            'products' => $products,
            'types' => $types,
            'brands' => $brands,
            'uoms' => $uoms,
            'categories' => $categories,
            'newSubCategories' => $newSubCategories,
            'editSubCategories' => $editSubCategories,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetNewFields();
        $this->new_product_code = $this->generateProductCode();
        $this->iteration++;
    }

    private function generateProductCode()
    {
        $last = Product::where('product_code', 'like', 'PRD-%')->get();
        if ($last->isEmpty()) return 'PRD-001';
        
        $maxNum = 0;
        foreach ($last as $p) {
            $num = (int) str_replace('PRD-', '', $p->product_code);
            if ($num > $maxNum) $maxNum = $num;
        }
        return 'PRD-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function resetNewFields()
    {
        $this->new_product_code = '';
        $this->new_product_name = '';
        $this->new_product_type_id = '';
        $this->new_invoicing_policy = 'Ordered';
        $this->new_brand_id = '';
        $this->new_uom_id = '';
        $this->new_purchase_uom_id = '';
        $this->new_item_category_id = '';
        $this->new_sub_item_id = '';
        $this->new_part_number = '';
        $this->new_product_notes = '';
    }

    public function cancelNew()
    {
        $this->isCreating = false;
        $this->resetNewFields();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'new_product_code' => 'required|string|unique:products,product_code',
            'new_product_name' => 'required|string|max:255',
            'new_product_type_id' => 'required|exists:product_types,id',
            'new_uom_id' => 'required|exists:uoms,id',
            'new_purchase_uom_id' => 'required|exists:uoms,id',
            'new_item_category_id' => 'required|exists:item_categories,id',
        ]);

        Product::create([
            'product_code' => $this->new_product_code,
            'product_name' => $this->new_product_name,
            'product_type_id' => $this->new_product_type_id,
            'invoicing_policy' => $this->new_invoicing_policy,
            'brand_id' => $this->new_brand_id ?: null,
            'uom_id' => $this->new_uom_id,
            'purchase_uom_id' => $this->new_purchase_uom_id,
            'item_category_id' => $this->new_item_category_id,
            'sub_item_id' => $this->new_sub_item_id ?: null,
            'part_number' => $this->new_part_number,
            'product_notes' => $this->new_product_notes,
        ]);

        $this->isCreating = false;
        $this->iteration++;
    }

    public function edit($id)
    {
        $p = Product::findOrFail($id);
        $this->editingId = $id;
        $this->edit_product_code = $p->product_code;
        $this->edit_product_name = $p->product_name;
        $this->edit_product_type_id = $p->product_type_id;
        $this->edit_invoicing_policy = $p->invoicing_policy;
        $this->edit_brand_id = $p->brand_id;
        $this->edit_uom_id = $p->uom_id;
        $this->edit_purchase_uom_id = $p->purchase_uom_id;
        $this->edit_item_category_id = $p->item_category_id;
        $this->edit_sub_item_id = $p->sub_item_id;
        $this->edit_part_number = $p->part_number;
        $this->edit_product_notes = $p->product_notes;

        $this->isCreating = false;
        $this->iteration++;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->iteration++;
    }

    public function saveEdit()
    {
        $this->validate([
            'edit_product_code' => 'required|string|unique:products,product_code,' . $this->editingId,
            'edit_product_name' => 'required|string|max:255',
            'edit_product_type_id' => 'required|exists:product_types,id',
            'edit_uom_id' => 'required|exists:uoms,id',
            'edit_purchase_uom_id' => 'required|exists:uoms,id',
            'edit_item_category_id' => 'required|exists:item_categories,id',
        ]);

        $p = Product::findOrFail($this->editingId);
        $p->update([
            'product_code' => $this->edit_product_code,
            'product_name' => $this->edit_product_name,
            'product_type_id' => $this->edit_product_type_id,
            'invoicing_policy' => $this->edit_invoicing_policy,
            'brand_id' => $this->edit_brand_id ?: null,
            'uom_id' => $this->edit_uom_id,
            'purchase_uom_id' => $this->edit_purchase_uom_id,
            'item_category_id' => $this->edit_item_category_id,
            'sub_item_id' => $this->edit_sub_item_id ?: null,
            'part_number' => $this->edit_part_number,
            'product_notes' => $this->edit_product_notes,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) Product::findOrFail($id)->delete();
    }
}
