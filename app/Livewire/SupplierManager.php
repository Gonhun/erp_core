<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\SupplierAccounting;
use App\Models\SupplierBankAccount;
use App\Models\SupplierContact;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class SupplierManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;
    public $activeTab = 'general'; // general, accounting, tax, bank, contacts

    // Supplier Fields
    public $supplier_code = '';
    public $supplier_name = '';
    public $supplier_address = '';
    public $supplier_region = '';
    public $supplier_state = '';
    public $supplier_postal_code = '';
    public $supplier_phone = '';
    public $supplier_email = '';
    public $supplier_website = '';
    
    // Tax Info
    public $supplier_npwp = '';
    public $supplier_nik = '';
    public $tax_name = '';
    public $tax_address = '';
    public $is_pkp = false;
    public $is_ppn = false;
    public $is_individual = false;
    public $is_active = true;

    // Accounting
    public $account_receivable = '';
    public $account_payable = '';

    // Details arrays
    public $bank_accounts = [];
    public $contacts = [];

    public function render()
    {
        $suppliers = Supplier::orderBy('supplier_code')->get();
        $accounts = Account::orderBy('account_code')->get();

        return view('livewire.supplier-manager', [
            'suppliers' => $suppliers,
            'accounts' => $accounts,
        ])->layout('layouts.app');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetFields();
        $this->activeTab = 'general';
        $this->iteration++;
    }

    private function resetFields()
    {
        $this->supplier_code = '';
        $this->supplier_name = '';
        $this->supplier_address = '';
        $this->supplier_region = '';
        $this->supplier_state = '';
        $this->supplier_postal_code = '';
        $this->supplier_phone = '';
        $this->supplier_email = '';
        $this->supplier_website = '';
        $this->supplier_npwp = '';
        $this->supplier_nik = '';
        $this->tax_name = '';
        $this->tax_address = '';
        $this->is_pkp = false;
        $this->is_ppn = false;
        $this->is_individual = false;
        $this->is_active = true;
        $this->account_receivable = '';
        $this->account_payable = '';
        $this->bank_accounts = [];
        $this->contacts = [];
    }

    public function addBankRow()
    {
        $this->bank_accounts[] = [
            'bank' => '',
            'bank_account_name' => '',
            'bank_account_number' => '',
            'is_active' => true
        ];
    }

    public function removeBankRow($index)
    {
        unset($this->bank_accounts[$index]);
        $this->bank_accounts = array_values($this->bank_accounts);
    }

    public function addContactRow()
    {
        $this->contacts[] = [
            'contact_type' => 'Contact Person',
            'contact_name' => '',
            'supplier_contact_address' => '',
            'supplier_contact_region' => '',
            'supplier_contact_state' => '',
            'supplier_contact_postal_code' => '',
            'supplier_contact_email' => '',
            'supplier_contact_phone' => '',
            'supplier_contact_mobile' => '',
            'is_active' => true
        ];
    }

    public function removeContactRow($index)
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts);
    }

    public function save()
    {
        if (!$this->editingId && empty($this->supplier_code)) {
            $lastSupplier = Supplier::orderBy('supplier_code', 'desc')->first();
            $lastNumber = $lastSupplier ? intval(str_replace('SUP-', '', $lastSupplier->supplier_code)) : 0;
            $this->supplier_code = 'SUP-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        $this->validate([
            'supplier_code' => 'required|string|unique:suppliers,supplier_code,' . ($this->editingId ?: 'NULL') . ',id',
            'supplier_name' => 'required|string|max:255',
            'account_receivable' => 'required|exists:accounts,id',
            'account_payable' => 'required|exists:accounts,id',
            'supplier_npwp' => 'nullable|string|unique:suppliers,supplier_npwp,' . ($this->editingId ?: 'NULL') . ',id',
            'supplier_email' => 'nullable|email',
        ]);

        DB::transaction(function () {
            $data = [
                'supplier_code' => $this->supplier_code,
                'supplier_name' => $this->supplier_name,
                'supplier_address' => $this->supplier_address,
                'supplier_region' => $this->supplier_region,
                'supplier_state' => $this->supplier_state,
                'supplier_postal_code' => $this->supplier_postal_code,
                'supplier_phone' => $this->supplier_phone,
                'supplier_email' => $this->supplier_email,
                'supplier_website' => $this->supplier_website,
                'supplier_npwp' => $this->supplier_npwp,
                'supplier_nik' => $this->supplier_nik,
                'tax_name' => $this->tax_name,
                'tax_address' => $this->tax_address,
                'is_pkp' => (bool)$this->is_pkp,
                'is_ppn' => (bool)$this->is_ppn,
                'is_individual' => (bool)$this->is_individual,
                'is_active' => (bool)$this->is_active,
            ];

            if ($this->editingId) {
                $supplier = Supplier::findOrFail($this->editingId);
                $supplier->update($data);
            } else {
                $supplier = Supplier::create($data);
            }

            // Sync Accounting
            SupplierAccounting::updateOrCreate(
                ['supplier_id' => $supplier->id],
                [
                    'account_receivable' => $this->account_receivable,
                    'account_payable' => $this->account_payable,
                ]
            );

            // Sync Bank Accounts
            SupplierBankAccount::where('supplier_id', $supplier->id)->delete();
            foreach ($this->bank_accounts as $bank) {
                SupplierBankAccount::create(array_merge($bank, ['supplier_id' => $supplier->id]));
            }

            // Sync Contacts
            SupplierContact::where('supplier_id', $supplier->id)->delete();
            foreach ($this->contacts as $contact) {
                SupplierContact::create(array_merge($contact, ['supplier_id' => $supplier->id]));
            }
        });

        $this->isCreating = false;
        $this->editingId = null;
        $this->iteration++;
        session()->flash('message', 'Supplier saved successfully.');
    }

    public function edit($id)
    {
        $s = Supplier::with(['accounting', 'bankAccounts', 'contacts'])->findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;
        
        $this->supplier_code = $s->supplier_code;
        $this->supplier_name = $s->supplier_name;
        $this->supplier_address = $s->supplier_address;
        $this->supplier_region = $s->supplier_region;
        $this->supplier_state = $s->supplier_state;
        $this->supplier_postal_code = $s->supplier_postal_code;
        $this->supplier_phone = $s->supplier_phone;
        $this->supplier_email = $s->supplier_email;
        $this->supplier_website = $s->supplier_website;
        $this->supplier_npwp = $s->supplier_npwp;
        $this->supplier_nik = $s->supplier_nik;
        $this->tax_name = $s->tax_name;
        $this->tax_address = $s->tax_address;
        $this->is_pkp = $s->is_pkp;
        $this->is_ppn = $s->is_ppn;
        $this->is_individual = $s->is_individual;
        $this->is_active = $s->is_active;

        if ($s->accounting) {
            $this->account_receivable = $s->accounting->account_receivable;
            $this->account_payable = $s->accounting->account_payable;
        }

        $this->bank_accounts = $s->bankAccounts->map(fn($b) => [
            'bank' => $b->bank,
            'bank_account_name' => $b->bank_account_name,
            'bank_account_number' => $b->bank_account_number,
            'is_active' => $b->is_active
        ])->toArray();

        $this->contacts = $s->contacts->map(fn($c) => [
            'contact_type' => $c->contact_type,
            'contact_name' => $c->contact_name,
            'supplier_contact_address' => $c->supplier_contact_address,
            'supplier_contact_region' => $c->supplier_contact_region,
            'supplier_contact_state' => $c->supplier_contact_state,
            'supplier_contact_postal_code' => $c->supplier_contact_postal_code,
            'supplier_contact_email' => $c->supplier_contact_email,
            'supplier_contact_phone' => $c->supplier_contact_phone,
            'supplier_contact_mobile' => $c->supplier_contact_mobile,
            'is_active' => $c->is_active
        ])->toArray();

        $this->activeTab = 'general';
        $this->iteration++;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->editingId = null;
        $this->resetFields();
        $this->iteration++;
    }

    public function delete($id)
    {
        if ($id) {
            Supplier::findOrFail($id)->delete();
            $this->iteration++;
            session()->flash('message', 'Supplier deleted.');
        }
    }
}
