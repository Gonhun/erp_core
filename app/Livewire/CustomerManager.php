<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\CustomerAccounting;
use App\Models\CustomerBankAccount;
use App\Models\CustomerContact;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class CustomerManager extends Component
{
    public $iteration = 0;
    public $isCreating = false;
    public $editingId = null;
    public $activeTab = 'general'; // general, tax, accounting, bank, contacts

    // Customer Fields
    public $customer_code = '';
    public $customer_name = '';
    public $customer_address = '';
    public $customer_region = '';
    public $customer_state = '';
    public $customer_postal_code = '';
    public $customer_npwp = '';
    public $customer_nik = '';
    public $tax_name = '';
    public $tax_address = '';
    public $customer_phone = '';
    public $customer_mobile = '';
    public $customer_email = '';
    public $customer_website = '';
    public $is_individual = false;
    public $is_company = false;
    public $is_ppn = false;
    public $is_pkp = false;
    public $is_active = true;
    public $credit_limit = 0;

    // Accounting Fields
    public $account_receivable = '';
    public $account_payable = '';

    // Bank Accounts Array
    public $bank_accounts = []; // ['bank', 'bank_account_name', 'bank_account_number', 'is_active']

    // Contacts Array
    public $contacts = []; // ['contact_type', 'address', 'region', 'state', 'postal_code', 'email', 'phone', 'mobile', 'is_active']

    public function render()
    {
        $customers = Customer::orderBy('customer_code')->get();
        $accounts = Account::where('is_active', true)->orderBy('account_code')->get();

        return view('livewire.customer-manager', [
            'customers' => $customers,
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
        $this->customer_code = $this->generateCustomerCode();
        $this->activeTab = 'general';
        $this->iteration++;
    }

    private function generateCustomerCode()
    {
        $last = Customer::orderBy('customer_code', 'desc')->first();
        if (!$last) return 'CUST-001';
        
        $num = (int) str_replace('CUST-', '', $last->customer_code);
        return 'CUST-' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }

    private function resetFields()
    {
        $this->customer_code = '';
        $this->customer_name = '';
        $this->customer_address = '';
        $this->customer_region = '';
        $this->customer_state = '';
        $this->customer_postal_code = '';
        $this->customer_npwp = '';
        $this->customer_nik = '';
        $this->tax_name = '';
        $this->tax_address = '';
        $this->customer_phone = '';
        $this->customer_mobile = '';
        $this->customer_email = '';
        $this->customer_website = '';
        $this->is_individual = false;
        $this->is_company = false;
        $this->is_ppn = false;
        $this->is_pkp = false;
        $this->is_active = true;
        $this->credit_limit = 0;

        $this->account_receivable = '';
        $this->account_payable = '';
        $this->bank_accounts = [];
        $this->contacts = [];
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->editingId = null;
        $this->resetFields();
        $this->iteration++;
    }

    // Bank Account Row Management
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

    // Contact Row Management
    public function addContactRow()
    {
        $this->contacts[] = [
            'contact_type' => 'Other',
            'customer_contact_address' => '',
            'customer_contact_region' => '',
            'customer_contact_state' => '',
            'customer_contact_postal_code' => '',
            'customer_contact_email' => '',
            'customer_contact_phone' => '',
            'customer_contact_mobile' => '',
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
        $this->validate([
            'customer_code' => 'required|string|unique:customers,customer_code,' . ($this->editingId ?: 'NULL') . ',id',
            'customer_name' => 'required|string|max:255',
            'customer_npwp' => 'nullable|string|unique:customers,customer_npwp,' . ($this->editingId ?: 'NULL') . ',id',
            'customer_nik' => 'nullable|string|max:16|unique:customers,customer_nik,' . ($this->editingId ?: 'NULL') . ',id',
            'account_receivable' => 'required|exists:accounts,id',
            'account_payable' => 'required|exists:accounts,id',
            'credit_limit' => 'nullable|numeric|min:0',
            'customer_email' => 'nullable|email|max:255',
        ]);

        DB::transaction(function () {
            $customerData = [
                'customer_code' => $this->customer_code,
                'customer_name' => $this->customer_name,
                'customer_address' => $this->customer_address,
                'customer_region' => $this->customer_region,
                'customer_state' => $this->customer_state,
                'customer_postal_code' => $this->customer_postal_code,
                'customer_npwp' => $this->customer_npwp,
                'customer_nik' => $this->customer_nik,
                'tax_name' => $this->tax_name,
                'tax_address' => $this->tax_address,
                'customer_phone' => $this->customer_phone,
                'customer_mobile' => $this->customer_mobile,
                'customer_email' => $this->customer_email,
                'customer_website' => $this->customer_website,
                'is_individual' => (bool)$this->is_individual,
                'is_company' => (bool)$this->is_company,
                'is_ppn' => (bool)$this->is_ppn,
                'is_pkp' => (bool)$this->is_pkp,
                'is_active' => (bool)$this->is_active,
                'credit_limit' => $this->credit_limit,
            ];

            if ($this->editingId) {
                $customer = Customer::findOrFail($this->editingId);
                $customer->update($customerData);
            } else {
                $customer = Customer::create($customerData);
            }

            // Accounting
            CustomerAccounting::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'account_receivable' => $this->account_receivable,
                    'account_payable' => $this->account_payable,
                ]
            );

            // Bank Accounts
            CustomerBankAccount::where('customer_id', $customer->id)->delete();
            foreach ($this->bank_accounts as $bank) {
                CustomerBankAccount::create(array_merge($bank, ['customer_id' => $customer->id]));
            }

            // Contacts
            CustomerContact::where('customer_id', $customer->id)->delete();
            foreach ($this->contacts as $contact) {
                CustomerContact::create(array_merge($contact, ['customer_id' => $customer->id]));
            }
        });

        $this->isCreating = false;
        $this->editingId = null;
        $this->iteration++;
    }

    public function edit($id)
    {
        $c = Customer::with(['accounting', 'bankAccounts', 'contacts'])->findOrFail($id);
        $this->editingId = $id;
        $this->isCreating = false;

        $this->customer_code = $c->customer_code;
        $this->customer_name = $c->customer_name;
        $this->customer_address = $c->customer_address;
        $this->customer_region = $c->customer_region;
        $this->customer_state = $c->customer_state;
        $this->customer_postal_code = $c->customer_postal_code;
        $this->customer_npwp = $c->customer_npwp;
        $this->customer_nik = $c->customer_nik;
        $this->tax_name = $c->tax_name;
        $this->tax_address = $c->tax_address;
        $this->customer_phone = $c->customer_phone;
        $this->customer_mobile = $c->customer_mobile;
        $this->customer_email = $c->customer_email;
        $this->customer_website = $c->customer_website;
        $this->is_individual = $c->is_individual;
        $this->is_company = $c->is_company;
        $this->is_ppn = $c->is_ppn;
        $this->is_pkp = $c->is_pkp;
        $this->is_active = $c->is_active;
        $this->credit_limit = $c->credit_limit;

        if ($c->accounting) {
            $this->account_receivable = $c->accounting->account_receivable;
            $this->account_payable = $c->accounting->account_payable;
        }

        $this->bank_accounts = $c->bankAccounts->map(fn($b) => [
            'bank' => $b->bank,
            'bank_account_name' => $b->bank_account_name,
            'bank_account_number' => $b->bank_account_number,
            'is_active' => $b->is_active,
        ])->toArray();

        $this->contacts = $c->contacts->map(fn($co) => [
            'contact_type' => $co->contact_type,
            'customer_contact_address' => $co->customer_contact_address,
            'customer_contact_region' => $co->customer_contact_region,
            'customer_contact_state' => $co->customer_contact_state,
            'customer_contact_postal_code' => $co->customer_contact_postal_code,
            'customer_contact_email' => $co->customer_contact_email,
            'customer_contact_phone' => $co->customer_contact_phone,
            'customer_contact_mobile' => $co->customer_contact_mobile,
            'is_active' => $co->is_active,
        ])->toArray();

        $this->activeTab = 'general';
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) Customer::findOrFail($id)->delete();
    }
}
