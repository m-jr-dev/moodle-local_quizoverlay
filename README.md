# Quiz Overlay (local_quizoverlay)

Provides management of **quiz access overlays per user and course** in Moodle.

The plugin allows administrators to define custom quiz availability rules for specific users, including time restrictions, attempt limits, and access passwords. It also supports **bulk creation of overlays through CSV import** and provides an administration interface to manage existing records.

---

## Requirements

- Moodle 5.0+.

---

## Installation

1. Copy this folder to: `local/quizoverlay`
2. Go to **Site administration → Notifications**

---

## Features

- Creation of **quiz overlays per user**
- Allows definition of:
  - Custom quiz opening time
  - Custom quiz closing time
  - Attempt limits
  - Quiz reference name
- Support for **user-specific quiz passwords**
- **Bulk import via CSV file**
- Administrative interface for managing overlay records
- Integration with Moodle administration navigation

---

## Configuration

There is no configuration page.

The plugin provides administration pages for importing and managing overlay records.

Pages provided:

- `/local/quizoverlay/index.php` — CSV import interface
- `/local/quizoverlay/manage.php` — overlay management page

---

## Capabilities

The plugin defines the capability below in `db/access.php`:

- `local/quizoverlay:manage` — Defined in system context (allowed archetype `manager`)

Users with this capability can:

- Access the plugin navigation entry
- Import CSV files
- Manage quiz overlay records

---

## Data and Privacy (GDPR)

The plugin creates its own database tables to store overlay configurations and user passwords.

Tables created:

- `local_quizoverlay`
- `local_quizoverlay_upass`

### Privacy API

Implements:

- `\core_privacy\local\metadata\provider`
