# 🔐 Google Sign-In Setup Guide

This guide will help you set up Google Sign-In for your Q2L application.

## 📋 Prerequisites

- A Google account
- Access to [Google Cloud Console](https://console.cloud.google.com/)

---

## 🚀 Step-by-Step Setup

### Step 1: Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click on the project dropdown at the top
3. Click **"New Project"**
4. Enter a project name (e.g., "Q2L Learning System")
5. Click **"Create"**
6. Wait for the project to be created, then select it

---

### Step 2: Enable Google+ API

1. In the Google Cloud Console, go to **"APIs & Services"** → **"Library"**
2. Search for **"Google+ API"** or **"Google Identity"**
3. Click on **"Google+ API"** or **"Google Identity API"**
4. Click **"Enable"**

**Note:** You can also use the newer "Google Identity Services" API if available.

---

### Step 3: Create OAuth 2.0 Credentials

1. Go to **"APIs & Services"** → **"Credentials"**
2. Click **"+ CREATE CREDENTIALS"** at the top
3. Select **"OAuth client ID"**
4. If prompted, configure the OAuth consent screen first:
   - Choose **"External"** (unless you have a Google Workspace account)
   - Click **"Create"**
   - Fill in the required information:
     - **App name**: Q2L Learning System (or your app name)
     - **User support email**: Your email address
     - **Developer contact information**: Your email address
   - Click **"Save and Continue"**
   - Skip scopes for now (click **"Save and Continue"**)
   - Add test users if needed (click **"Save and Continue"**)
   - Review and click **"Back to Dashboard"**

5. Now create the OAuth client:
   - **Application type**: Select **"Web application"**
   - **Name**: Q2L Web Client (or any name you prefer)
   - **Authorized JavaScript origins**: 
     ```
     http://localhost:8000
     http://127.0.0.1:8000
     ```
     (Add your production domain later if needed)
   - **Authorized redirect URIs**:
     ```
     http://localhost:8000/auth/google/callback
     http://127.0.0.1:8000/auth/google/callback
     ```
     (Add your production callback URL later if needed)
   - Click **"Create"**

6. **IMPORTANT**: Copy the **Client ID** and **Client Secret** that are displayed
   - You'll need these in the next step!

---

### Step 4: Configure Your Laravel Application

1. Open your `.env` file in the project root
2. Add these lines (replace with your actual credentials):

```env
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Example:**
```env
GOOGLE_CLIENT_ID=123456789-abcdefghijklmnop.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnopqrstuvwxyz
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

3. **Save the `.env` file**

---

### Step 5: Clear Configuration Cache

Run this command to clear Laravel's configuration cache:

```bash
php artisan config:clear
```

Or if you're using cached config:

```bash
php artisan config:cache
```

---

### Step 6: Test Google Sign-In

1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Navigate to: `http://localhost:8000/login`

3. Select **"Student"** portal

4. Click the **"Sign in with Google"** button

5. You should be redirected to Google's sign-in page

6. After signing in, you should be redirected back to your application

---

## 🔧 Troubleshooting

### Issue: "Redirect URI mismatch"

**Solution:**
- Make sure the redirect URI in your `.env` file **exactly matches** the one in Google Cloud Console
- Check for trailing slashes or http vs https
- The redirect URI should be: `http://localhost:8000/auth/google/callback`

### Issue: "Invalid client"

**Solution:**
- Double-check your `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in `.env`
- Make sure there are no extra spaces or quotes
- Run `php artisan config:clear` after updating `.env`

### Issue: "Access blocked: This app's request is invalid"

**Solution:**
- Make sure you've completed the OAuth consent screen setup
- If testing, add your email as a test user in the OAuth consent screen
- For production, you'll need to verify your app with Google

### Issue: Button doesn't appear or doesn't work

**Solution:**
- Check browser console for JavaScript errors
- Verify the route exists: `php artisan route:list | grep google`
- Make sure Socialite is installed: `composer show laravel/socialite`

---

## 🌐 Production Setup

When deploying to production:

1. **Update Google Cloud Console:**
   - Add your production domain to **Authorized JavaScript origins**
   - Add your production callback URL to **Authorized redirect URIs**
   - Example:
     ```
     https://yourdomain.com
     https://yourdomain.com/auth/google/callback
     ```

2. **Update `.env` file:**
   ```env
   GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
   ```

3. **Clear config cache:**
   ```bash
   php artisan config:cache
   ```

---

## 📝 Notes

- Google Sign-In only works for **Student** accounts (as configured in the code)
- New users signing in with Google will be automatically created as students
- The system will use the email from Google account
- If a user already exists with that email, they'll be logged in directly

---

## ✅ Verification Checklist

- [ ] Google Cloud project created
- [ ] Google+ API enabled
- [ ] OAuth consent screen configured
- [ ] OAuth 2.0 credentials created
- [ ] Client ID and Secret copied
- [ ] `.env` file updated with credentials
- [ ] Redirect URI matches in both places
- [ ] Configuration cache cleared
- [ ] Tested sign-in flow

---

## 🆘 Need Help?

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for errors
3. Verify all credentials are correct
4. Make sure your server is running on the correct port

