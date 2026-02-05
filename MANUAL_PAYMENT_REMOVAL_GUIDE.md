# Manual Payment Removal & Gateway Integration Guide

**Created:** Feb 2026
**Target Date:** ~10 Days from creation
**Purpose:** This document tracks all changes made for the "Manual Payment" system so they can be safely removed or adapted when integrating a real Payment Gateway (e.g., SSLCommerz, AmarPay, Stripe).

> [!WARNING]
> **Production Critical Operation:** These changes affect the Checkout flow and Order recording. Follow these steps carefully to avoid breaking order placement.

---

## 1. Database Considerations

**Table:** `orders`
**Columns Added:** `transaction_id`, `payment_number`

> [!TIP]
> **Do NOT Drop Columns Immediately.**
> Even when you switch to a real gateway, you will still need a column to store the Gateway's Transaction ID (e.g., `val_id` or `tran_id`). You can repurpose the existing `transaction_id` column for this.
> You might want to keep `payment_number` for historical reference of old manual orders, or drop it if unused.

---

## 2. Frontend Changes (`checkout.blade.php`)

**File:** `resources/views/shop/checkout.blade.php`

### What to Remove/Replace:
1.  **Payment Method Radio Buttons:**
    *   Currently, we have hardcoded HTML for `cod`, `bkash`, `bank`.
    *   **Action:** Replace these with the Gateway's Button or a valid list of supported gateways.
    *   *If keeping COD:* You can keep the COD radio button but remove the "Advance Payment" warning logic if you stop requiring advance delivery charges.

2.  **Manual Input Fields (`#payment-details-section`):**
    *   We added a hidden div `#payment-details-section` that shows `transaction_id` and `payment_number` inputs.
    *   **Action:** **DELETE** this entire section. Real gateways handle input on their own secure pages, not your checkout form.

3.  **JavaScript Logic (`updatePaymentUI`):**
    *   We added JS to toggle the visibility of the manual inputs and set them as `required`.
    *   **Action:** **DELETE** the `updatePaymentUI` function and its calls.

---

## 3. Backend Controller (`CheckoutController.php`)

**File:** `app/Http/Controllers/CheckoutController.php`

### Method: `placeOrder`

1.  **Validation Rules:**
    *   We added conditional validation: `if ($paymentMethod == 'bkash' || ...)` checking for `transaction_id`.
    *   **Action:** **REMOVE** these custom validation checks. Real gateways don't expect a TrxID *before* redirection.

2.  **Order Creation (`$orderData`):**
    *   We are manually saving `'transaction_id' => $request->transaction_id`.
    *   **Action:** For a real gateway, you typically create the order with status `unpaid` first, **THEN** redirect the user to the gateway URL. You won't have a transaction ID at this step yet.

3.  **Redirect Logic (Crucial Step):**
    *   Currently: It redirects to `shop.index` with success.
    *   **Future:** It must redirect to the **Payment Gateway URL**.

---

## 4. Admin Panel Cleanup

**Files:**
1.  `resources/views/layouts/admin.blade.php` (Sidebar)
    *   **Action:** Remove the "Payment Verification" link if manual verification is no longer needed.

2.  `app/Http/Controllers/Admin/OrderController.php`
    *   **Action:** Remove logic for `has_transaction` filter if you no longer filter by manual inputs.
    *   **Action:** Remove `mark_paid` logic if the gateway handles status updates automatically via IPN (Instant Payment Notification).

3.  `resources/views/admin/orders/show.blade.php`
    *   **Action:** Remove the "Verify & Mark Paid" button.
    *   **Action:** Keep the display for `transaction_id` (so you can see the Gateway's TrxID), but maybe rename the label from "Manual Payment" to "Gateway Details".

---

## 5. Integration Workflow (The "To-Do" List)

When you are ready to integrate the gateway:

1.  [ ] **Install Gateway Package:** Add the library (e.g., `sslcommerz-laravel`).
2.  [ ] **Modify Checkout Controller:**
    *   Remove Manual Validation.
    *   Create Order as "Pending/Unpaid".
    *   Initiate Gateway Payment (Call API).
    *   **Redirect User** to the returned Gateway URL.
3.  [ ] **Implement IPN/Callback:**
    *   Create a new Route/Controller method (e.g., `OrderController@paymentSuccess`) to handle the return from the Gateway.
    *   In this method, update the Order:
        ```php
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $request->val_id // Store Gateway's ID
        ]);
        ```
4.  [ ] **Clean Up Views:** Remove the manual inputs from `checkout.blade.php`.
