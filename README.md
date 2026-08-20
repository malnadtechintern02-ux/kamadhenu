# Kamadenu Goushala – Modern PHP/MySQL Website

A production-ready website and content management system developed for **Kamadhenu Goushala** (Kavadi, Virajpet Taluk, Kodagu, Karnataka), dedicated to the protection and preservation of indigenous Indian cow breeds (*Gir, Hallikar, Malenadu Gidda, Amritamahal, Tharparkar*).

---

## 🌟 Technology Stack

- **Backend:** PHP 8.2+ with PDO (MySQL / MariaDB)
- **Frontend:** HTML5, Bootstrap 5.3, Bootstrap Icons, Vanilla JavaScript, Custom CSS Design System
- **Database:** Normalized Relational Database (18 tables with Foreign Keys, Cascades & Indices)
- **Web Server:** Apache (Clean URL rewrite engine via `.htaccess`, XAMPP / LAMP / cPanel ready)

---

## 🚀 Key Features

### 1. Public Portal
- **Modern Homepage:** Hero section with badges, animated statistics counters, mission story, seva categories, featured cows, indigenous breeds showcase, upcoming events, latest blog news, products catalog, and donor testimonials.
- **Indigenous Cow Sanctuary:** Searchable cow registry with breed filtering, age calculation, medical status, rescue stories, and direct adoption links.
- **Indigenous Breeds Encyclopedia:** Comprehensive profiles on native Indian cow breeds, A2 beta-casein milk benefits, origin geography, and physical characteristics.
- **Seva & Donation System:**
  - Online contribution flow with preset & custom amounts.
  - Dedicated **Gau Grass Seva** (Feed a cow daily/monthly packages).
  - Dedicated **Adopt a Cow** program with interactive duration calculator and digital **Adoption Certificate**.
  - 80G tax exemption PAN collection & instant digital receipt generation.
  - Direct Trust Bank Account and UPI details.
- **News & Community Events:** Blog with category filtering, SEO meta tags, social sharing, and upcoming yagnas/event registration.
- **Products Catalog:** Cow-based products (Ghee, Dhoop, Panchagavya) with direct **Order on WhatsApp** integration.
- **Photo Gallery:** Lightbox modal photo viewer with categorized filters.
- **Contact & Location:** Interactive contact form with server-side validation, WhatsApp quick chat, and Google Maps embed.

### 2. Admin Management Panel (`/admin`)
- **Secure Authentication:** CSRF token verification, password hashing (`bcrypt`), and session-based auth guards.
- **Analytics Dashboard:** Live metrics for total cows, total donations received, active guardians, and pending inquiries.
- **Content CRUD:**
  - Cows management with image uploads & adoptable toggles.
  - Indigenous Breeds management with auto-slug generator.
  - Seva programs with customizable suggested amounts.
  - Events management with scheduling and registration links.
  - Blog & News editor with tags and custom SEO meta fields.
  - Photo Gallery uploader with instant previews.
  - Products catalog with stock tracking.
  - Testimonial manager.
- **Transactions & Communications:**
  - Full Donations Ledger with transaction IDs and receipt printer.
  - Adoptions registry with Guardian details and Certificate viewer.
  - Inquiries inbox with single-click email and WhatsApp reply actions.
- **System Settings:** Update site titles, impact counters, social links, and official bank/UPI details in real-time.

---

## 📁 Directory Structure

```text
kamadhenu/
├── admin/                      # Complete Admin Management Panel
│   ├── includes/               # Admin auth guard, header, sidebar, footer
│   ├── index.php               # Admin Dashboard & Analytics
│   ├── cows.php & cow-form.php # Cow registry CRUD
│   ├── breeds.php & form       # Breed profiles CRUD
│   ├── seva.php & form         # Seva offerings CRUD
│   ├── events.php & form       # Events CRUD
│   ├── news.php & form         # News/Blog CRUD
│   ├── gallery.php & upload    # Gallery album manager
│   ├── products.php & form     # Products catalog CRUD
│   ├── donations.php & details # Donations ledger
│   ├── adoptions.php & details # Cow adoptions registry
│   ├── messages.php & details  # Inquiries inbox
│   ├── settings.php            # Site-wide settings
│   ├── profile.php             # Password & Profile
│   ├── login.php & logout.php  # Admin auth
├── assets/
│   ├── css/                    # style.css (Public Design System), admin.css
│   ├── js/                     # main.js (Public JS), admin.js
│   ├── images/                 # Favicon, banners, static assets
├── config/
│   ├── config.php              # Global site paths & helpers
│   ├── database.php            # Secure PDO database connector
├── database/
│   ├── schema.sql              # 18-table relational MySQL schema
│   ├── seed.sql                # Production seed data
├── includes/
│   ├── auth.php                # Admin session helpers
│   ├── csrf.php                # CSRF protection
│   ├── flash.php               # User notification alerts
│   ├── functions.php           # Formatting, DB wrappers & helpers
│   ├── header.php, navbar.php, footer.php # Core public layouts
│   ├── pagination.php          # Accessible Bootstrap pagination
│   ├── seo.php                 # Schema.org & dynamic meta tags
│   ├── upload.php              # Secure file & image uploader
│   ├── validation.php          # Server-side validation engine
├── uploads/                    # Upload directories for cows, breeds, news, etc.
├── index.php                   # Homepage
├── about.php                   # About Sanctuary & History
├── cows.php & cow-details.php  # Cow profiles & details
├── breeds.php & breed-details  # Indigenous breeds encyclopedia
├── gau-seva.php                # Seva offerings
├── feed-a-cow.php              # Dedicated Gau Grass Seva
├── adopt-a-cow.php             # Dedicated Cow Adoption
├── donate.php                  # Donation portal
├── donation-success.php        # Printable donation receipt
├── adoption-success.php        # Printable adoption certificate
├── events.php & event-details  # Events
├── news.php & news-details     # News & Blog
├── gallery.php                 # Photo Gallery
├── products.php & details      # Products Catalog
├── contact.php                 # Contact & Map
├── privacy-policy.php & terms  # Legal Pages
├── 404.php, 403.php, 500.php   # Error handlers
├── sitemap.php & robots.txt    # Search engine optimization
└── .htaccess                   # Apache URL rewriting & security headers
```

---

## 🛠️ Installation & Setup (XAMPP / LAMP)

1. **Clone / Copy Files:**
   Place the `kamadhenu` directory into your web root (e.g., `C:/xampp/htdocs/kamadhenu`).

2. **Import Database:**
   - Create a MySQL database named `kamadhenu`.
   - Import `database/schema.sql`.
   - Import `database/seed.sql`.

3. **Configure Database Connection:**
   If your MySQL credentials differ from `root` / empty password, adjust `config/database.php`.

4. **Default Admin Credentials:**
   - **URL:** `http://localhost/kamadhenu/admin/login.php`
   - **Username:** `admin`
   - **Password:** `admin123`
   *(Please update your password in Admin Profile upon first login).*

---

## 🔒 Security Measures Implemented
- **SQL Injection Prevention:** 100% Prepared Statements via PDO.
- **Cross-Site Request Forgery (CSRF):** Unique cryptographic tokens validated on all state-altering requests.
- **Cross-Site Scripting (XSS):** HTML output escaping via `htmlspecialchars()` helper `e()`.
- **Session Security:** Strict cookie parameters (`HttpOnly`, `SameSite=Lax`, regenerate ID on login).
- **Directory Protection:** `.htaccess` blocks direct browser access to `/config`, `/database`, and system logs.
- **File Upload Security:** MIME-type validation and randomized filenames.
