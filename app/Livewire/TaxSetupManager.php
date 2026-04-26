<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tax;
use App\Models\TaxInvoiceDefinition;
use App\Models\TaxRefundDefinition;
use App\Models\TaxSetup;
use App\Models\Account;

class TaxSetupManager extends Component
{
    public Tax $tax;
    public $activeTab = 'definition';

    // Data collections
    public $accounts = [];
    
    // --- INVOICE DEFINITION STATE ---
    public $invIteration = 0;
    public $invIsCreating = false;
    public $invEditingId = null;
    
    public $new_inv_account_id = '';
    public $new_inv_def_amount = 0;
    
    public $edit_inv_account_id = '';
    public $edit_inv_def_amount = 0;

    // --- REFUND DEFINITION STATE ---
    public $refIteration = 0;
    public $refIsCreating = false;
    public $refEditingId = null;
    
    public $new_ref_account_id = '';
    public $new_ref_def_amount = 0;
    
    public $edit_ref_account_id = '';
    public $edit_ref_def_amount = 0;

    // --- SETUP STATE ---
    public $setupIteration = 0;
    public $setupIsCreating = false;
    public $setupEditingId = null;
    
    public $new_setup_tax_label = '';
    public $new_setup_tax_country = '';
    public $new_setup_is_included_price = false;
    
    public $edit_setup_tax_label = '';
    public $edit_setup_tax_country = '';
    public $edit_setup_is_included_price = false;

    public function mount(Tax $tax)
    {
        $this->tax = $tax;
        $this->accounts = Account::orderBy('account_code')->get();
    }

