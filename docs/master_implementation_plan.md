# Master Implementation Plan - ERP Core System

This document serves as the comprehensive technical record of all architectural decisions, database schemas, and logic flows implemented since the inception of the project.

## 1. Project Vision & Standards
- **Objective**: Build a professional, Odoo-inspired ERP with high-density UI/UX.
- **Tech Stack**: Laravel 11, Livewire 3, PostgreSQL, Vanilla CSS.
- **Design Standard**: Compact horizontal launcher grid, UUID-based primary keys, and real-time reactive calculations.

---

## 2. Phase 1: Finance Foundation (Completed)
- **Account Management**:
    - [x] **`group_accounts`**: Hierarchical grouping for financial reporting.
    - [x] **`accounts`**: Full Chart of Accounts (COA) with `balance_type`, `currency`, and `reconciliation` support.
    - [x] **Excel Importer**: Bulk upload utility for COA with auto-mapping.
- **Tax System**:
    - [x] **`taxes`**: Multi-computation engine (Percentage, Fixed, Group of Taxes).
    - [x] **`tax_setup`**: Detailed GL account mapping for Invoice vs. Refund contexts.
- **CRM Lite**:
    - [x] **`customers`**: Multi-address and multi-contact management for sales.

---

## 3. Phase 2: Inventory & Catalog (Completed)
- **Structure**:
    - [x] **`warehouses`**: Multi-warehouse support with shipment step logic.
    - [x] **`warehouse_locations`**: Bins, aisles, and shelf tracking.
- **Product Catalog**:
    - [x] **`products`**: UUID-based catalog with part numbers and invoicing policies.
    - [x] **`uom_categories` & `uoms`**: Complex ratio-based unit conversions (e.g., Box to Units).
    - [x] **`brands` & `item_categories`**: Hierarchical classification.
- **Stock Tracking (New)**:
    - [x] **`product_stocks`**: Real-time balance table (Product x Warehouse).

---

## 4. Phase 3 & 4: Purchasing Lifecycle (Completed)
- **Vendors**:
    - [x] **`suppliers`**: Comprehensive vendor profiles including Bank Accounts, PPN status, and PKP details.
- **Workflow**:
    - [x] **`purchase_orders`**: Header tracking `order_deadline`, `expected_arrival`, and `currency`.
    - [x] **`purchase_order_items`**: Line-level tracking with **Discounts** and **Tax** calculations.
    - [x] **Document States**: `Draft (Quotation)` → `Sent (RFQ)` → `Purchase (Confirmed)`.
- **Outputs**:
    - [x] **PDF Engine**: Barryvdh/DomPDF integration for professional branded document generation.

---

## 5. Phase 5: Inventory Reception & Traceability (Completed)
- **Execution**:
    - [x] **`goods_receipts`**: Source-linked arrival documents with PIB tracking.
    - [x] **Automation**: Auto-generation of receipts upon PO confirmation.
    - [x] **Audit Ledger**:
        - [x] **`stock_moves`**: Immutable ledger recording every item "in" and "out".
- **Advanced Logic**:
    - [x] **Split Shipments**: Automated Backorder generation for partial receipts.

---

## 6. Phase 6: Finance & Vendor Bills (Completed)
- **Billing & AP**:
    - [x] **`vendor_bills`**: Header tracking bill number, PO reference, invoice date, due date, status (`draft`, `posted`, `paid`, `cancel`), and totals.
    - [x] **`vendor_bill_items`**: Line items mapping products to quantities, prices, taxes, discounts, and GL accounts.
    - [x] **Roman Month Number Sequence**: Pre-generated sequential document numbers (`BILL/{Year}/{RomanMonth}/{5-digit Sequence}`) reactively updated on date change.
    - [x] **Double-Entry Journal Generation**: Automatically creates posted Journal Entries and dual-balanced Journal Items upon bill validation.
- **Double-Entry General Ledger**:
    - [x] **`journal_entries`**: Ledger transaction records.
    - [x] **`journal_items`**: Dual-balanced journal ledger lines enforcing Total Debits = Total Credits.
- **Payments & Settlements**:
    - [x] **`payments`**: Cash out tracking bank/cash accounts and amounts.
    - [x] **`payment_allocations`**: Multi-bill payment allocations mapping cash payouts to settle vendor bills.
- **Odoo Tabbed Interface**:
    - [x] **Invoice Lines vs. Journal Items**: Compact toggle interface displaying product inputs alongside live general ledger preview.

---

## 7. System-Wide Features (Completed)
- [x] **UI/UX Launcher**: Redesigned homepages for Finance, Inventory, and Purchasing using compact icon-based grids.
- [x] **Safety Interlocks**: Implementation of `wire:confirm` across all CRUD and Status-change actions.
- [x] **Polymorphic Search**: Searchable Select2 integrations for all relational fields.
- [x] **Enterprise-Grade Try-Catch Handling**: Every create, update, and delete action in our Livewire controllers is fully protected inside `try-catch` blocks with custom session flash error messaging to prevent raw system exposure and ensure maximum application stability.
