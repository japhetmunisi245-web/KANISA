# ⛪ E.A.G.T Kiburugwa Philadelphia Church — Management System

A web-based Church Management and Department Administration System built with **Laravel 12**, **PostgreSQL**, **Vite**, and **Beem Africa SMS Gateway**.

---

## ✨ Key Features

- 👥 **Membership Management:** Member registration, role assignments, department affiliations, and directory search.
- 🏢 **Department Leadership Portals:** Dedicated leader dashboards for tracking department members, meetings, attendance, and contribution campaigns.
- 📱 **Beem Africa Bulk SMS:** Integrated bulk SMS gateway with automated department tags (`[IDARA YA VIJANA]`, `[IDARA YA WANAWAKE]`, etc.) using the approved `KANISA` sender ID.
- 📜 **Official Church Letters & Verification:** Digital letter drafting, pastoral approvals, and public QR code document verification.
- 💰 **Contributions & Financials:** Pledge campaigns, offering records, and downloadable PDF receipts with DomPDF.
- 📊 **Executive Church Office:** Pastoral and office oversight for meetings, duty schedules, and church-wide announcements.

---

## 🚀 Quick Setup (Local Development)

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL

### Installation

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd KANISA
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` to configure your PostgreSQL credentials and Beem Africa keys.*

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start Development Servers:**
   ```bash
   npm run dev
   php artisan serve
   ```

---

## 🌐 Production Hosting & Deployment

For a full guide on deploying this system for **1,000+ members** on Railway, Docker, or a VPS (Hetzner / DigitalOcean):

👉 **Read the [Hosting & Deployment Guide](HOSTING_GUIDE.md)**

---

## 📜 License
Proprietary software developed for E.A.G.T Kiburugwa Philadelphia Church. All rights reserved.
