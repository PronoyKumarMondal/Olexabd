# Automating Deployment Locally -> GitHub -> Hostinger

This guide explains how to set up **Continuous Deployment (CD)** so that whenever you push code to GitHub, your Hostinger site updates automatically.

---

## Prerequisite: GitHub Repository

1.  **Initialize Git & Link Repo** (Run these commands in your project folder):
    ```bash
    echo "# Olexabd" >> README.md
    git init
    git add README.md
    git commit -m "first commit"
    git branch -M main
    git remote add origin https://github.com/PronoyKumarMondal/Olexabd.git
    git push -u origin main
    ```
2.  **Add Remaining Files**:
    Since the above command only added `README.md`, you now need to push your actual project code:
    ```bash
    git add .
    git commit -m "Upload full project"
    git push
    ```

---

## Step 1: Configure Hostinger (Git Integration)

1.  Log in to **Hostinger hPanel**.
2.  Go to **Advanced** -> **Git**.
3.  **Add New Repository**:
    *   **Repository URL**: `https://github.com/PronoyKumarMondal/Olexabd.git`
    *   **Branch**: `main`
    *   **Directory**: Leave empty to deploy to `public_html`.
4.  **Install**:
    *   Hostinger will attempt to clone your repo.
    *   *Note: It might fail initially because of missing configuration. Ignore errors for now.*

---

## Step 2: Set Up Auto-Deployment (Webhook)

1.  In Hostinger **Git** section, click **Auto Deployment** or copy the **Webhook URL**.
2.  Go to **GitHub** -> **Settings** -> **Webhooks** -> **Add webhook**.
3.  **Payload URL**: Paste the Hostinger URL.
4.  **Content type**: `application/json`.
5.  **Trigger**: "Just the push event".
6.  Click **Add webhook**.

---

## Step 3: Create Database (Detailed)

Since your code is on the server, it needs a database to store users and products.

1.  Go to **Hostinger hPanel**.
2.  Click **Databases** -> **Management**.
3.  **Create a New MySQL Database**:
    *   **Database Name**: Enter a name (e.g., `olexabd`).
    *   **Username**: Enter a user (e.g., `admin`).
    *   **Password**: **IMPORTANT**: Create a strong password and **COPY IT** to a notepad now.
4.  Click **Create**.
5.  **Copy Details**:
    *   Look at the "List of Current Databases" below.
    *   Copy the **Database Name** (it will look like `u123456789_olexabd`).
    *   Copy the **Username** (it will look like `u123456789_admin`).

---

## Step 4: Configure Environment (.env)

The `.env` file holds your passwords. Git ignores it for security, so we must make it manually.

1.  Go to **Hostinger hPanel** -> **Files** -> **File Manager**.
2.  Open the `public_html` folder.
3.  **Create New File**:
    *   Name it: `.env` (don't forget the dot).
4.  **Edit the File**: Paste the following content:

```ini
APP_NAME=OlexaBD
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE_FROM_LOCAL_ENV
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456_olexabd  <-- PASTE DATABASE NAME FROM STEP 3
DB_USERNAME=u123456_admin    <-- PASTE USERNAME FROM STEP 3
DB_PASSWORD=your_password    <-- PASTE PASSWORD FROM STEP 3

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

5.  **Get Your APP_KEY**:
    *   Open your **local** `.env` file on your computer.
    *   Copy the `APP_KEY` (e.g., `base64:adsf...`).
    *   Paste it into the Hostinger `.env` file `APP_KEY=...`.
6.  **Save & Close**.

---

## Step 5: Install Dependencies & Run Setup

Your server has the code, but it needs to install libraries (Composer) and set up the database tables (Migration).

**Method: The "One-Click Setup" Route**

1.  **Locally**, open `routes/web.php`.
2.  Add this temporary route at the bottom:

```php
Route::get('/server-setup', function () {
    // 1. Install Dependencies (Might take time)
    // Note: Hostinger usually blocks 'composer install' via web. 
    // IF THIS FAILS, USE 'Option B' below.
    // exec('composer install --no-dev --optimize-autoloader');

    // 2. Clear Caches
    \Artisan::call('optimize:clear');
    \Artisan::call('config:clear');

    // 3. Migrate Database (Create Tables)
    \Artisan::call('migrate:fresh --seed --force');

    // 4. Link Storage (For Images)
    \Artisan::call('storage:link');

    return 'Setup Completed! <br> 1. Cache Cleared <br> 2. Database Migrated <br> 3. Storage Linked';
});
```

3.  **Push this change** to GitHub:
    ```bash
    git commit -am "Add setup route"
    git push
    ```
4.  **Wait 30 seconds** for Hostinger to auto-deploy.
5.  **Visit**: `yourdomain.com/server-setup`
    *   If you see "Setup Completed!", **YOU ARE LIVE!** 🎉
    *   *If you see a "500 Error" or "Class not found"*, it means Composer dependencies are missing. Proceed to **Option B**.

---

## Option B: Installing Vendor Dependencies (If Setup Route Fails)

If the route above failed, you need to upload the `vendor` folder manually because Hostinger Shared Hosting often restricts Composer.

1.  **Locally**: Zip your `vendor` folder.
2.  **Hostinger File Manager**: Upload `vendor.zip` to `public_html`.
3.  **Extract** it.
4.  Now visit `yourdomain.com/server-setup` again. It should work perfectly.

---

## Step 6: Final Cleanup

1.  **Remove the Setup Route**:
    *   Locally, delete the `/server-setup` route from `routes/web.php`.
    *   Commit and Push: `git commit -am "Remove setup route" && git push`.
2.  **Test your site**:
    *   Go to `yourdomain.com/admin`
    *   Login with your admin credentials (default seed: `admin@example.com` / `password` unless you changed it).

**Your Automatic Deployment Pipeline is COMPLETE.** 🚀
