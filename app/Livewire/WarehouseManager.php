<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Warehouse;

class WarehouseManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;

    // Create state
    public $new_warehouse_code = '';
    public $new_warehouse_name = '';
    public $new_warehouse_short_name = '';
    public $new_warehouse_address = '';
    public $new_shipment_type = '';
    public $new_is_buy_resupply = false;
    public $new_is_active = true;

    // Edit state
    public $edit_warehouse_code = '';
    public $edit_warehouse_name = '';
    public $edit_warehouse_short_name = '';
    public $edit_warehouse_address = '';
    public $edit_shipment_type = '';
    public $edit_is_buy_resupply = false;
    public $edit_is_active = true;

    public function render()
    {
        $warehouses = Warehouse::orderBy('warehouse_code')->get();
        return view('livewire.warehouse-manager', [
            'warehouses' => $warehouses,
        ])->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetNewFields();
        $this->new_warehouse_code = $this->generateWarehouseCode();
        $this->iteration++;
    }

    private function generateWarehouseCode()
    {
        $warehouses = Warehouse::where('warehouse_code', 'like', 'WH-%')->get();
        if ($warehouses->isEmpty()) {
            return 'WH-001';
        }

        $maxNum = 0;
        foreach ($warehouses as $wh) {
            $num = (int) str_replace('WH-', '', $wh->warehouse_code);
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }

        return 'WH-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }

    private function resetNewFields()
    {
        $this->new_warehouse_code = '';
        $this->new_warehouse_name = '';
        $this->new_warehouse_short_name = '';
        $this->new_warehouse_address = '';
        $this->new_shipment_type = '';
        $this->new_is_buy_resupply = false;
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
            'new_warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code',
            'new_warehouse_name' => 'required|string|max:255',
            'new_warehouse_short_name' => 'nullable|string|max:15',
            'new_warehouse_address' => 'nullable|string',
            'new_shipment_type' => 'required|string|max:6',
        ]);

        Warehouse::create([
            'warehouse_code' => $this->new_warehouse_code,
            'warehouse_name' => $this->new_warehouse_name,
            'warehouse_short_name' => $this->new_warehouse_short_name,
            'warehouse_address' => $this->new_warehouse_address,
            'shipment_type' => $this->new_shipment_type,
            'is_buy_resupply' => (bool)$this->new_is_buy_resupply,
            'is_active' => (bool)$this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->resetNewFields();
        $this->iteration++;
    }

    public function edit($id)
    {
        $wh = Warehouse::findOrFail($id);
        $this->editingId = $id;
        
        $this->edit_warehouse_code = $wh->warehouse_code;
        $this->edit_warehouse_name = $wh->warehouse_name;
        $this->edit_warehouse_short_name = $wh->warehouse_short_name;
        $this->edit_warehouse_address = $wh->warehouse_address;
        $this->edit_shipment_type = $wh->shipment_type;
        $this->edit_is_buy_resupply = $wh->is_buy_resupply;
        $this->edit_is_active = $wh->is_active;

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
            'edit_warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code,' . $this->editingId,
            'edit_warehouse_name' => 'required|string|max:255',
            'edit_warehouse_short_name' => 'nullable|string|max:15',
            'edit_warehouse_address' => 'nullable|string',
            'edit_shipment_type' => 'required|string|max:6',
        ]);

        $wh = Warehouse::findOrFail($this->editingId);
        $wh->update([
            'warehouse_code' => $this->edit_warehouse_code,
            'warehouse_name' => $this->edit_warehouse_name,
            'warehouse_short_name' => $this->edit_warehouse_short_name,
            'warehouse_address' => $this->edit_warehouse_address,
            'shipment_type' => $this->edit_shipment_type,
            'is_buy_resupply' => (bool)$this->edit_is_buy_resupply,
            'is_active' => (bool)$this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            Warehouse::findOrFail($id)->delete();
        }
    }
}
