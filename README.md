# Mivra - Lightweight PHP Framework

Mivra is a **barebones, lightweight PHP framework** designed for developers who want simplicity, speed, and minimal overhead. It’s ideal for very small micro‑websites, such as **portfolio sites, landing pages, personal blogs**, or any project where a full‑scale framework would be overkill.

---

## Purpose

- **Minimal complexity:** Only the essentials are included - no heavy dependencies, no unnecessary layers.
- **Fast and responsive:** Lightweight routing and MVC structure for quick load times.
- **Easy to deploy:** Built with simple configuration and a `public/` web root for secure hosting.
- **Customizable:** A clean starting point for adding only the features you actually need.
- **Beginner friendly:** Easy to understand for new developers while still flexible for experienced ones.

---

## When to Use Mivra

- Personal portfolio websites,
- Single‑page product or event landing pages,
- Simple informational business sites,
- Micro‑projects or MVPs,
- Educational projects to learn basic PHP MVC concepts.

---

## Features

- Custom PHP router (supports static and parameterized routes),
- MVC pattern for clear separation of concerns,
- `.env` environment configuration loader,
- PDO‑based database connection with prepared statements,
- Simple view templating with reusable components,
- Ready‑to‑use deployment scripts for DigitalOcean Ubuntu droplet.

---

## Why Not a Full‑Stack Framework?

Frameworks like Laravel or Symfony are powerful, but they come with a lot of features you may never use in a small site. Mivra keeps only what’s necessary, so you:

- Write less boilerplate,
- Reduce server resource usage,
- Maintain full control over the code.

---

# Deploying to a DigitalOcean Droplet

This guide shows two reliable ways to deploy the **Mivra** lightweight PHP framework on a fresh Ubuntu 22.04/24.04 droplet.

- **Path A (fast):** use the provided scripts in `deploy/`

> Replace values in **ALL CAPS** with your own (e.g., `YOUR_DROPLET_IP`, `mivra.example.com`).

---

## Prerequisites

- A DigitalOcean droplet running **Ubuntu 22.04 or 24.04** (Basic plan is fine)
- Your project files on GitHub
- A subdomain pointing to the droplet (e.g., `mivra.example.com`)
- GitHub CLI installed on the droplet to clone private repos:
  ```bash
  apt-get install -y gh
  gh auth login
  ```

---

## DNS Setup (before you start)

Create these **DNS records** at your DNS provider (where your domain's nameservers live):

| Type              | Host/Name | Value               | TTL |
| ----------------- | --------- | ------------------- | --- |
| A                 | `mivra`   | `YOUR_DROPLET_IPV4` | 300 |
| AAAA _(optional)_ | `mivra`   | `YOUR_DROPLET_IPV6` | 300 |

> If you **don’t** use IPv6, **do not** create an AAAA record. If one exists pointing elsewhere, delete it. IPv6 pointing to the wrong server will cause Let’s Encrypt failures.

---

## Path A - Quick Install with Provided Scripts

**1) SSH into the droplet**

```bash
ssh root@YOUR_DROPLET_IP
apt-get update && apt-get upgrade -y
```

**2) Get the app onto the server**

- Using **git**:
  ```bash
  cd /var/www && git clone https://github.com/YOU/mivra.git
  cd /var/www/mivra
  ```

**3) Run the setup script**

```bash
cd /var/www/mivra
```

If you hit "php: command not found", install php-cli first:

```bash
apt-get install -y php-cli
sudo ./deploy/setup_ubuntu.sh
```

This installs **Nginx + PHP‑FPM**, PHP extensions, firewall rules, and the site config.

**4) Set your domain in Nginx**

```bash
sudo sed -i 's/server_name YOUR_DOMAIN_OR_IP;/server_name mivra.example.com;/' deploy/nginx.mivra.conf
sudo cp deploy/nginx.mivra.conf /etc/nginx/sites-available/mivra.conf
sudo ln -sf /etc/nginx/sites-available/mivra.conf /etc/nginx/sites-enabled/mivra.conf
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

**5) Create .env file**

```bash
cp .env.example .env
nano .env
```

Set APP*URL=https://mivra.example.com and DB*\* values

**6) Permissions & reload**

```bash
sudo chown -R www-data:www-data /var/www/mivra
sudo find /var/www/mivra -type f -exec chmod 0644 {} \;
sudo find /var/www/mivra -type d -exec chmod 0755 {} \;
PHPV=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
sudo systemctl reload php${PHPV}-fpm
sudo systemctl reload nginx
```

**7) HTTPS with Let’s Encrypt**

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d mivra.example.com
```

Done. Visit `https://mivra.example.com`.

---

## Verifying the Deployment

```bash
curl -I http://mivra.example.com
curl -I https://mivra.example.com
```

You should see `HTTP/1.1 200` (HTTP) and `HTTP/2 200` (HTTPS).

Logs to check:

- Nginx: `/var/log/nginx/mivra.access.log`, `/var/log/nginx/mivra.error.log`
- PHP‑FPM: `/var/log/php*-fpm.log`

---

## Updating the App (Zero‑Downtime Rsync)

From your workstation:

```bash
rsync -a --delete --exclude '.git' ./ root@YOUR_DROPLET_IP:/var/www/mivra/
ssh root@YOUR_DROPLET_IP "PHPV=\$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;'); systemctl reload php\${PHPV}-fpm; systemctl reload nginx"
```

Or on the droplet using the provided helper:

```bash
cd /var/www/mivra
sudo ./deploy/deploy_local_to_server.sh
```

---

## Common Pitfalls & Fixes

- **Let’s Encrypt fails with**: Your **AAAA (IPv6)** record likely points elsewhere. Delete bad AAAA or set it to the droplet’s real IPv6.
- **502 Bad Gateway**: FastCGI socket mismatch. Update `fastcgi_pass` to the actual `phpX.Y-fpm.sock` and reload Nginx.
- **404 on every route**: Ensure `root /var/www/mivra/public;` and `try_files $uri $uri/ /index.php?$query_string;` are present.
- **Blank page**: Temporarily set `APP_DEBUG=true` in `.env`, test again, then turn it back **off** in production.
- **Nginx ignores**: Expected - `.htaccess` is for Apache only. Nginx uses the server block above.

---

## Security & Hardening Tips

- Keep `APP_DEBUG=false` in production
- Apply security updates regularly or enable unattended upgrades
- Limit SSH to key auth; disable password logins
- Restrict write perms; only `www-data` should own files served by Nginx
- Rotate logs and back up your DB regularly

---

## Switching Domains Later

- Update DNS A/AAAA for the new subdomain
- Change `server_name` in `/etc/nginx/sites-available/mivra.conf`
- Update `APP_URL` in `.env`
- Re‑issue SSL: `certbot --nginx -d new.example.com`

---

## Uninstall / Rollback

Disable and remove the site

```bash
rm -f /etc/nginx/sites-enabled/mivra.conf
rm -f /etc/nginx/sites-available/mivra.conf
nginx -t && systemctl reload nginx
```

(Optional) remove app

```bash
rm -rf /var/www/mivra
```

---

# License

This project is open‑source and available under the MIT License.
