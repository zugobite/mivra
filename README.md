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

# Quickstart

**1) Clone into your web server directory**
```bash
git clone https://github.com/zugobite/mivra.git
```

**2) Copy example `.env.example` config file into `.env`**
```bash
cp .env.example .env
```

## Running Project Locally

If you have PHP installed, you can run Mivra locally without any extra tools:

**1) Run this command in your terminal from the project root**
```bash
php -S localhost:8000 -t public
```

**2) Then visit**
```bash
http://localhost:8000
```

---

# Deploy to DigitalOcean

Read the full [DigitalOcean Deployment Instructions](/deploy/README_DEPLOY.md).

---

# License

This project is open‑source and available under the [MIT License](/LICENSE.txt).