    public function render()
    {
        return view('livewire.tax-setup-manager')->layout('layouts.app');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // ==========================================
    // INVOICE DEFINITIONS
    // ==========================================
    public function createNewInv()
    {
        $this->invIsCreating = true;
        $this->resetNewInvFields();
        $this->invEditingId = null;
        $this->invIteration++;
    }

    public function resetNewInvFields()
    {
        $this->new_inv_account_id = '';
        $this->new_inv_def_amount = 0;
    }

    public function cancelNewInv()
    {
        $this->invIsCreating = false;
        $this->resetNewInvFields();
        $this->resetValidation();
        $this->invIteration++;
    }

    public function saveNewInv()
    {
        $this->validate([
            'new_inv_account_id' => 'required|exists:accounts,id',
            'new_inv_def_amount' => 'required|numeric',
        ]);

        TaxInvoiceDefinition::create([
            'tax_id' => $this->tax->id,
            'account_id' => $this->new_inv_account_id,
            'def_amount' => (float) $this->new_inv_def_amount,
        ]);

        $this->invIsCreating = false;
        $this->resetNewInvFields();
        $this->invIteration++;
    }

    public function editInv($id)
    {
        $def = TaxInvoiceDefinition::findOrFail($id);
        $this->invEditingId = $id;
        $this->edit_inv_account_id = $def->account_id;
        $this->edit_inv_def_amount = $def->def_amount;

        $this->invIsCreating = false;
        $this->resetValidation();
        $this->invIteration++;
    }

    public function cancelEditInv()
    {
        $this->invEditingId = null;
        $this->resetValidation();
        $this->invIteration++;
    }

    public function saveEditInv()
    {
        $this->validate([
            'edit_inv_account_id' => 'required|exists:accounts,id',
            'edit_inv_def_amount' => 'required|numeric',
        ]);

        $def = TaxInvoiceDefinition::findOrFail($this->invEditingId);
        $def->update([
            'account_id' => $this->edit_inv_account_id,
            'def_amount' => (float) $this->edit_inv_def_amount,
        ]);

        $this->invEditingId = null;
        $this->invIteration++;
    }

    public function deleteInv($id)
    {
        $this->invIteration++;
        if ($id) {
            TaxInvoiceDefinition::findOrFail($id)->delete();
        }
    }

    // ==========================================
    // REFUND DEFINITIONS
    // ==========================================
    public function createNewRef()
    {
        $this->refIsCreating = true;
        $this->resetNewRefFields();
        $this->refEditingId = null;
        $this->refIteration++;
    }

    public function resetNewRefFields()
    {
        $this->new_ref_account_id = '';
        $this->new_ref_def_amount = 0;
    }

    public function cancelNewRef()
    {
        $this->refIsCreating = false;
        $this->resetNewRefFields();
        $this->resetValidation();
        $this->refIteration++;
    }

    public function saveNewRef()
    {
        $this->validate([
            'new_ref_account_id' => 'required|exists:accounts,id',
            'new_ref_def_amount' => 'required|numeric',
        ]);

        TaxRefundDefinition::create([
            'tax_id' => $this->tax->id,
            'account_id' => $this->new_ref_account_id,
            'def_amount' => (float) $this->new_ref_def_amount,
        ]);

        $this->refIsCreating = false;
        $this->resetNewRefFields();
        $this->refIteration++;
    }

    public function editRef($id)
    {
        $def = TaxRefundDefinition::findOrFail($id);
        $this->refEditingId = $id;
        $this->edit_ref_account_id = $def->account_id;
        $this->edit_ref_def_amount = $def->def_amount;

        $this->refIsCreating = false;
        $this->resetValidation();
        $this->refIteration++;
    }

    public function cancelEditRef()
    {
        $this->refEditingId = null;
        $this->resetValidation();
        $this->refIteration++;
    }

    public function saveEditRef()
    {
        $this->validate([
            'edit_ref_account_id' => 'required|exists:accounts,id',
            'edit_ref_def_amount' => 'required|numeric',
        ]);

        $def = TaxRefundDefinition::findOrFail($this->refEditingId);
        $def->update([
            'account_id' => $this->edit_ref_account_id,
            'def_amount' => (float) $this->edit_ref_def_amount,
        ]);

        $this->refEditingId = null;
        $this->refIteration++;
    }

    public function deleteRef($id)
    {
        $this->refIteration++;
        if ($id) {
            TaxRefundDefinition::findOrFail($id)->delete();
        }
    }

    // ==========================================
    // TAX SETUP
    // ==========================================
    public function createNewSetup()
    {
        $this->setupIsCreating = true;
        $this->resetNewSetupFields();
        $this->setupEditingId = null;
        $this->setupIteration++;
    }

    public function resetNewSetupFields()
    {
        $this->new_setup_tax_label = '';
        $this->new_setup_tax_country = '';
        $this->new_setup_is_included_price = false;
    }

    public function cancelNewSetup()
    {
        $this->setupIsCreating = false;
        $this->resetNewSetupFields();
        $this->resetValidation();
        $this->setupIteration++;
    }

    public function saveNewSetup()
    {
        $this->validate([
            'new_setup_tax_label' => 'required|string|max:255',
            'new_setup_tax_country' => 'nullable|string|max:255',
        ]);

        TaxSetup::create([
            'tax_id' => $this->tax->id,
            'tax_label' => $this->new_setup_tax_label,
            'tax_country' => $this->new_setup_tax_country ?: null,
            'is_included_price' => (bool) $this->new_setup_is_included_price,
        ]);

        $this->setupIsCreating = false;
        $this->resetNewSetupFields();
        $this->setupIteration++;
    }

    public function editSetup($id)
    {
        $setup = TaxSetup::findOrFail($id);
        $this->setupEditingId = $id;
        $this->edit_setup_tax_label = $setup->tax_label;
        $this->edit_setup_tax_country = $setup->tax_country;
        $this->edit_setup_is_included_price = $setup->is_included_price;

        $this->setupIsCreating = false;
        $this->resetValidation();
        $this->setupIteration++;
    }

    public function cancelEditSetup()
    {
        $this->setupEditingId = null;
        $this->resetValidation();
        $this->setupIteration++;
    }

    public function saveEditSetup()
    {
        $this->validate([
            'edit_setup_tax_label' => 'required|string|max:255',
            'edit_setup_tax_country' => 'nullable|string|max:255',
        ]);

        $setup = TaxSetup::findOrFail($this->setupEditingId);
        $setup->update([
            'tax_label' => $this->edit_setup_tax_label,
            'tax_country' => $this->edit_setup_tax_country ?: null,
            'is_included_price' => (bool) $this->edit_setup_is_included_price,
        ]);

        $this->setupEditingId = null;
        $this->setupIteration++;
    }

    public function deleteSetup($id)
    {
        $this->setupIteration++;
        if ($id) {
            TaxSetup::findOrFail($id)->delete();
        }
    }
}
