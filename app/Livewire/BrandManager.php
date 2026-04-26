<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Brand;

class BrandManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    public $new_brand_name = '';
    public $new_is_active = true;

    public $edit_brand_name = '';
    public $edit_is_active = true;

    public function render()
    {
        $brands = Brand::orderBy('brand_name')->get();
        return view('livewire.brand-manager', [
            'brands' => $brands,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_brand_name = '';
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
            'new_brand_name' => 'required|string|max:255|unique:brands,brand_name',
        ]);

        Brand::create([
            'brand_name' => $this->new_brand_name,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->iteration++;
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        $this->editingId = $id;
        $this->edit_brand_name = $brand->brand_name;
        $this->edit_is_active = $brand->is_active;

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
            'edit_brand_name' => 'required|string|max:255|unique:brands,brand_name,' . $this->editingId,
        ]);

        $brand = Brand::findOrFail($this->editingId);
        $brand->update([
            'brand_name' => $this->edit_brand_name,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            Brand::findOrFail($id)->delete();
        }
    }
}
