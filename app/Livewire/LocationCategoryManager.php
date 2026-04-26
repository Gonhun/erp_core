<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LocationCategory;

class LocationCategoryManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    public $new_location_name = '';
    public $edit_location_name = '';

    public function render()
    {
        $categories = LocationCategory::orderBy('location_name')->get();
        return view('livewire.location-category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->new_location_name = '';
        $this->iteration++;
    }

    public function cancelNew()
    {
        $this->isCreating = false;
        $this->new_location_name = '';
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'new_location_name' => 'required|string|max:255',
        ]);

        LocationCategory::create([
            'location_name' => $this->new_location_name,
        ]);

        $this->isCreating = false;
        $this->new_location_name = '';
        $this->iteration++;
    }

    public function edit($id)
    {
        $cat = LocationCategory::findOrFail($id);
        $this->editingId = $id;
        $this->edit_location_name = $cat->location_name;

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
            'edit_location_name' => 'required|string|max:255',
        ]);

        $cat = LocationCategory::findOrFail($this->editingId);
        $cat->update([
            'location_name' => $this->edit_location_name,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            LocationCategory::findOrFail($id)->delete();
        }
    }
}
