# Security Policy

This document outlines how security vulnerabilities should be reported and handled for the Mivra framework.

---

## 1. Supported Versions

The following versions of Mivra are currently supported with security updates:

| Version | Supported       |
| ------- | --------------- |
| Latest  | Fully supported |
| Older   | Not supported   |

If you are using an unsupported version, you are strongly encouraged to upgrade to the latest release to ensure security patches are applied.

---

## 2. Reporting a Vulnerability

If you discover a security vulnerability in Mivra:

1. **Do not** publicly disclose the vulnerability until it has been reviewed and fixed.
2. Contact the project maintainers directly: support@mivra.co.za
3. Include the following information in your report:
   - A clear description of the vulnerability.
   - Steps to reproduce the issue.
   - The potential security impact.
   - Any recommended fixes or mitigations (if available).
   - Your environment details (PHP version, database type, OS).
4. We will acknowledge receipt of your report within **48 hours**.

---

## 3. Response Process

- **Initial Triage** - The maintainers will verify the report and assess severity.
- **Mitigation** - A patch or mitigation will be developed and tested.
- **Release** - A security release will be published, and the vulnerability will be disclosed responsibly.
- **Acknowledgment** - Credit will be given to the reporter if they wish to be acknowledged.

---

## 4. Security Best Practices for Contributors

All contributors should:

- Use **prepared statements** for all database queries.
- Validate and sanitize all user input.
- Avoid exposing sensitive data in views or API responses.
- Never store credentials or secrets in the repository.
- Follow the **MVC architecture** to maintain separation of concerns.
- Perform local security testing before submitting pull requests.

---

## 5. Disclosure Policy

- Vulnerabilities will be disclosed publicly only after a fix has been released.
- High-severity vulnerabilities may result in immediate out-of-cycle security releases.
- Public disclosure will include:
  - A description of the issue.
  - The severity rating.
  - Instructions for upgrading or patching.
  - Credit to the reporter (if applicable).

---

## 6. Legal Safe Harbor

If you follow this policy in reporting a vulnerability, we will not pursue legal action against you for your findings.  
You are expected to avoid:

- Exploiting the vulnerability for personal gain.
- Compromising the privacy or security of others.
- Disrupting or damaging production systems.

---

Maintaining the security of Mivra is a priority.  
We appreciate the efforts of the security community in helping keep the framework safe for all users.
