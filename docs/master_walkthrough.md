# Master Project Walkthrough - ERP Core System

Welcome to your unified ERP Core. This document guides you through the entire end-to-end business flow, from financial setup to warehouse reception.

---

## 1. The Core Experience (UI/UX)
Your system is designed for high-density professional work. 
- **Launcher Dashboards**: The Home pages for **Finance**, **Inventory**, and **Purchasing** feature compact "App Launcher" grids. Each icon gives you instant access to specific sub-modules.
- **Safety Interlocks**: Every time you Save, Update, or Delete data, the system will ask for confirmation (`wire:confirm`) to prevent operational errors.

---

## 2. Finance & Accounting Setup
- **Chart of Accounts**: Navigate to **Finance > Accounts**. You can manually create accounts or use the **IMPORT** button to bulk-load your COA from Excel.
- **Tax Mapping**: In **Finance > Taxes**, use the **SETUP** button on any tax line to map specific GL accounts for Invoices and Refunds. The system handles Percentage, Fixed, and even "Grouped" taxes automatically.

---

## 3. Product & Warehouse Catalog
- **Warehouses**: Before buying goods, define your **Warehouses** and **Locations** (Aisles/Bins) in the Inventory module.
- **Products**: The **Inventory > Products** module tracks everything from part numbers to units of measure.
- **Units (UoM)**: The system handles complex conversions. For example, you can buy in "Boxes" and store in "Units" with automatic ratio calculations.

---

## 4. The Procurement Cycle (Purchasing)
- **Step 1: Quotation**: Start in **Purchasing > Orders** and click **NEW QUOTATION**. Select a Vendor (Tax/PPN status will auto-load).
- **Step 2: RFQ**: Click **CONFIRM RFQ** to mark the document as sent to the vendor. You can print the professional PDF at any time.
- **Step 3: Confirmation**: Once the price is agreed upon, click **CONFIRM ORDER**. 
- **Result**: The document becomes a legal **Purchase Order**, and the system automatically sends a "Pending Arrival" notification to the Warehouse.

---

## 5. Warehouse & Stock Management
- **Receiving Goods**: Navigate to **Inventory > Receipts**. You will see your incoming shipment waiting in `READY` status.
- **Processing**: Click on the receipt. If the vendor sent less than ordered, enter the actual amount received in the **"Done"** column.
- **Backorders**: Click **VALIDATE**. If the shipment was partial, the system will instantly add the arrived goods to stock and create a **Backorder Receipt** for the missing balance.
- **Auditing**: Go to **Inventory > Stock Moves** to see the full "Inventory Ledger"—a chronological history of every item that ever entered or left your warehouse.

---

## 6. Real-time Balances
At any time, you can verify your physical stock levels in the system. The **Inventory Ledger** and **Product Stock** tables ensure that your digital records perfectly match your physical reality.
