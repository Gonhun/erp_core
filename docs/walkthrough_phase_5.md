# Project Walkthrough - Phase 5: Inventory & Traceability

This walkthrough covers the procurement-to-receipt cycle, stock auditing, and the new safety features implemented in the ERP system.

## 1. Purchase Order to Receipt Flow
- **Step 1**: Create a **Purchase Quotation** in the Purchasing module.
- **Step 2**: Click **"Confirm Order"**. 
- **Result**: The system now automatically triggers a **Goods Receipt** in the Inventory module. You will see a notification: *"Order confirmed and Inventory Receipt generated."*

## 2. Warehouse Operations (Goods Receipt)
- Navigate to **Inventory > Receipts**.
- Open the pending receipt (Status: `READY`).
- **Partial Shipment**: Enter a lower quantity in the "Done" column.
- **Validate**: Click **VALIDATE** (Confirm the popup).
- **Backorder**: If the quantity was partial, the system automatically creates a second receipt in `WAITING` status for the remaining items.

## 3. Inventory Auditing (Stock Moves)
- Navigate to **Inventory > Stock Moves**.
- Here you can see a perfect ledger of all items received.
- Each move shows the **Reference** (GR Number), **Quantity** (with + sign for arrivals), and **Timestamp**.
- This ensures full accountability for every single piece of stock.

## 4. Safety & UI Improvements
- **Confirmations**: Every "Save", "Validate", or "Delete" action now prompts for confirmation. This prevents accidental data loss during high-speed operations.
- **Dashboard**: The Inventory, Finance, and Purchasing homepages now use a compact, Odoo-style "App Launcher" grid for faster navigation.
- **PDF Export**: Purchase documents now export with professional styling, fixed alignments, and clear totals.
