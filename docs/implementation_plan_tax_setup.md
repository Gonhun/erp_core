# Tax Setup Manager Implementation Plan

## Goal
Create a unified Livewire component (`TaxSetupManager`) to manage the setup configurations for a specific `Tax`. This interface will consolidate three separate CRUD tables (`taxes_invoice_definition`, `taxes_refund_definition`, and `taxes_setup`) into a single view, organized by Bootstrap tabs, utilizing the Odoo-style inline editing UI.

## Proposed Changes

### Component Level (Livewire & Routing)

#### [MODIFY] `routes/web.php`
- Add a new route: `Route::get('/finance/taxes/{tax}/setup', \App\Livewire\TaxSetupManager::class)->name('finance.taxes.setup');`

#### [NEW] `app/Livewire/TaxSetupManager.php`
- Component to handle the `Tax` model instance passed via URL.
- Handle active tab state (`public $activeTab = 'definition'`).
- Handle `$accounts` loading for the dropdowns.
- Implement **three separate sets** of CRUD logic:
  - **Invoice Definition CRUD**: Variables (`$invIsCreating`, `$invEditingId`, etc.), and functions (`saveNewInv`, `saveEditInv`, `deleteInv`).
  - **Refund Definition CRUD**: Variables (`$refIsCreating`, `$refEditingId`, etc.), and functions (`saveNewRef`, `saveEditRef`, `deleteRef`).
  - **Setup CRUD**: Variables (`$setupIsCreating`, `$setupEditingId`, etc.), and functions (`saveNewSetup`, `saveEditSetup`, `deleteSetup`).
- Implement independent `$iteration` counters for each grid (`$invIteration`, `$refIteration`, `$setupIteration`) to guarantee perfectly isolated DOM resets and avoid any Select2 "stuck" bugs.

### UI Level (Blade Template)

#### [NEW] `resources/views/livewire/tax-setup-manager.blade.php`
- **Header**: Display the name of the Tax currently being configured, along with a "Back to Taxes" button.
- **Tabs Layout**:
  - Two Bootstrap Nav Tabs: "Definition" and "Advanced Setup".
- **Tab 1: Definition Input**:
  - Two independent Odoo-style tables stacked vertically.
  - **Top Table**: "Invoice Definitions" with columns: Account, Amount, Actions.
  - **Bottom Table**: "Refund Definitions" with columns: Account, Amount, Actions.
  - Inline Select2 dropdowns wrapped in Alpine.js for robust state binding with Livewire.
- **Tab 2: Setup Input**:
  - One Odoo-style table for `taxes_setup` with columns: Label, Country, Included in Price (Toggle Switch), Actions.

## Verification Plan

### Automated Tests
- Validate all validation rules (e.g., ensuring `def_amount` is correctly cast to decimal, and `account_id` is required).

### Manual Verification
- Navigate to a Tax setup page.
- Switch between tabs to verify state preservation.
- Attempt to create, edit, and delete an Invoice Definition, ensuring Select2 fields clear correctly on cancel/save using the `$iteration` property.
- Repeat manual verification for Refund Definitions and Tax Setup grids.
