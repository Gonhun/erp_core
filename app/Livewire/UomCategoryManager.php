<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UomCategory;

class UomCategoryManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    public $new_category_name = '';
    public $new_base_uom_name = '';
    public $new_base_uom = '';
    public $new_is_active = true;

    public $edit_category_name = '';
    public $edit_base_uom_name = '';
    public $edit_base_uom = '';
    public $edit_is_active = true;

    public function render()
    {
        $categories = UomCategory::orderBy('category_name')->get();
        return view('livewire.uom-category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_category_name = '';
        $this->new_base_uom_name = '';
        $this->new_base_uom = '';
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
            'new_category_name' => 'required|string|max:255|unique:uom_categories,category_name',
            'new_base_uom_name' => 'required|string|max:255',
            'new_base_uom' => 'required|string|max:50',
        ]);

        UomCategory::create([
            'category_name' => $this->new_category_name,
            'base_uom_name' => $this->new_base_uom_name,
            'base_uom' => $this->new_base_uom,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->iteration++;
    }

    public function edit($id)
    {
        $cat = UomCategory::findOrFail($id);
        $this->editingId = $id;
        $this->edit_category_name = $cat->category_name;
        $this->edit_base_uom_name = $cat->base_uom_name;
        $this->edit_base_uom = $cat->base_uom;
        $this->edit_is_active = $cat->is_active;

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
            'edit_category_name' => 'required|string|max:255|unique:uom_categories,category_name,' . $this->editingId,
            'edit_base_uom_name' => 'required|string|max:255',
            'edit_base_uom' => 'required|string|max:50',
        ]);

        $cat = UomCategory::findOrFail($this->editingId);
        $cat->update([
            'category_name' => $this->edit_category_name,
            'base_uom_name' => $this->edit_base_uom_name,
            'base_uom' => $this->edit_base_uom,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            UomCategory::findOrFail($id)->delete();
        }
    }
}
