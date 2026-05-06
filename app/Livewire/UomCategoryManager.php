<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UomCategory;
use App\Models\Uom;
use Illuminate\Support\Facades\DB;

class UomCategoryManager extends Component
{
    public $iteration = 0;
    
    // Create state
    public $isCreating = false;
    public $new_base_uom = '';
    public $new_base_uom_name = '';
    public $new_is_active = true;

    // Edit state
    public $editingId = null;
    public $edit_base_uom = '';
    public $edit_base_uom_name = '';
    public $edit_is_active = true;

    public function render()
    {
        $categories = UomCategory::orderBy('base_uom')->get();
        return view('livewire.uom-category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_base_uom = '';
        $this->new_base_uom_name = '';
        $this->new_is_active = true;
        $this->iteration++;
    }

    public function cancelCreate()
    {
        $this->isCreating = false;
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'new_base_uom' => 'required|string|max:50|unique:uom_categories,base_uom',
            'new_base_uom_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () {
            $category = UomCategory::create([
                'category_name' => $this->new_base_uom_name,
                'base_uom_name' => $this->new_base_uom_name,
                'base_uom' => $this->new_base_uom,
                'is_active' => (bool)$this->new_is_active,
            ]);
            
            Uom::create([
                'uom_category_id' => $category->id,
                'uom_name' => $this->new_base_uom,
                'uom_type' => 'Reference',
                'ratio' => 1.0000,
                'is_active' => true,
            ]);
        });

        $this->isCreating = false;
        $this->iteration++;
        session()->flash('message', 'Unit of Measure created.');
    }

    public function edit($id)
    {
        $cat = UomCategory::findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;
        $this->edit_base_uom = $cat->base_uom;
        $this->edit_base_uom_name = $cat->base_uom_name;
        $this->edit_is_active = $cat->is_active;
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
            'edit_base_uom' => 'required|string|max:50|unique:uom_categories,base_uom,' . $this->editingId,
            'edit_base_uom_name' => 'required|string|max:255',
        ]);

        $cat = UomCategory::findOrFail($this->editingId);
        $cat->update([
            'category_name' => $this->edit_base_uom_name,
            'base_uom_name' => $this->edit_base_uom_name,
            'base_uom' => $this->edit_base_uom,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
        session()->flash('message', 'Unit of Measure updated.');
    }

    public function delete($id)
    {
        if ($id) {
            UomCategory::findOrFail($id)->delete();
            $this->iteration++;
            session()->flash('message', 'Unit of Measure deleted.');
        }
    }
}
