# 📘 Meta (Facebook) Tracking & Attribution Guide

This guide explains how your store tracks sales, attributes them to ads, and sends data to Facebook for "Pay Per Sale" optimization.

## 🎯 Features Overview
1.  **Pixel Tracking (Browser):** Tracks user actions on their browser.
2.  **Source Attribution:** Tracks exactly where a customer came from (Facebook, Instagram, Google) and labels the order in the Admin Panel.
3.  **Conversions API (Server-Side):** Sends "Purchase" events directly from your server to Facebook. This bypasses AdBlockers and ensures 100% data accuracy for Ad Optimization.

---

## 🚀 1. Setup Instructions (Required)

To enable these features, you must add your Meta credentials to the `.env` file on your server.

**Step 1: Get Pixel ID**
1.  Go to Facebook Events Manager -> Data Sources.
2.  Copy your ID (e.g., `123456789`).

**Step 2: Get Access Token (For CAPI)**
1.  Go to Events Manager -> Settings.
2.  Scroll down to **Conversions API**.
3.  Click **Generate Access Token**.
4.  Copy the long string.

**Step 3: Update Server Config**
Open your `.env` file on Hostinger and add:
```env
META_PIXEL_ID=your_pixel_id_here
META_ACCESS_TOKEN=your_long_token_here
```

**Step 4: Clear Cache**
Run this command in the terminal to apply changes:
```bash
php artisan config:clear
```

---

## 🔗 2. How to Track Ads & Links

### The "Magic" Link (Simple)
For simple tracking, just add `?source=name` to any link. The Admin Panel will show this name.

*   **Facebook Post:** `https://olexabd.com/product/oven?source=facebook`
*   **Instagram Bio:** `https://olexabd.com?source=instagram_bio`

### UTM Parameters (Professional)
The system automatically detects standard UTM parameters used by Facebook Ads Manager.

*   **Ads Manager URL:** `https://olexabd.com?utm_source=facebook&utm_medium=cpc&utm_campaign=winter_sale`
*   **What You See in Admin:** `facebook (winter_sale)`

**Note:** The system remembers the source for 30 days. If a user clicks today and buys next week, you still get the credit!

---

## 📡 3. Server-Side Tracking (CAPI)

### Why "Pay Per Sale" Needs This
Browser tracking pixels miss about **20-30% of sales** due to iOS 14 updates, AdBlockers, and connection issues.
If Facebook doesn't see the sale, it can't optimize your ads, and you might pay for ads that don't convert.

**How We Fixed It:**
*   When a payment is successful, our server automatically talks to Facebook.
*   It sends a secure **"Purchase"** event with the Order ID, Amount, and Customer Email (hashed).
*   Facebook matches this user to the Ad they clicked and records the conversion reliably.

---

## ✅ 4. Verification

### Method A: Browser Helper
1.  Install the **"Meta Pixel Helper"** Chrome extension.
2.  Visit your site.
3.  It should light up green and show `PageView`.

### Method B: Test Events (Facebook)
1.  Go to Events Manager -> **Test Events**.
2.  Enter your website URL.
3.  Buy a product (or complete a mock purchase).
4.  You should see two events appear:
    *   **Browser** event (from the Pixel).
    *   **Server** event (from the CAPI).
    *   Facebook will say "Deduplicating..." which means it's working perfectly!

### Method C: Admin Panel
1.  Go to **Admin -> Orders**.
2.  Look at the columns:
    *   **Portal:** Shows `WEB` or `APP` (Where the order happened).
    *   **Traffic:** Shows `facebook`, `google`, or custom source (Where the user came from).
