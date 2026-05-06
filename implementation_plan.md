# Implementation Plan: Purchasing Module

## Phase 1: Supplier (Vendor) Management
Before we can create orders, we need a Master table for Suppliers. We will use a structure similar to our `Customers` module.
- **Migration**: `create_suppliers_table`, `supplier_accountings`, `supplier_contacts`, `supplier_bank_accounts`.
- **Model**: `Supplier` with relationships.
- **UI**: `SupplierManager` Livewire component.

## Phase 2: Purchase Order Structure
A single table to handle the entire lifecycle (Quotation, RFQ, PO) using a `status` field.
- **Migration**: `create_purchase_orders_table` and `create_purchase_order_items_table`.
- **Status Enum**: `draft` (Quotation), `rfq` (Sent), `purchase` (PO), `cancel`, `done`.

## Phase 3: Livewire Order Manager
A complex form to handle the header and dynamic item lines.
- **Features**:
    - Auto-calculate Subtotals, Taxes, and Grand Totals.
    - Dynamic row addition/removal for items.
    - Status transition buttons (e.g., "Confirm Order").
    - Print/PDF export logic (optional next step).

## Phase 4: Refined Order Logic
- **Line-Level Discounts**: Each item can have a percentage discount applied, which reduces the untaxed subtotal for that row.
- **Line-Level Taxes**: Taxes are selected per item. If a vendor is marked as `is_ppn`, the standard PPN tax is automatically set as the default for new lines.
- **Real-time Recalculation**: Row subtotals and total taxes are recalculated instantly upon any input change.

## Phase 5: Inventory Reception Preparation
- **PO-to-Receipt Link**: Plan for the transition from a "Confirmed" PO to a "Ready" Goods Receipt.
- **Backorder Support**: Logic for partial shipments.