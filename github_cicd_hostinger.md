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

## Step 1: Configure Hostinger

1.  Log in to **Hostinger hPanel**.
2.  Go to **Advanced** -> **Git**.
3.  **Add New Repository**:
    *   **Repository URL**: `https://github.com/PronoyKumarMondal/Olexabd.git`
    *   **Branch**: `main`
    *   **Directory**: Leave empty to deploy to `public_html`, or type a subfolder name.
4.  **Install**:
    *   Hostinger will attempt to clone your repo.
    *   *Note*: If you have a `.gitignore` that excludes `vendor` (which it should!), your site will break initially. We will fix this in Step 3.

---

## Step 2: Set Up Auto-Deployment (Webhook)

1.  In the Hostinger **Git** section, find your repository.
2.  Look for the **"Auto Deployment"** button or "Webhook URL".
3.  **Copy this URL**. It usually looks like: `https://api.hostinger.com/git/webhook/....`
4.  Go to **GitHub** -> **Your Repo** -> **Settings**.
5.  Click **Webhooks** -> **Add webhook**.
6.  **Payload URL**: Paste the Hostinger URL here.
7.  **Content type**: `application/json`.
8.  **Just the push event**: Selected.
9.  Click **Add webhook**.

*Now, every time you `git push`, GitHub tells Hostinger to "Pull" the latest changes.*

---

## Step 3: Handle Dependencies (Vendor & .env)

Hostinger's Git pull **only** downloads your code files. It does **not** automatically run `composer install` or `php artisan migrate`.

### 1. The .env File
Since `.env` is ignored by Git (security), you must create it manually on Hostinger one time.
*   Go to Hostinger **File Manager**.
*   Create `.env` file.
*   Paste your production configuration (Database credentials, etc.).

### 2. The Vendor Folder (Composer)
You have two options:

**Option A: Run Composer on Hostinger (Recommended Strategy)**
1.  Go to **Advanced** -> **PHP Configuration**. Ensure PHP 8.2+ is selected.
2.  Go to **Advanced** -> **Cron Jobs**.
3.  Create a "Run Once" or manually run a command (via SSH if available) to install dependencies:
    ```bash
    cd domains/yourdomain.com/public_html && composer install --no-dev --optimize-autoloader
    ```
    *If you don't have SSH, you might need to upload the `vendor` folder manually via FTP once, or use a "Web Route" (see below).*

**Option B: Web Route for Post-Deploy Tasks**
Since the Webhook only updates files, you need a way to clear cache and migrate DB.
Add this route to `routes/web.php`:

```php
Route::get('/deploy-tasks', function () {
    // 1. Install/Update Dependencies (Only works if Composer is available and exec enabled)
    // exec('composer install --no-dev'); 
    
    // 2. Clear Caches
    Artisan::call('optimize:clear');
    
    // 3. Migrate Database
    Artisan::call('migrate --force');
    
    return "Deployment tasks completed!";
});
```
*After pushing code, visit `yourdomain.com/deploy-tasks` to finalize the update.*

---

## Step 4: Test It

1.  Make a small change locally (e.g., change a text in `welcome.blade.php`).
2.  Commit and Push:
    ```bash
    git commit -am "Update welcome text"
    git push
    ```
3.  Wait ~30 seconds.
4.  Check your live website. The change should appear!
