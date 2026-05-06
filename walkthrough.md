# Walkthrough: Purchasing Flow

## 1. Creating a Quotation
You will navigate to **Purchasing > Purchase Orders** and click **NEW**. 
- **Vendor Selection**: Select a Vendor. The system will automatically check if this vendor is marked for **PPN** and set the default tax for your items.
- **Header Details**: Set the Order Date, Deadline, and Delivery Warehouse.
- **Item List**: Add products. For each item:
    - Specify **Quantity** and **Unit Price**.
    - Apply a **Discount %** if applicable.
    - Select specific **Taxes** (defaults to PPN if the vendor is eligible).
- **Auto-Calculation**: The system calculates the row subtotal (Price * Qty - Discount) and sums up all untaxed amounts and taxes at the bottom.
- **Initial Status**: Quotation (Draft).

## 2. Transitioning to RFQ
Once the items are correct, you click **"Confirm RFQ"**.
- This changes the status to **RFQ SENT**.
- The record remains editable in case the vendor provides different pricing.

## 3. Confirming as Purchase Order (PO)
Once the vendor confirms availability and price:
- Click **"Confirm Order"**.
- The status changes to **PURCHASE ORDER**.
- This locks the order and signals the warehouse to expect delivery.

## 4. Dashboard Integration
The **Purchasing Dashboard** provides quick access to your Vendors and active Purchase Orders, showing real-time totals and statuses.
