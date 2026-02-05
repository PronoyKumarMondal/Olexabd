# Google OAuth Setup Guide for OlexaBD

To enable "Continue with Google", you need to create a project in the Google Cloud Console and get your **Client ID** and **Client Secret**.

## Step 1: Create a Project
1.  Go to the [Google Cloud Console](https://console.cloud.google.com/).
2.  Sign in with your Google Account.
3.  Click on the **Project Dropdown** (top left, near the logo) and click **"New Project"**.
4.  **Project Name:** Enter `OlexaBD Web`.
5.  Click **Create**.

## Step 2: Configure OAuth Consent Screen
1.  In the left sidebar, go to **APIs & Services** > **OAuth consent screen**.
2.  **User Type:** Select **External**.
3.  Click **Create**.
4.  **App Information:**
    *   **App Name:** `OlexaBD`
    *   **User Support Email:** Select your email.
    *   **Developer Contact Information:** Enter your email.
5.  Click **Save and Continue** (You can skip Scopes and Test Users for now by just clicking Save and Continue).
6.  Click **Back to Dashboard**.

## Step 3: Create Credentials
1.  In the left sidebar, click **Credentials**.
2.  Click **+ CREATE CREDENTIALS** (top of screen) > **OAuth client ID**.
3.  **Application Type:** Select **Web application**.
4.  **Name:** `OlexaBD Laravel Client`.
5.  **Configure URIs (Critical Step):**
    
    *   **Field 1: Authorized JavaScript origins** (Optional, but good for local dev)
        *   *Rule:* **NO** path, **NO** trailing slash. Just the domain.
        *   **Local:** `http://localhost:8000`
        *   **Live:** `https://olexabd.com`
        *   *(Do NOT put `/auth/...` here. Only the root domain.)*

    *   **Field 2: Authorized redirect URIs** (Required - Where Google sends data back)
        *   *Rule:* Must match your route **exactly**.
        *   Click **+ ADD URI**.
        *   **Local:** `http://localhost:8000/auth/google/callback`
        *   **Live:** `https://olexabd.com/auth/google/callback`

6.  Click **Create**.

## Step 4: Get Your Keys
1.  A popup will appear with your **Client ID** and **Client Secret**.
2.  **Copy these keys.**

## Step 5: Update Your Environment (.env)
Open your `.env` file in the project root and add these lines:

```env

```GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URL=https://olexabd.com/auth/google/callback
*(Use `http://localhost:8000/auth/google/callback` if testing locally)*

---

## Technical Note
Since your application requires a **Phone Number**, we have implemented a special flow:
1.  User signs in with Google.
2.  If they don't have a phone number in your system, they are **redirected** to a page to enter it.
3.  They **cannot** access the Checkout or Dashboard until they provide it.
