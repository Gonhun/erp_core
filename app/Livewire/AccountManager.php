<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Models\GroupAccount;

class AccountManager extends Component
{
    public $accounts;
    public $groupAccounts;
    public $currencies = [];
    public $iteration = 0;

    // Inline Create State
    public $isCreating = false;
    public $new_account_code = '';
    public $new_account_name = '';
    public $new_group_account_id = '';
    public $new_balance_type = '';
    public $new_is_reconciliation = false;
    public $new_account_currency = '';
    public $new_is_active = true;

    // Inline Edit State
    public $editingId = null;
    public $edit_account_code = '';
    public $edit_account_name = '';
    public $edit_group_account_id = '';
    public $edit_balance_type = '';
    public $edit_is_reconciliation = false;
    public $edit_account_currency = '';
    public $edit_is_active = true;

    public function mount()
    {
        $this->groupAccounts = GroupAccount::orderBy('group_accounts_name', 'asc')->get();

        try {
            $this->currencies = \AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate::currencies();
        } catch (\Throwable $th) {
            // Fallback if ExchangeRate API key is missing or network fails
            $this->currencies = ['USD', 'EUR', 'GBP', 'IDR', 'CNY', 'JPY', 'AUD', 'CAD', 'CHF', 'HKD', 'SGD'];
        }
    }

    public function render()
    {
        $this->accounts = Account::with('groupAccount')->orderBy('account_code', 'asc')->get();
        return view('livewire.account-manager')->layout('layouts.app');
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
        $this->new_account_code = '';
        $this->new_account_name = '';
        $this->new_group_account_id = '';
        $this->new_balance_type = '';
        $this->new_is_reconciliation = false;
        $this->new_account_currency = '';
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
            'new_account_code' => 'required|string|max:255',
            'new_account_name' => 'required|string|max:255',
            'new_group_account_id' => 'required|exists:group_accounts,id',
            'new_balance_type' => 'required|string|max:6',
            'new_account_currency' => 'nullable|string|max:5',
        ]);

        Account::create([
            'account_code' => $this->new_account_code,
            'account_name' => $this->new_account_name,
            'group_account_id' => $this->new_group_account_id,
            'balance_type' => $this->new_balance_type,
            'account_level' => 1,
            'is_reconciliation' => (bool) $this->new_is_reconciliation,
            'account_currency' => $this->new_account_currency ?: null,
            'is_active' => (bool) $this->new_is_active,
            'is_parent' => false,
        ]);

        $this->isCreating = false;
        $this->resetNewFields();
        $this->iteration++;
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        $this->editingId = $id;
        $this->edit_account_code = $account->account_code;
        $this->edit_account_name = $account->account_name;
        $this->edit_group_account_id = $account->group_account_id;
        $this->edit_balance_type = $account->balance_type;
        $this->edit_is_reconciliation = $account->is_reconciliation;
        $this->edit_account_currency = $account->account_currency;
        $this->edit_is_active = $account->is_active;

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
            'edit_account_code' => 'required|string|max:255',
            'edit_account_name' => 'required|string|max:255',
            'edit_group_account_id' => 'required|exists:group_accounts,id',
            'edit_balance_type' => 'required|string|max:6',
            'edit_account_currency' => 'nullable|string|max:5',
        ]);

        $account = Account::findOrFail($this->editingId);
        $account->update([
            'account_code' => $this->edit_account_code,
            'account_name' => $this->edit_account_name,
            'group_account_id' => $this->edit_group_account_id,
            'balance_type' => $this->edit_balance_type,
            'is_reconciliation' => (bool) $this->edit_is_reconciliation,
            'account_currency' => $this->edit_account_currency ?: null,
            'is_active' => (bool) $this->edit_is_active,
        ]);

        $this->editingId = null;
        $this->iteration++;
    }

    public function delete($id)
    {
        $this->iteration++;
        if ($id) {
            Account::findOrFail($id)->delete();
        }
    }
}
