# German University Research Manager (Multi-User)

A PHP + MySQL web app for organizing research into German public
universities and their Master's programs. Multiple people can register
their own accounts and keep completely separate, private research data.
One account is the **admin**, who manages users; everyone else is a
**student**, who gets the full research dashboard for their own use.

```
Germany
 └── University            (each student's own, private to them)
      ├── University Information
      ├── Programs
      │    └── Program (Admission, Language, Fees, Deadlines, Documents, Links, Notes)
      └── Notes
```

## Roles

- **Student** (default role for anyone who registers): full access to
  their own dashboard, universities, programs — everything the original
  single-user app could do — but only ever sees and edits their own data.
- **Admin** (you): everything a student can do for their *own* research,
  **plus** an Admin Panel to see every registered user, activate/disable
  accounts, delete accounts (and all their data), and "View as" a
  student to see their dashboard exactly as they see it (useful for
  support). There is intentionally one bootstrap admin created on first
  run; see "Creating additional admins" below if you ever need more.

Each student's universities and programs are strictly isolated at the
database level — every query is scoped to `user_id`, so one student can
never read or modify another's data, even by guessing an ID in the URL
(this is enforced in `includes/functions.php` and was verified during
testing).

## Requirements

```
PHP 8.2+ with the pdo_mysql extension enabled
MySQL 5.7+ or MariaDB 10.3+ (needed for native JSON column support)
Apache, Nginx+PHP-FPM, XAMPP, or shared hosting with a MySQL database
```

## Installation

1. **Create a database and a database user.** On your own server:
   ```
   mysql -u root -p -e "CREATE DATABASE german_uni_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p -e "CREATE USER 'gum_app'@'localhost' IDENTIFIED BY 'choose-a-strong-password';"
   mysql -u root -p -e "GRANT ALL PRIVILEGES ON german_uni_manager.* TO 'gum_app'@'localhost';"
   ```
   On shared hosting (cPanel etc.), create the database and a database
   user through the hosting control panel instead — the interface is
   different everywhere, but the end result is the same: a database
   name, a username, and a password.

2. **Import the schema:**
   ```
   mysql -u gum_app -p german_uni_manager < schema.sql
   ```

3. **Edit `config.php`** with your database host, name, username, and
   password.

4. **Copy the whole folder** to your web server (e.g. `htdocs/` for
   XAMPP, or your `public_html/` on shared hosting).

5. **Open the app in your browser.** Since the `users` table is empty,
   you'll automatically land on a one-time setup screen:
   ```
   http://localhost/german-university-manager/
   ```
   Fill in your name, email, and password — this becomes the **admin**
   account. From then on, `setup.php` refuses to run again (it only
   works while the database has zero users), so this can't be repeated
   by someone else later.

6. Anyone else who needs an account goes to `register.php` and creates
   their own **student** account.

### Nginx note

If you're using Nginx + PHP-FPM instead of Apache, the `.htaccess` file
in `includes/` is ignored (Apache-only). To block direct web access to
that folder, add this to your server block:

```nginx
location ~ ^/includes/ {
    deny all;
}
```

## How data is stored

Everything lives in three MySQL tables (see `schema.sql`):

- **`users`** — name, email, hashed password, role (`admin`/`student`),
  status (`active`/`disabled`).
- **`universities`** — one row per university a user is researching,
  tagged with `user_id`.
- **`programs`** — one row per Master's program, tagged with both
  `user_id` and `university_id`. The detailed research sections
  (Language, Fees, Application, Admission, Documents, Links, Personal
  notes) are stored as JSON columns, so the structure stays flexible
  without needing a dozen extra tables.

Passwords are hashed with PHP's `password_hash()` (bcrypt) — never
stored in plain text. Every form submission is protected by a CSRF
token, and every database query that touches a university or program is
scoped to the owning `user_id`.

## Using the app (students)

This is unchanged from the original single-user version — see the
in-app pages themselves, which are self-explanatory:

- **Dashboard** — stats, upcoming deadlines, recently updated items,
  high-priority programs, and anything needing verification.
- **University Explorer** / **Program Explorer** — searchable,
  filterable, sortable tables.
- **Tree sidebar** — expand/collapse each university to jump to a
  specific program.
- **Search** — across university and program names, notes, and research
  fields.
- **Import / Export** — download a JSON backup of *your own* data, or a
  CSV for Excel/Sheets. Importing a JSON file restores it into *your own*
  account only (merge or replace) — it never touches other users' data.

## Using the Admin Panel (you)

