<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GroupAccount;

class GroupAccountManager extends Component
{
    public $groupAccounts;
    public $iteration = 0;
    
    // Inline Create State
    public $isCreating = false;
    public $newName = '';

    // Inline Edit State
    public $editingId = null;
    public $editingName = '';

    public function render()
    {
        $this->groupAccounts = GroupAccount::orderBy('group_accounts_name', 'asc')->get();
        return view('livewire.group-account-manager')->layout('layouts.app');
    }

    public function createNew()
    {
        $this->isCreating = true;
        $this->newName = '';
        $this->editingId = null; // Close any active edit
        $this->iteration++;
    }

    public function cancelNew()
    {
        $this->isCreating = false;
        $this->newName = '';
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveNew()
    {
        $this->validate([
            'newName' => 'required|string|max:255'
        ]);

        GroupAccount::create([
            'group_accounts_name' => $this->newName,
        ]);

        $this->isCreating = false;
        $this->newName = '';
        $this->iteration++;
        // Optional: session()->flash('message', 'Group Account created.');
    }

    public function edit($id)
    {
        $groupAccount = GroupAccount::findOrFail($id);
        $this->editingId = $id;
        $this->editingName = $groupAccount->group_accounts_name;
        $this->isCreating = false; // Close create mode if open
        $this->resetValidation();
        $this->iteration++;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editingName = '';
        $this->resetValidation();
        $this->iteration++;
    }

    public function saveEdit()
    {
        $this->validate([
            'editingName' => 'required|string|max:255'
        ]);

        $groupAccount = GroupAccount::findOrFail($this->editingId);
        $groupAccount->update([
            'group_accounts_name' => $this->editingName,
        ]);

        $this->editingId = null;
        $this->editingName = '';
        $this->iteration++;
        // Optional: session()->flash('message', 'Group Account updated.');
    }

    public function delete($id)
    {
        if ($id) {
            GroupAccount::findOrFail($id)->delete();
            $this->iteration++;
        }
    }
}
