<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Uom;
use App\Models\UomCategory;

class UomManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    public $new_uom_name = '';
    public $new_uom_category_id = '';
    public $new_uom_type = 'Reference';
    public $new_ratio = 1.0000;
    public $new_is_active = true;

    public $edit_uom_name = '';
    public $edit_uom_category_id = '';
    public $edit_uom_type = '';
    public $edit_ratio = 0;
    public $edit_is_active = true;

    public function render()
    {
        $uoms = Uom::with('category')->orderBy('uom_name')->get();
        $categories = UomCategory::orderBy('category_name')->get();

        return view('livewire.uom-manager', [
            'uoms' => $uoms,
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_uom_name = '';
        $this->new_uom_category_id = '';
        $this->new_uom_type = 'Reference';
        $this->new_ratio = 1.0000;
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
            'new_uom_name' => 'required|string|max:255|unique:uoms,uom_name',
            'new_uom_category_id' => 'required|exists:uom_categories,id',
            'new_uom_type' => 'required|string',
            'new_ratio' => 'required|numeric',
        ]);

        Uom::create([
            'uom_name' => $this->new_uom_name,
            'uom_category_id' => $this->new_uom_category_id,
            'uom_type' => $this->new_uom_type,
            'ratio' => $this->new_ratio,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->iteration++;
    }

    public function edit($id)
    {
        $uom = Uom::findOrFail($id);
        $this->editingId = $id;
        $this->edit_uom_name = $uom->uom_name;
        $this->edit_uom_category_id = $uom->uom_category_id;
        $this->edit_uom_type = $uom->uom_type;
        $this->edit_ratio = $uom->ratio;
        $this->edit_is_active = $uom->is_active;

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
            'edit_uom_name' => 'required|string|max:255|unique:uoms,uom_name,' . $this->editingId,
            'edit_uom_category_id' => 'required|exists:uom_categories,id',
            'edit_uom_type' => 'required|string',
            'edit_ratio' => 'required|numeric',
        ]);

        $uom = Uom::findOrFail($this->editingId);
        $uom->update([
            'uom_name' => $this->edit_uom_name,
            'uom_category_id' => $this->edit_uom_category_id,
            'uom_type' => $this->edit_uom_type,
            'ratio' => $this->edit_ratio,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            Uom::findOrFail($id)->delete();
        }
    }
}
