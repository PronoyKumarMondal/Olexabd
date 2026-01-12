# 📘 Meta (Facebook) Tracking & Attribution Guide

This guide explains how your store tracks sales, attributes them to ads, and sends data to Facebook for "Pay Per Sale" optimization.

## 🎯 Features Overview
1.  **Pixel Tracking (Browser):** Tracks user actions on their browser.
2.  **Source Attribution:** Tracks exactly where a customer came from (Facebook, Instagram, Google) and labels the order in the Admin Panel.
3.  **Conversions API (Server-Side):** Sends "Purchase" events directly from your server to Facebook. This bypasses AdBlockers and ensures 100% data accuracy for Ad Optimization.

---

## 🚀 1. Setup Instructions (Start Here)

### Step 1: Create a Pixel (Get Pixel ID)
1.  Go to [Facebook Business Settings](https://business.facebook.com/settings/).
2.  Select your Business Account.
3.  On the left menu, go to **Data Sources** -> **Pixels** (or **Datasets** in newer interfaces).
4.  Click **Add** -> Name it "Olexa Store" -> Click Continue.
5.  Once created, click on the ID at the top to copy it (e.g., `123456789012345`).

### Step 2: Get CAPI Access Token
1.  In the same **Data Sources** menu, click **Open in Events Manager**.
2.  Go to the **Settings** tab.
3.  Scroll down to the **Conversions API** section.
4.  Look for "Set up manually" and click **Generate Access Token**.
5.  Copy the very long text string.

### Step 3: Connect it to Your Website
1.  Open your **Hostinger File Manager** (or SSH).
2.  Edit the `.env` file in `public_html`.
3.  Add these two lines at the bottom:

```env
META_PIXEL_ID=paste_pixel_id_here
META_ACCESS_TOKEN=paste_long_token_here
```

4.  **Save** the file.
5.  Run this command in terminal to activate it:
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

### Method C: Admin Panel (3-Layer Tracking)
Go to **Admin -> Orders**. We now track 3 distinct layers for every order:

| Column | Description | Examples |
| :--- | :--- | :--- |
| **Order Portal** | **Who** placed the order and **Where**? | `Customer Portal - Web`, `Admin Portal`, `Customer App` |
| **Traff. Source** | **Marketing Origin** (How they found us) | `facebook`, `instagram_bio`, `google_ads` |
| **Platform** | **Device Environment** | `WEB` (Browser), `APP` (Mobile App), `FB APP` |

*   **Example 1:** You create an order for a client who called you.
    *   Portal: `Admin Portal`
    *   Traffic: `Phone Call` (if selected)
    *   Platform: `WEB`

*   **Example 2:** A customer clicks a Facebook Ad and buys on the website.
    *   Portal: `Customer Portal - Web`
    *   Traffic: `facebook`
    *   Platform: `WEB`
