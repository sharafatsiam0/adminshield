# Trustline WP

**Zero-Trust security layer for WordPress admin powered by Cloudflare Access**

Trustline WP is a lightweight security plugin that protects your **/wp-admin** and login area by allowing access only through Cloudflare Trust / Zero-Trust verification.
It helps block direct login attempts, bots, and unauthorized users before WordPress loads.

---

## ✨ Features

* Protects **wp-admin** behind Cloudflare Access
* Blocks direct public access to admin panel
* Allows only verified users/devices
* Email-based access restriction
* IP allowlist support
* Basic audit logging
* Lightweight and developer-friendly

---

## 🚀 How It Works

Trustline WP checks Cloudflare Access headers on every admin request.

If the request is not authenticated through Cloudflare, the plugin blocks access immediately.

Example headers:

* `CF-Access-Authenticated-User-Email`
* Cloudflare IP verification
* Optional allowlist rules

---

## 📦 Installation

1. Download the plugin
2. Upload to:

```
/wp-content/plugins/trustline-wp
```

3. Activate from **WordPress → Plugins**
4. Configure settings

---

## ⚙️ Basic Usage

After activation:

1. Create a Cloudflare Access application for your WordPress admin
2. Enable authentication (Google, GitHub, email, etc.)
3. Open Trustline WP settings
4. Add allowed emails or IPs
5. Enable protection

Now only authenticated users can open **wp-admin**.

---

## 🔐 Example Protection Logic

```php
add_action('admin_init', function () {
    if (!isset($_SERVER['HTTP_CF_ACCESS_AUTHENTICATED_USER_EMAIL'])) {
        wp_die('Access blocked — Trustline required');
    }
});
```






---

## 🎯 Use Cases

* Agencies protecting client admin panels
* Developers securing staging sites
* SaaS dashboards built on WordPress
* High-traffic sites reducing brute-force attacks

---

## 🤝 Contributing

Pull requests and suggestions are welcome.

If you find a bug, open an issue with steps to reproduce.

---

## 📄 License

GPL v2 or later

---

## 👤 Author

Siam
WordPress Developer (Learning & Building)

---

## ⭐ Vision

Trustline WP aims to bring **Zero-Trust security** to WordPress in a simple, developer-friendly way — turning admin panels into private infrastructure instead of public login pages.