Log in with your admin account and click your name (top right) →
**Admin Panel**, or use the "Admin Panel" link in the sidebar. From
there you can:

- See totals across every user (students, universities, programs).
- See each user's name, email, role, status, and how much data they
  have.
- **Activate / Disable** an account — a disabled account is immediately
  logged out and can't log back in until re-activated (they'll see a
  clear message explaining why).
- **Delete** an account — this permanently deletes that user and *all*
  of their universities and programs (confirmed before it happens; you
  cannot delete your own account or the last remaining admin this way).
- **View as** (the eye icon) — temporarily see and edit that student's
  dashboard exactly as they would. A yellow banner stays at the top of
  every page while you're doing this, with a one-click "Return to Admin
  Panel" button. This never logs the student out or affects their own
  session — it's purely for you to look at (or fix) their data if they
  ask for help.

Admins also get their own personal dashboard (click "My Own Dashboard"
or just navigate normally) — being an admin doesn't take away your
ability to research universities for yourself.

### Creating additional admins

There's no UI for promoting a second admin, to avoid accidentally
creating one by mistake. If you ever need another admin, run this once
against the database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'someone@example.com';
```

## Project structure

```
german-university-manager/
├── config.php                    → DB credentials (edit this first)
├── schema.sql                    → run once to create the MySQL tables
├── setup.php                     → one-time bootstrap admin creation
├── login.php / register.php / logout.php
├── index.php                     → routes to setup/login/dashboard as appropriate
├── dashboard.php, universities.php, university.php,
│   programs.php, program.php, search.php, import-export.php
│                                  → the student-facing app (scoped to
│                                     whichever user is "effective" —
│                                     see includes/auth.php)
│
├── admin/
│   ├── index.php                  → user list + global stats
│   ├── user-toggle-status.php     → activate/disable a user
│   ├── user-delete.php            → delete a user (cascades their data)
│   ├── impersonate.php            → admin "View as" a student
│   └── stop-impersonate.php
│
├── actions/                       → POST-only handlers, all scoped to
│                                     effective_user_id() with ownership
│                                     checks, redirect back when done
│
├── includes/
│   ├── helpers.php                → escaping, CSRF, redirects, badges
│   ├── db.php                     → PDO connection
│   ├── auth.php                   → sessions, login, roles, impersonation
│   ├── functions.php              → all MySQL data access lives here
│   ├── header.php / footer.php / sidebar.php
│   └── .htaccess                  → blocks direct web access
│
├── assets/
│   ├── css/style.css
│   └── js/app.js
│
└── README.md
```

## Security notes

- Passwords hashed with bcrypt (`password_hash`/`password_verify`).
- Every university/program/document/link database query is scoped to
  `user_id`; ownership is re-checked on every read and write, not just
  assumed from the URL.
- CSRF tokens on every state-changing form.
- Disabled accounts are rejected at both login and on every subsequent
  page load (in case someone is already logged in when disabled).
- SQL is entirely parameterized (PDO prepared statements) — no string
  concatenation into queries.
- `includes/` is blocked from direct web access via `.htaccess`
  (Apache) — see the Nginx note above if you're not using Apache.
- There is deliberately no self-service way to become an admin — the
  very first account created via `setup.php` is the only admin unless
  you promote someone else directly in the database.

This is appropriate for a small group (a class, a friend group, a
family) managing their own research. It has not been hardened for
public internet-facing deployment with untrusted signups (e.g. no email
verification, no rate limiting on login attempts) — if you expose this
publicly, consider adding those, or put it behind a shared login page
at the server level.

## Testing performed during development

- First-run setup creates exactly one admin; running `setup.php` again
  afterward correctly refuses and redirects to login. ✅
- Two independent student accounts registered, each created their own
  university/program; neither could see the other's data in any explorer
  or search. ✅
- A student directly requesting another student's university by URL
  (`university.php?id=<someone else's id>`) was blocked and redirected,
  not shown any data. ✅
- Admin login lands on the admin's own personal dashboard; the Admin
  Panel is one click away and shows accurate global stats. ✅
- Admin "View as" a student correctly showed that student's exact data,
  with a banner and working "Return to Admin Panel" link; the admin's
  own dashboard was unaffected before and after. ✅
- Disabling a user immediately blocks their next login with a clear
  message, and logs out any existing session on their next request. ✅
- Deleting a user cascades correctly — their universities and programs
  are removed from the database along with the account. ✅
- Admin cannot delete their own account or the last remaining admin. ✅
- CSRF and login/role checks verified on both regular actions and admin
  actions. ✅
