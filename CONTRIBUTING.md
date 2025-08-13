# Contributing to Mivra

Thank you for your interest in contributing to this project.  
This document outlines the rules, processes, and standards for contributing to maintain code quality, security, and consistency.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Do’s](#dos)
3. [Don’ts](#donts)
4. [Code Style and Standards](#code-style-and-standards)
5. [Branching and Commit Conventions](#branching-and-commit-conventions)
6. [Testing and Quality Assurance](#testing-and-quality-assurance)
7. [Pull Request Process](#pull-request-process)
8. [Security and Privacy](#security-and-privacy)
9. [License](#license)

---

## Getting Started

- Fork the repository and create a new branch before making any changes.
- Ensure you have the following installed:
  - PHP 8.0 or higher
  - MySQL
- Copy `.env.example` to `.env` and configure your environment variables.
- Apply database migrations from `/database/migrations` before starting development.
- All development should be done using only PHP, JavaScript, and CSS.
- No external dependencies or frameworks are to be introduced.

---

## Do’s

- Follow the **MVC architecture**:
  - Controllers: Handle HTTP requests and pass data to Views.
  - Models: Contain business logic and database interactions.
  - Views: Contain presentation logic only (HTML, CSS, JavaScript).
- Keep logic separated:
  - Do not place business logic in Views.
  - Do not make direct database queries in Controllers - use Models.
- Use environment variables for all sensitive data; never hardcode secrets.
- Write clear, maintainable, and self-documenting code.
- Include PHPDoc and JSDoc comments for all functions, classes, and methods.
- Maintain consistency with the existing CSS theme and design system.
- Use prepared statements for all database queries.
- Test all changes locally before committing.
- Ensure proper authentication and authorization checks on protected pages.

---

## Don’ts

- Do not commit directly to the `main` branch; use a feature branch.
- Do not store credentials, API keys, or secrets in the codebase.
- Do not mix inline styles with CSS files.
- Do not commit generated files (e.g., `/uploads/`, `/database/.migrated.json`).
- Do not break backward compatibility without prior discussion.
- Do not push code with debug statements (`var_dump`, `console.log`, etc.).
- Do not bypass the Router - all routes must be registered in `Router.php`.
- Do not add third-party dependencies or frameworks.

---

## Code Style and Standards

**PHP**

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- Use namespaces for all classes.
- Indent using 4 spaces.

**JavaScript**

- Use ES6+ syntax where applicable.
- Indent using 2 spaces.
- Wrap all scripts in `DOMContentLoaded` or equivalent to ensure proper execution.

**CSS**

- Follow BEM naming conventions.
- Use existing CSS variables where possible.
- Group related properties logically for readability.

---

## Branching and Commit Conventions

**Branch naming**:

- `feature/<short-description>` for new features.
- `fix/<short-description>` for bug fixes.
- `docs/<short-description>` for documentation changes.
- `refactor/<short-description>` for code structure improvements without functional changes.

**Commit messages**:  
Follow [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/):

- `feat(auth): add password reset feature`
- `fix(feed): correct post visibility issue`
- `docs: update README with setup instructions`

---

## Testing and Quality Assurance

- Test features on multiple screen sizes, including desktop and mobile.
- Test on the latest versions of Chrome, Firefox, and Safari.
- Ensure no PHP warnings or notices are present.
- Run SQL migrations on a clean database before submission.
- Verify that authentication and access control functions correctly.

---

## Pull Request Process

1. Create a draft pull request early if feedback is required.
2. Ensure all tests pass and code follows the defined style guidelines.
3. Provide a clear, descriptive summary of the changes made.
4. Link related issues in the pull request description.
5. Address review feedback before final approval.
6. Squash commits where appropriate before merging.

---

## Security and Privacy

- Do not expose sensitive information in commits or issues.
- Report security vulnerabilities directly to the project maintainers.
- Follow relevant privacy regulations when handling user data.

---

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](/LICENSE.txt).
