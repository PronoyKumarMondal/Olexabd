# Deploying Laravel to Hostinger (Shared Hosting)

This guide walks you through deploying your **OlexaBD** e-commerce application to Hostinger's Shared Hosting.

---

## 1. Prepare Your Application Locally

Before uploading, we need to optimize the application.

1.  **Clear Caches**:
    open your terminal in the project folder and run:
    ```bash
    php artisan optimize:clear
    php artisan config:clear
    ```

2.  **Zip the Project**:
    Compress your entire project folder (`Ecommerce appliance`) into a `.zip` file.
    *Exclude `node_modules` if it exists, as it makes the file huge and isn't needed for production if assets are built.*

---

## 2. Prepare Hostinger Database

1.  Log in to your **Hostinger hPanel**.
2.  Go to **Databases** -> **Management**.
3.  Create a **New MySQL Database**:
    *   **Database Name**: e.g., `u123456789_olexabd`
    *   **Username**: e.g., `u123456789_admin`
    *   **Password**: *Create a strong password and save it!*
4.  Click **Create**.
5.  Save these credentials; you will need them for the `.env` file.

---

## 3. Upload Files

1.  Go to **Files** -> **File Manager**.
2.  Navigate to `public_html`.
    *   *If you want the site to be on the main domain (e.g., yoursite.com), stay in `public_html`.*
    *   *If you want a subdomain (e.g., store.yoursite.com), navigate to that folder.*
3.  **Upload** your `.zip` file here.
4.  **Extract** the zip file.
    *   Ensure all files (like `app`, `bootstrap`, `public`, `vendor`, `.env.example`) are directly in `public_html` (or your subdomain folder), **not** inside a subfolder like `Ecommerce appliance`.
    *   If they are in a subfolder, move them all up one level.

---

## 4. Configure Application

1.  **Rename .env**:
    *   Find `.env.example` file.
    *   Rename it to `.env`.
2.  **Edit .env**:
    *   Right-click `.env` and select **Edit**.
    *   Update the following lines:
        ```ini
        APP_NAME=OlexaBD
        APP_ENV=production
        APP_KEY=  <-- If empty, we will generate it later, but better to copy from your local .env
        APP_DEBUG=false
        APP_URL=https://your-domain.com

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=u123456789_olexabd  (Use the name from Step 2)
        DB_USERNAME=u123456789_admin   (Use the user from Step 2)
        DB_PASSWORD=your_password      (Use the password from Step 2)
        ```
    *   Save the file.

---

## 5. Set Up Public Folder (Crucial for Shared Hosting)

By default, Laravel points to `public/`. Shared hosting servers point to `public_html`. We need to fix this via `.htaccess`.

1.  In `public_html`, create a new file named `.htaccess` (if it exists, edit it).
2.  Add this code to redirect traffic to the `public` folder securely:

    ```apache
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteRule ^(.*)$ public/$1 [L]
    </IfModule>
    ```

    *Alternatively, you can move everything inside `public/` to `public_html` and adjust `index.php` paths, but the `.htaccess` method is cleaner.*

---

## 6. Import Database (SQLite -> MySQL)

Since you are using SQLite locally but MySQL on the server, you cannot simply export your local file. You need to create the tables in the Hostinger MySQL database.

**Option A: If you have SSH Access (Recommended)**
1.  Connect via SSH.
2.  Run: `php artisan migrate:fresh --seed --force`

**Option B: Web Route Migration (No SSH)**
Since shared hosting often lacks SSH, we can use a temporary web route to run migrations.

1.  Open `routes/web.php` **locally**.
2.  Add this temporary route at the bottom:
    ```php
    Route::get('/run-migration', function () {
        Artisan::call('optimize:clear');
        Artisan::call('migrate:fresh --seed --force');
        return "Migration completed! Check database.";
    });
    ```
3.  **Upload** this modified `web.php` to your server (replace the one in `public_html/routes`).
4.  Visit `yourdomain.com/run-migration` in your browser.
5.  Wait for the "Migration completed!" message.
6.  **Capabilities Check**: Go to Hostinger phpMyAdmin to confirm tables are created.
7.  **IMPORTANT**: Remove that route from `routes/web.php` on the server immediately after use for security.

---

## 7. Link Storage

Images uploaded to `storage/app/public` need to be accessible from `public/storage`.

**Option A: SSH**
Run: `php artisan storage:link`

**Option B: PHP Script (No SSH)**
1.  Create a file `link_storage.php` in `public_html`.
2.  Add:
    ```php
    <?php
    $target = '/home/u123456789/domains/yourdomain.com/public_html/storage/app/public';
    $shortcut = '/home/u123456789/domains/yourdomain.com/public_html/public/storage';
    symlink($target, $shortcut);
    echo "Storage linked!";
    ?>
    ```
    *(Update paths to match your actual Hostinger path shown in File Manager).*
3.  Visit `yourdomain.com/link_storage.php`.
4.  Delete the file afterwards.

---

## 8. Final Checks

1.  Visit your website.
2.  Test **Login/Register**.
3.  Test **Admin Panel**.
4.  Test **Image Upload** (Banner creation) to verify storage link.

**Done! Your OlexaBD store is live.** 🚀
