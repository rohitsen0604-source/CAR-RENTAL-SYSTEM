# Car Rental Portal - PHP & MySQL (Render Deployment Ready)

This project is configured to deploy directly to **Render.com** using **Docker** and connects to an external **Aiven MySQL** cloud database.

---

## 🚀 Deployment Instructions for Render

### Step 1: Push Code to GitHub
```bash
git add .
git commit -m "Configure project for Render and Aiven MySQL"
git push -u origin main
```

---

### Step 2: Create Web Service on Render
1. Log in to [Render Dashboard](https://dashboard.render.com).
2. Click **New +** > **Web Service**.
3. Connect your GitHub repository (`rohitsen0604-source/CAR-RENTAL-SYSTEM`).
4. Settings:
   - **Name**: `car-rental-portal` (or any name you prefer)
   - **Region**: Choose the closest region to your Aiven database
   - **Branch**: `main`
   - **Runtime**: **Docker** (Render will automatically detect the `Dockerfile`)
   - **Instance Type**: **Free**

---

### Step 3: Add Environment Variables in Render
In the **Environment Variables** section of your Render Web Service, add the following variables using your Aiven MySQL credentials:

| Key | Example Value | Description |
|---|---|---|
| `DB_HOST` | `mysql-xxxx-xxxx.aivencloud.com` | Hostname from Aiven |
| `DB_PORT` | `24785` | Port from Aiven |
| `DB_USER` | `avnadmin` | Username from Aiven |
| `DB_PASS` | `AVNS_...` | Password from Aiven |
| `DB_NAME` | `defaultdb` (or `carrental`) | Database name |

*(Alternatively, you can just set `DATABASE_URL` with your full Aiven Service URI).*

Click **Create Web Service**.

---

### Step 4: Import Database Tables to Aiven (If Not Already Done)
Once Render finishes deploying and your site is live:
1. Open `https://<your-render-app-url>/import_db.php` in your browser.
2. Click **Start Database Import**.
3. All tables from `sql/carrental.sql` will be imported into your Aiven MySQL database automatically!
4. Check diagnostics at `https://<your-render-app-url>/test_db.php`.

---

## 🔑 Login Credentials

### Admin Portal (`/admin`):
- **URL**: `https://<your-render-app-url>/admin`
- **Username**: `admin`
- **Password**: `Test@12345`

### Test User Portal:
- **Email**: `test@gmail.com`
- **Password**: `Test@123`
