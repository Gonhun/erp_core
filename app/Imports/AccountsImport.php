<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\GroupAccount;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class AccountsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip if required fields are missing
        if (empty($row['account_code']) || empty($row['account_name'])) {
            return null;
        }

        // Resolve Group Account
        $groupAccount = null;
        if (!empty($row['group_account'])) {
            $groupAccount = GroupAccount::firstOrCreate([
                'group_accounts_name' => $row['group_account']
            ]);
        }

        // Resolve Parent Account (optional)
        $parentId = null;
        if (!empty($row['parent_code'])) {
            $parent = Account::where('account_code', $row['parent_code'])->first();
            $parentId = $parent ? $parent->id : null;
        }

        return new Account([
            'account_code' => $row['account_code'],
            'account_name' => $row['account_name'],
            'group_account_id' => $groupAccount ? $groupAccount->id : null,
            'balance_type' => $row['balance_type'] ?? 'Debit',
            'account_level' => $row['account_level'] ?? 1,
            'is_reconciliation' => isset($row['allow_reconciliation']) ? (bool) $row['allow_reconciliation'] : (isset($row['is_reconciliation']) ? (bool) $row['is_reconciliation'] : false),
            'reconciliation_type' => $row['reconciliation_type'] ?? null,
            'account_currency' => $row['currency'] ?? 'IDR',
            'is_parent' => isset($row['is_parent']) ? (bool) $row['is_parent'] : false,
            'parent_id' => $parentId,
            'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : true,
        ]);
    }
}
