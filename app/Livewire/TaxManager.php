<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tax;

class TaxManager extends Component
{
    public $taxes;
    public $iteration = 0;

    // Inline Create State
    public $isCreating = false;
    public $new_tax_name = '';
    public $new_tax_computation = '';
    public $new_tax_type = '';
    public $new_tax_scope = '';
    public $new_tax_amount = 0;
    public $new_is_ppn = false;
    public $new_is_active = true;

    // Inline Edit State
    public $editingId = null;
    public $edit_tax_name = '';
    public $edit_tax_computation = '';
    public $edit_tax_type = '';
    public $edit_tax_scope = '';
    public $edit_tax_amount = 0;
    public $edit_is_ppn = false;
    public $edit_is_active = true;

    public function render()
    {
        $this->taxes = Tax::orderBy('tax_name', 'asc')->get();
        return view('livewire.tax-manager')->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->resetNewFields();
        $this->editingId = null;
        $this->iteration++;
    }

    public function resetNewFields()
    {
        $this->new_tax_name = '';
        $this->new_tax_computation = '';
        $this->new_tax_type = '';
        $this->new_tax_scope = '';
        $this->new_tax_amount = 0;
        $this->new_is_ppn = false;
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
            'new_tax_name' => 'required|string|max:255',
            'new_tax_computation' => 'nullable|integer',
            'new_tax_type' => 'nullable|string|max:255',
            'new_tax_scope' => 'nullable|string|max:12',
            'new_tax_amount' => 'required|numeric',
        ]);

        Tax::create([
            'tax_name' => $this->new_tax_name,
            'tax_computation' => $this->new_tax_computation !== '' ? $this->new_tax_computation : null,
            'tax_type' => $this->new_tax_type ?: null,
            'tax_scope' => $this->new_tax_scope ?: null,
            'tax_amount' => (float) $this->new_tax_amount,
            'is_ppn' => (bool) $this->new_is_ppn,
            'is_active' => (bool) $this->new_is_active,
        ]);

        $this->isCreating = false;
        $this->resetNewFields();
        $this->iteration++;
    }

    public function edit($id)
    {
        $tax = Tax::findOrFail($id);
        $this->editingId = $id;
        $this->edit_tax_name = $tax->tax_name;
        $this->edit_tax_computation = $tax->tax_computation;
        $this->edit_tax_type = $tax->tax_type;
        $this->edit_tax_scope = $tax->tax_scope;
        $this->edit_tax_amount = $tax->tax_amount;
        $this->edit_is_ppn = $tax->is_ppn;
        $this->edit_is_active = $tax->is_active;

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
            'edit_tax_name' => 'required|string|max:255',
            'edit_tax_computation' => 'nullable|integer',
            'edit_tax_type' => 'nullable|string|max:255',
            'edit_tax_scope' => 'nullable|string|max:12',
            'edit_tax_amount' => 'required|numeric',
        ]);

        $tax = Tax::findOrFail($this->editingId);
        $tax->update([
            'tax_name' => $this->edit_tax_name,
            'tax_computation' => $this->edit_tax_computation !== '' ? $this->edit_tax_computation : null,
            'tax_type' => $this->edit_tax_type ?: null,
            'tax_scope' => $this->edit_tax_scope ?: null,
            'tax_amount' => (float) $this->edit_tax_amount,
            'is_ppn' => (bool) $this->edit_is_ppn,
            'is_active' => (bool) $this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            Tax::findOrFail($id)->delete();
        }
    }
}
