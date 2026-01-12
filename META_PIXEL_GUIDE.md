# Meta Pixel Integration & Facebook Sales Tracking Guide

This guide explains how to integrate Meta (Facebook) Pixel into OlexaBD and how to automatically attribute orders to "Facebook" (or any other channel) using special links.

## 1. Configure Meta Pixel

### Step 1: Get your Pixel ID
1. Go to **Facebook Events Manager**.
2. Copy your **Dataset ID** (Pixel ID). It is a long number like `123456789012345`.

### Step 2: Add to Environment
1. Open your `.env` file on the server (or locally).
2. Add this line at the bottom:
   ```env
   META_PIXEL_ID=your_pixel_id_here
   ```
3. Save the file.

---

## 2. How Tracking Works (Automatic Channel Attribution)

We have implemented a system where special "Source Links" automatically tell the website where the customer came from.

### The "Magic" Link
When running Facebook Ads or posting on Social Media, add `?source=facebook` to the end of your URL.

*   **Homepage Ad:** `https://olexabd.com?source=facebook`
*   **Product Ad:** `https://olexabd.com/product/fridge-123?source=facebook`

### What happens when a user clicks?
1.  The website detects `source=facebook`.
2.  It saves "facebook" into the customer's **session** (browser memory) for 30 days.
3.  When they checkout, the order is automatically saved with **Source: facebook** in the database.
4.  You will see "Facebook" as the Source in your Admin Order List.

---

## 3. Verify It's Working

### Option A: Use "Facebook Pixel Helper"
1. Install the **Meta Pixel Helper** Chrome Extension.
2. Visit your site.
3. The extension should light up green and show `PageView`.
4. Add an item to cart -> It should show `AddToCart`.
5. Purchase -> It should show `Purchase`.

### Option B: Test an Order
1. Open an Incognito window.
2. Visit `https://olexabd.com?source=test_channel`.
3. Place an order.
4. Go to **Admin Panel > Orders**.
5. Check if the new order says **Source: test_channel**.

---

## 4. Technical Implementation Details (For Developers)

### A. Frontend (Pixel Script)
The Pixel code is added to `resources/views/layouts/app.blade.php`. It automatically reads `META_PIXEL_ID` from your configuration.

### B. Backend (Source Tracking)
A Middleware `TrackSource` captures the `source` parameter and stores it in the Session (`session()->put('order_source', $request->source)`).
The `PaymentController` reads this session value and saves it to the `traffic_source` column in the database (while `source` column remains 'web' or 'app').
