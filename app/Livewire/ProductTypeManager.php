<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductType;

class ProductTypeManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    public $new_type_name = '';
    public $new_is_active = true;

    public $edit_type_name = '';
    public $edit_is_active = true;

    public function render()
    {
        $types = ProductType::orderBy('product_type_name')->get();
        return view('livewire.product-type-manager', [
            'types' => $types,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_type_name = '';
        $this->new_is_active = true;
        $this->iteration++;
    }

    public function cancelNew()
    {
        $this->isCreating = false;
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'new_type_name' => 'required|string|max:255|unique:product_types,product_type_name',
        ]);

        ProductType::create([
            'product_type_name' => $this->new_type_name,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->iteration++;
    }

    public function edit($id)
    {
        $type = ProductType::findOrFail($id);
        $this->editingId = $id;
        $this->edit_type_name = $type->product_type_name;
        $this->edit_is_active = $type->is_active;

        $this->isCreating = false;
        $this->resetValidation();
        $this->iteration++;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveEdit()
    {
        $this->validate([
            'edit_type_name' => 'required|string|max:255|unique:product_types,product_type_name,' . $this->editingId,
        ]);

        $type = ProductType::findOrFail($this->editingId);
        $type->update([
            'product_type_name' => $this->edit_type_name,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            ProductType::findOrFail($id)->delete();
        }
    }
}
