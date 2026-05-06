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
    public $selectedCategoryId = null; // Filter by category

    // Fields
    public $uom_name = '';
    public $uom_category_id = '';
    public $uom_type = 'Reference';
    public $ratio = 1.0000;
    public $is_active = true;

    public function render()
    {
        $categories = UomCategory::with(['uoms' => function($query) {
            $query->orderBy('uom_name');
        }])->orderBy('category_name')->get();

        return view('livewire.uom-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function createNew($categoryId = null)
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetFields();
        if ($categoryId) {
            $this->uom_category_id = $categoryId;
        }
        $this->iteration++;
    }

    private function resetFields()
    {
        $this->uom_name = '';
        $this->uom_category_id = '';
        $this->uom_type = 'Reference';
        $this->ratio = 1.0000;
        $this->is_active = true;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->editingId = null;
        $this->resetFields();
        $this->iteration++;
    }

    public function save()
    {
        $this->validate([
            'uom_name' => 'required|string|max:255|unique:uoms,uom_name,' . ($this->editingId ?: 'NULL') . ',id',
            'uom_category_id' => 'required|exists:uom_categories,id',
            'uom_type' => 'required|string',
            'ratio' => 'required|numeric',
        ]);

        $data = [
            'uom_name' => $this->uom_name,
            'uom_category_id' => $this->uom_category_id,
            'uom_type' => $this->uom_type,
            'ratio' => $this->ratio,
            'is_active' => (bool)$this->is_active,
        ];

        if ($this->editingId) {
            Uom::findOrFail($this->editingId)->update($data);
        } else {
            Uom::create($data);
        }

        $this->isCreating = false;
        $this->editingId = null;
        $this->iteration++;
    }

    public function edit($id)
    {
        $uom = Uom::findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;
        
        $this->uom_name = $uom->uom_name;
        $this->uom_category_id = $uom->uom_category_id;
        $this->uom_type = $uom->uom_type;
        $this->ratio = $uom->ratio;
        $this->is_active = $uom->is_active;

        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) Uom::findOrFail($id)->delete();
    }
}
