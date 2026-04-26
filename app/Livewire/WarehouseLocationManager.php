<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WarehouseLocation;
use App\Models\Warehouse;
use App\Models\LocationCategory;

class WarehouseLocationManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    // Fields
    public $new_location_code = '';
    public $new_location_name = '';
    public $new_warehouse_id = '';
    public $new_location_type = '';
    public $new_is_parent = false;
    public $new_parent_location = '';
    public $new_is_negative_stock = false;
    public $new_is_scrap_location = false;
    public $new_is_return_location = false;
    public $new_is_replenish = false;
    public $new_inventory_frequency = null;
    public $new_location_note = '';
    public $new_is_active = true;

    public $edit_location_code = '';
    public $edit_location_name = '';
    public $edit_warehouse_id = '';
    public $edit_location_type = '';
    public $edit_is_parent = false;
    public $edit_parent_location = '';
    public $edit_is_negative_stock = false;
    public $edit_is_scrap_location = false;
    public $edit_is_return_location = false;
    public $edit_is_replenish = false;
    public $edit_inventory_frequency = null;
    public $edit_location_note = '';
    public $edit_is_active = true;

    public function render()
    {
        $locations = WarehouseLocation::with(['warehouse', 'category', 'parent'])->orderBy('location_code')->get();
        $warehouses = Warehouse::orderBy('warehouse_code')->get();
        $categories = LocationCategory::orderBy('location_name')->get();
        $parentLocations = WarehouseLocation::where('is_parent', true)->orderBy('location_name')->get();

        return view('livewire.warehouse-location-manager', [
            'locations' => $locations,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'parentLocations' => $parentLocations,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetNewFields();
        $this->new_location_code = $this->generateLocationCode();
        $this->iteration++;
    }

    private function generateLocationCode()
    {
        $locs = WarehouseLocation::where('location_code', 'like', 'LOC-%')->get();
        if ($locs->isEmpty()) {
            return 'LOC-001';
        }

        $maxNum = 0;
        foreach ($locs as $loc) {
            $num = (int) str_replace('LOC-', '', $loc->location_code);
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }

        return 'LOC-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function resetNewFields()
    {
        $this->new_location_code = '';
        $this->new_location_name = '';
        $this->new_warehouse_id = '';
        $this->new_location_type = '';
        $this->new_is_parent = false;
        $this->new_parent_location = '';
        $this->new_is_negative_stock = false;
        $this->new_is_scrap_location = false;
        $this->new_is_return_location = false;
        $this->new_is_replenish = false;
        $this->new_inventory_frequency = null;
        $this->new_location_note = '';
        $this->new_is_active = true;
    }

    public function cancelNew()
    {
        $this->isCreating = false;
        $this->resetNewFields();
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'new_location_code' => 'required|string|max:255|unique:warehouse_locations,location_code',
            'new_location_name' => 'required|string|max:255',
            'new_warehouse_id' => 'required|uuid|exists:warehouses,id',
            'new_location_type' => 'required|uuid|exists:location_categories,id',
            'new_parent_location' => 'nullable|uuid|exists:warehouse_locations,id',
            'new_inventory_frequency' => 'nullable|integer',
        ]);

        WarehouseLocation::create([
            'location_code' => $this->new_location_code,
            'location_name' => $this->new_location_name,
            'warehouse_id' => $this->new_warehouse_id,
            'location_type' => $this->new_location_type,
            'is_parent' => (bool)$this->new_is_parent,
            'parent_location' => $this->new_parent_location ?: null,
            'is_negative_stock' => (bool)$this->new_is_negative_stock,
            'is_scrap_location' => (bool)$this->new_is_scrap_location,
            'is_return_location' => (bool)$this->new_is_return_location,
            'is_replenish' => (bool)$this->new_is_replenish,
            'inventory_frequency' => $this->new_inventory_frequency ?: null,
            'location_note' => $this->new_location_note,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->resetNewFields();
        $this->iteration++;
    }

    public function edit($id)
    {
        $loc = WarehouseLocation::findOrFail($id);
        $this->editingId = $id;

        $this->edit_location_code = $loc->location_code;
        $this->edit_location_name = $loc->location_name;
        $this->edit_warehouse_id = $loc->warehouse_id;
        $this->edit_location_type = $loc->location_type;
        $this->edit_is_parent = $loc->is_parent;
        $this->edit_parent_location = $loc->parent_location;
        $this->edit_is_negative_stock = $loc->is_negative_stock;
        $this->edit_is_scrap_location = $loc->is_scrap_location;
        $this->edit_is_return_location = $loc->is_return_location;
        $this->edit_is_replenish = $loc->is_replenish;
        $this->edit_inventory_frequency = $loc->inventory_frequency;
        $this->edit_location_note = $loc->location_note;
        $this->edit_is_active = $loc->is_active;

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
            'edit_location_code' => 'required|string|max:255|unique:warehouse_locations,location_code,' . $this->editingId,
            'edit_location_name' => 'required|string|max:255',
            'edit_warehouse_id' => 'required|uuid|exists:warehouses,id',
            'edit_location_type' => 'required|uuid|exists:location_categories,id',
            'edit_parent_location' => 'nullable|uuid|exists:warehouse_locations,id',
            'edit_inventory_frequency' => 'nullable|integer',
        ]);

        $loc = WarehouseLocation::findOrFail($this->editingId);
        $loc->update([
            'location_code' => $this->edit_location_code,
            'location_name' => $this->edit_location_name,
            'warehouse_id' => $this->edit_warehouse_id,
            'location_type' => $this->edit_location_type,
            'is_parent' => (bool)$this->edit_is_parent,
            'parent_location' => $this->edit_parent_location ?: null,
            'is_negative_stock' => (bool)$this->edit_is_negative_stock,
            'is_scrap_location' => (bool)$this->edit_is_scrap_location,
            'is_return_location' => (bool)$this->edit_is_return_location,
            'is_replenish' => (bool)$this->edit_is_replenish,
            'inventory_frequency' => $this->edit_inventory_frequency ?: null,
            'location_note' => $this->edit_location_note,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            WarehouseLocation::findOrFail($id)->delete();
        }
    }
}
