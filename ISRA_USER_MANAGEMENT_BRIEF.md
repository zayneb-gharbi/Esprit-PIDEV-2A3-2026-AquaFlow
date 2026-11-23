# 🧭 Brief for the AI — “Gestion des utilisateurs” (Isra's part)

> ⚠️ **Read `ISRA_REPO_AUDIT_AND_PLAN.md` FIRST.** It is the audit of the real repo (Oracle, `?page=` routing, `utilisateur` table…) and **overrides this brief wherever they conflict** (see its §8). This brief remains the reference for the requirements, security rules, validation rules, Git workflow and test ideas.


> **Reader: an AI coding/documentation assistant.** This file tells you what the student **Isra** must deliver for this week's validation session, how it must be built, and how it must integrate with her teammates' work.
> Read §0 first. Do not skip §4 (audit the repo before writing any code).

---

## 0. Rules for you (the AI)

1. **Do not invent** requirements, table/column names, routes or files that already exist elsewhere. Anything marked **(proposal)** is a suggestion that must be checked against the real repo and confirmed with the team.
2. **Audit before coding.** Teammates have already pushed work to a shared repo. Run §4 first and adapt to what exists (naming, folder layout, language of the UI, helper methods). If you cannot access the repo, **ask the user for the repo URL, a zip, or a file tree** — do not guess its content.
3. **Isra only owns three features** (§2). Teammates own the rest. Never rewrite a teammate's file wholesale; propose a minimal, clearly-scoped change and say which teammate must review it.
4. **Follow the professor's workshop** (`workshop_Session_Sécurité`): strict MVC, PDO prepared statements, `password_hash`, sessions, CSRF, `htmlspecialchars`, role checks on **every** sensitive page. Details in §6.
5. **Server-side validation is mandatory**; JavaScript validation complements it, HTML `required` alone is not enough (§8).
6. **Explain your code** so a student can present and defend it (what it does, why it is safe). Label recommendations as *suggestions*, not as professor requirements.
7. Keep the **GitHub workflow** in mind (§10): branches, small commits, board cards. The professor grades individual contributions.

---

## 1. Context

### 1.1 What is being graded this week

The professor's brief for the module **Gestion des utilisateurs** asks for:

- **Complete user management**: CRUD, roles, **authorization / access control** by role, all business features of the module — all in **MVC**.
- **Security and sessions**: follow the workshop guide.
- **Input validation** on **every** form (PHP and/or JavaScript). Professor's notes: name field **required**, **min length**, **max length**; worth **3 points**.
- **Teamwork on GitHub** (worth **3 points**): tasks tracked on the **GitHub Project Board** (To do / In progress / Done), integration with **branches, commits, merges**, individual contributions **traceable**; a well-documented `README.md`; strict repo naming convention.
- **Validation happens on ONE machine per group** → everything must be merged and working together there.
- Technical notes from the professor: **PDO**, **OOP** (attributes, constructor, getters/setters, methods; interfaces possibly), **three actors: Citoyen, Plombier, Admin**.

### 1.2 Project

**AquaFlow** — a water-leak management platform (citizens report leaks, plumbers repair, admin supervises). This week's module is the **user management** module shared by all other modules. UI language is **French** (see `NOVA_DESIGN_SYSTEM.md` and `AQUAFLOW_MODULES_SPEC.md` if they are available in the repo or conversation).

### 1.3 Who does what (given by the professor/team)

**Admin side — “gérer admin”**

| Feature | Owner |
|---|---|
| Consulter l'historique total | Zayneb |
| Consulter les détails d'un user | Amir |
| Approuver le compte d'un user | Omar |
| **Bloquer un user** | **Isra** |
| **Débloquer un user** | **Isra** |
| Consulter la liste des citoyens (recherche par nom, filtre par statut) | Mariem |
| Consulter la liste des plombiers | Mariem |
| S'authentifier (admin) | Yasmine |
| Exporter la liste plombiers/citoyens en Excel | Zayneb |
| Consulter les statistiques user | Mariem |

**User side — “gérer user”**

| Feature | Owner |
|---|---|
| Consulter son historique | Zayneb |
| Changer son mot de passe | Yasmine |
| Reset du mot de passe (notification par e-mail) | Yasmine |
| Créer un compte | Omar |
| Se connecter / se déconnecter | Omar |
| Consulter ses informations personnelles | Omar |
| **Modifier ses informations personnelles** | **Isra** |
| Désactiver mon compte | Mariem |
| Se connecter via Google / Facebook | Amir |
| Authentification à deux facteurs (Google Authenticator) | Amir |

---

## 2. Isra's scope (what must be delivered)

### 2.1 Feature A — Bloquer un utilisateur *(Admin)*

**User story:** as an administrator, I can block a citizen or a plumber so that they can no longer use the platform.

**Acceptance criteria**

- [ ] A **“Bloquer”** action is available on each eligible row of the citizens list and plumbers list (Mariem) and on the user-details page (Amir).
- [ ] Clicking it opens a **confirmation step** (modal or confirmation page) with an optional **reason** field.
- [ ] The request is a **POST** with a **CSRF token**; access is restricted with `requireRole('admin')` (HTTP 403 otherwise, redirect to login if not authenticated).
- [ ] Business rules enforced **in the controller** (not only hidden in the UI):
  - the target user must exist;
  - an admin **cannot block himself**;
  - an admin **cannot block another admin** *(proposal — confirm with the professor/team)*;
  - only an account currently **actif** can be blocked *(proposal — see §5 and open question Q2)*;
  - an already-blocked account cannot be blocked again.
- [ ] Effect: the account's status becomes **bloqué**; the block date, the admin who blocked and the optional reason are stored.
- [ ] A **blocked user cannot log in** (login checks the status **after** verifying the password, so the existence of an account is never leaked) and an **already-open session stops working at the next request** (status re-checked in the database — this is exactly the workshop's “question de réflexion” about checking roles only at login).
- [ ] The action is **recorded in the history** (Zayneb's feature) — see §3.
- [ ] A clear **flash message** confirms success or explains the error; the admin is redirected back to where he came from (whitelisted targets only — no open redirect).
- [ ] The status badge **“Bloqué”** is visible in lists and details, and “bloqué” is a value of the **status filter** (Mariem).

### 2.2 Feature B — Débloquer un utilisateur *(Admin)*

**User story:** as an administrator, I can unblock a previously blocked account so the user can use the platform again.

**Acceptance criteria**

- [ ] Same entry points, protection (POST + CSRF + `requireRole('admin')`) and feedback as Feature A, with an **“Débloquer”** action shown **instead of** “Bloquer” for blocked accounts.
- [ ] Only an account currently **bloqué** can be unblocked; the status goes back to **actif** *(proposal)*; `blocked_at`, `blocked_by`, `block_reason` are cleared (the history keeps the trace).
- [ ] The action is recorded in the history.
- [ ] After unblocking, the user can log in again with the same password.

### 2.3 Feature C — Modifier ses informations personnelles *(any authenticated user: citoyen, plombier, admin)*

**User story:** as a logged-in user, I can edit my own personal information.

**Acceptance criteria**

- [ ] A **“Modifier mes informations”** button/link on the personal-info page (Omar's page) leads to an edit form **pre-filled** with the current values.
- [ ] The user to update is **always taken from the session** (`$_SESSION['user_id']`), **never** from a form field or URL parameter (prevents editing someone else's account — IDOR).
- [ ] **Editable fields = the personal-info fields that exist in the real `users` table** (see §4). Per the workshop schema these are at least **username** and **email**; if the team added `nom`, `prénom`, `téléphone`, `adresse`… include them too.
- [ ] **Never editable here:** `id`, `role`, `statut`, `created_at`, `password` (password change belongs to Yasmine). The controller must ignore any such key sent in `$_POST` (no mass assignment).
- [ ] Validation on **server (PHP) and client (JavaScript)** — rules in §8; inline error messages; previously typed values are kept after an error (`$old`).
- [ ] Uniqueness checks on username/email **excluding the current user's own row**.
- [ ] POST + **CSRF token**; all output escaped with `htmlspecialchars`.
- [ ] On success: update the database, **refresh `$_SESSION['username']`** (and any other cached value), show a success message, redirect to the info page.
- [ ] Only accounts with status **actif** can use this feature (a blocked/disabled account is logged out by the status re-check).
- [ ] The update is recorded in the history *(optional but recommended — see §3)*.

---

## 3. Integration with teammates (read carefully)

Isra's features touch several teammates' code. **Agree on these points before coding** and write the agreement in the repo's README or an issue.

| With | Integration point | What Isra needs / provides |
|---|---|---|
| **Omar** (create account, login, logout, approve, personal info) | `users.statut` column and `UserModel` | **Shared status column.** Omar's “approve” sets `en_attente → actif`; Isra's block/unblock use `bloque`. New accounts must start as `en_attente` (or the team's chosen value). Omar's **login** must refuse `bloque` (and `en_attente`/`desactive`) **after** the password check. The “Modifier mes informations” link is added to Omar's info page. |
| **Yasmine** (admin authentication, change/reset password) | Authentication flow, `requireAuth()` | The **admin login** must also refuse blocked/disabled accounts. `requireAuth()` (or a shared helper) should **re-read the status from the database** on protected requests so a blocked user is kicked out immediately. Password is excluded from Isra's edit form. If e-mail is used for password reset, changing the e-mail in Feature C must keep reset working (note it in the PR). |
| **Mariem** (lists, filters, statistics, deactivate my account) | Citizens/plumbers lists, status filter, `desactive` value | Add the **Bloquer/Débloquer** button in her rows; the status filter must include `bloque`; statistics may count blocked users. **Blocked ≠ deactivated:** *bloqué* = decision of an admin; *désactivé* = decision of the user (her feature). They must be **two different status values** and the UI must show them differently. |
| **Amir** (user details, Google/Facebook login, 2FA) | Details page, alternative login paths | Add the Bloquer/Débloquer button and the status badge on the details page. **Google/Facebook login and 2FA must apply the same status check** (a blocked user must not get in through OAuth either). |
| **Zayneb** (history, Excel export) | History table/service | Isra needs a function to log actions such as `USER_BLOCKED`, `USER_UNBLOCKED`, `PROFILE_UPDATED` with: actor id, target user id, action code, optional details (reason), timestamp. **Reuse Zayneb's mechanism if it exists**; otherwise agree on one signature with her *(proposal: `log(int $actorId, ?int $targetId, string $action, ?string $details = null)`)*. The Excel export should include the status column. |

**Merge-conflict warning:** `UserModel.php`, `AuthController.php`, `index.php` (the `switch`), the lists and `users` table are touched by several people. Rules:
- Push the **small shared change first** (the `statut` attribute + SQL migration) to a short-lived branch and have it merged **before** everyone builds on it.
- Put Isra's logic in **her own files** (§7) so most merges are trivial.
- Only add `case` blocks to `index.php`; don't reorder or reformat the file.

---

## 4. Step 0 — Audit the repo before writing code

The user has a repo containing teammates' work and will give you access (URL, zip or file tree). If you have **no access**, stop and ask for it. Then produce a short **audit report** with these points:

1. **Folder layout and conventions:** is it the workshop layout (`config/database.php`, `controllers/`, `models/`, `views/`, `index.php`) or a different structure (e.g. `app/Controllers`, `app/Views/partials`, `layout.php` from the template-integration workshop)? Namespaces? File naming (`AuthController.php`, `UserModel.php`…)? **Follow what exists.**
2. **Database access:** is there a `Database` singleton with `getConnexion()` (the workshop spells it that way) or another class? Which DB name? Is there a **schema/SQL file** under version control?
3. **`users` table:** exact columns and types today — roles (`user/admin` from the workshop, or `citoyen/plombier/admin` as the professor's “3 acteurs” imply), is there a **status** column, personal-info columns, any history table?
4. **Existing features:** which of the teammates' features are already implemented, partially implemented, or missing (use the table in §1.3 as the checklist).
5. **Auth & session code:** `isAuthenticated()`, `requireAuth()`, `requireRole()`, CSRF helpers, `login()` — are statuses already checked? Where would a status re-check go?
6. **Routing:** how `index.php` routes (`?action=` + `switch`, or a `routes.php`), and the list of existing actions (to avoid collisions).
7. **Views/layout:** which layout/template is used for front and back office, how flash messages and error lists are displayed, CSS/JS locations, UI language.
8. **Validation helpers:** is there any shared validation code or JS file to reuse?
9. **Git state:** branches, naming style, recent commits, open PRs, README content, Project Board columns.
10. **Conflicts & risks:** duplicated logic, files several people edit, security gaps you notice (list them; fix only what is in Isra's scope, report the rest).

> **Output of Step 0:** a concise report + the **final list of files to create/modify** and the **questions that remain** (see §12). Wait for the user's answers on blocking questions before coding.

---

## 5. Data model (proposal — adapt to the real table)

The workshop's table only has `id, username, email, password, role('user','admin'), created_at`. Isra's features need a **status** and block metadata. **Check §4 first; if a status column already exists, reuse it and its values.**

```sql
-- database/migrations/2026-11-xx_add_user_status_and_block.sql   (proposal)
ALTER TABLE users
  ADD COLUMN statut ENUM('en_attente','actif','bloque','desactive') NOT NULL DEFAULT 'en_attente',
  ADD COLUMN blocked_at   DATETIME     NULL,
  ADD COLUMN blocked_by   INT          NULL,
  ADD COLUMN block_reason VARCHAR(255) NULL,
  ADD CONSTRAINT fk_users_blocked_by
      FOREIGN KEY (blocked_by) REFERENCES users(id) ON DELETE SET NULL;

-- Existing accounts must be given an explicit status:
UPDATE users SET statut = 'actif';
```

| Status | Meaning | Set by |
|---|---|---|
| `en_attente` | account created, waiting for admin approval | creation (Omar) |
| `actif` | approved, can use the platform | approval (Omar) / unblock (**Isra**) |
| `bloque` | blocked by an administrator | block (**Isra**) |
| `desactive` | deactivated by the user himself | “désactiver mon compte” (Mariem) |

**Rules**

- **All schema changes go into a versioned `.sql` file in the repo** (not only phpMyAdmin) — validation runs on one machine that must be able to rebuild the DB from the repo.
- Role values: if the team uses the workshop's `user/admin` only, the “3 acteurs” (Citoyen, Plombier, Admin) still need to be reconciled — see Q1. Do **not** change the role ENUM unless the team has agreed.
- `UserModel` stays a pure model: **only** private attributes + constructor + getters/setters + `fromArray()`. Add `statut`, `blockedAt`, `blockedBy`, `blockReason` the same way as the existing attributes.

---

## 6. Architecture & coding rules (from the professor's workshop)

**MVC, strictly:**

| Layer | May contain | Must NOT contain |
|---|---|---|
| **Model** | private attributes, constructor, getters/setters, `fromArray()` | SQL, business logic |
| **Controller** | **all** logic: PDO prepared statements, validation, hashing, sessions, CSRF, role checks | HTML output |
| **View** | HTML + display logic only, `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')` on every dynamic value | SQL, business rules |
| **index.php** | session setup, security headers, routing (`switch` on `?action=`) | business logic |

**Always:**

- **PDO** with **named-parameter prepared statements**; connection through the project's `Database` singleton; `ERRMODE_EXCEPTION`. No string-concatenated SQL.
- **Typed code** (`declare(strict_types=1)`, typed parameters/returns, as in the workshop).
- Methods that read the session are encapsulated in the controller (`isAuthenticated()`, `requireAuth()`, `requireRole('admin')`) — **no scattered `$_SESSION` checks in views**.
- Role/status are **re-checked server-side on every sensitive request**; hiding a button is never protection.
- State-changing operations (block, unblock, edit profile) use **POST + CSRF token** (`getCsrfToken()` / `verifyCsrfToken()` with `hash_equals`) — never GET links.
- Generic, non-leaking error messages for authentication.
- Keep the workshop's **session hardening** (`use_strict_mode`, `cookie_httponly`, `cookie_samesite=Lax`, `session_regenerate_id(true)` after login, 30-minute inactivity expiry) and **security headers**.
- **OOP concepts to show** (professor): attributes, constructor, getters/setters, methods; use an **interface** where natural (e.g. a small `HistoryLogger` interface implemented by Zayneb's/Isra's logger) — *only if the professor confirms interfaces are expected (Q6)*.

---

## 7. Implementation plan (suggested files and routes)

> Names below are **proposals**. Reuse the repo's existing naming if it differs (§4).

**Isra's own files (minimise merge conflicts):**

| File | Purpose |
|---|---|
| `controllers/UserAdminController.php` *(or a section inside the existing controller if the team has a single one)* | `blockUser()`, `unblockUser()` + status update helper |
| `controllers/ProfileController.php` | `getProfileForEdit()`, `updateProfile()` + validation helpers |
| `views/profile_edit.php` | edit-my-info form (front office layout) |
| `views/partials/block_modal.php` *(or equivalent)* | reusable confirmation modal for Bloquer/Débloquer, included by Mariem's and Amir's pages |
| `assets/js/profile-validation.js` | client-side validation for the edit form |
| `database/migrations/…_add_user_status_and_block.sql` | schema change |

**Shared files Isra must touch minimally (coordinate in PRs):** `models/UserModel.php` (new attributes), `index.php` (new `case`s), the login method(s) (status check — **owned by Omar/Yasmine/Amir**, propose the change to them), list/detail views (button + badge — **owned by Mariem/Amir**).

**Routes (proposal)**

| `?action=` | Method | Who | Purpose |
|---|---|---|---|
| `user_block` | POST | admin | block a user (`user_id`, `reason`, `csrf_token`, `redirect`) |
| `user_unblock` | POST | admin | unblock a user |
| `profile_edit` | GET | any authenticated | show the pre-filled form |
| `profile_edit` | POST | any authenticated | validate + update, then redirect |

**Controller method contract (signatures only):**

```php
// Admin side — every method starts with AuthController::requireRole('admin')
public function blockUser(int $adminId, array $post): array     // returns list of error messages ([] = success)
public function unblockUser(int $adminId, array $post): array
private function changeStatus(int $targetId, string $newStatus, int $adminId, ?string $reason): bool

// Profile side — user id comes from $_SESSION, never from $post
public function getProfileForEdit(int $userId): ?UserModel
public function updateProfile(int $userId, array $post): array  // returns errors ([] = success)
private function isEmailOrUsernameTakenByOther(string $email, string $username, int $excludeId): bool
```

The `changeStatus` update should be **conditional** (e.g. `UPDATE … SET statut = 'bloque' … WHERE id = :id AND statut = 'actif'`) and check the affected-row count, so two admins acting at the same time cannot produce an inconsistent state.

---

## 8. Validation rules (professor: required + min length + max length, server **and** client)

Apply on **every** form Isra creates. HTML `required` is a complement only — the server decides.

| Field | Rule | Message (French) |
|---|---|---|
| `username` | required; regex `^[a-zA-Z0-9_\-]{3,50}$` (workshop rule: 3–50 chars) | « Le nom d'utilisateur doit contenir 3 à 50 caractères (lettres, chiffres, _ ou -). » |
| `nom`, `prénom` *(if these columns exist)* | required; **min 2, max 50**; letters (Unicode), spaces, hyphen, apostrophe | « Ce champ est obligatoire (2 à 50 caractères). » |
| `email` | required; `filter_var(FILTER_VALIDATE_EMAIL)`; max 150; unique (excluding self) | « Adresse e-mail invalide. » / « Cette adresse est déjà utilisée. » |
| `téléphone` *(if it exists)* | optional or required per team; format to confirm *(suggestion: 8 digits for Tunisia)* | « Numéro de téléphone invalide. » |
| `reason` (block) | optional; if provided **min 5, max 255** characters; stored escaped on output | « Le motif doit contenir entre 5 et 255 caractères. » |
| `user_id` (block/unblock) | integer > 0, user must exist | « Utilisateur introuvable. » |
| `csrf_token` | present and equal to the session token | « Jeton de sécurité invalide. Veuillez réessayer. » |

- Always `trim()` inputs first; reject unexpected/extra keys silently (use an **allow-list** of editable fields).
- **Client side (JavaScript):** same rules, inline messages under each field, submit disabled/blocked while invalid. Put `novalidate` on the form so the JS messages are the ones shown, and keep `required`/`minlength`/`maxlength` attributes as a fallback. A short unit of the same rules in PHP must exist — **never rely on JS alone**.
- Show errors with the project's existing error component and **keep the previous input** (`$old`).

---

## 9. Security checklist for Isra's code

- [ ] Prepared statements everywhere; no concatenated SQL.
- [ ] Output escaped with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` (names, reasons, e-mail, flash messages).
- [ ] CSRF token in every POST form (block/unblock modals included) and verified with `hash_equals`.
- [ ] `requireRole('admin')` at the very start of block/unblock; `requireAuth()` for profile edit; 403 or redirect as in the workshop.
- [ ] Target of block/unblock validated server-side (exists, allowed transition, not self/admin).
- [ ] Profile update uses the **session** id; role/status/id/password keys ignored.
- [ ] Redirect after action goes only to a **whitelist** of internal pages.
- [ ] A blocked user's active session is terminated on the next request; login path (all variants: user, admin, Google/Facebook, 2FA) refuses non-`actif` accounts.
- [ ] No sensitive data (hashes, tokens) in flash messages, logs or HTML.
- [ ] Block/unblock/profile-update recorded in history with actor id and timestamp.

---

## 10. GitHub workflow (graded — 3 points)

- [ ] Work in **feature branches**, one per feature, with the author's name or the feature in the name: `feature/isra-block-user`, `feature/isra-unblock-user`, `feature/isra-edit-profile`, `feature/isra-status-migration`.
- [ ] **Small, frequent commits** with meaningful messages (e.g. `feat(users): add blocked status and migration`, `feat(users): block user with CSRF and admin check`, `fix(profile): exclude own row in uniqueness check`). Contributions must be **visible per person** in the history.
- [ ] Open a **pull request** per branch, request review from the owner of any file touched (see §3), merge into `main` only when it works with the others' code. Resolve conflicts, don't overwrite.
- [ ] **GitHub Project Board** with columns **To do / In progress / Done**; create one card per feature and per sub-task (migration, controller, view, JS validation, integration, tests, README); move cards as work progresses; link PRs to cards.
- [ ] Update `README.md`: project description, **features list** (add block/unblock and edit profile), installation (clone, DB creation, **running the migration SQL**, config of `database.php`), run instructions, test accounts (admin / citoyen / plombier), contributors. Follow the Esprit repo standardisation (name `Esprit-[PI]-[Classe]-[AU]-[NomDuProjet]`, description, topics, README structure with *Overview, Features, Tech Stack, Architecture, Contributors, Academic Context, Getting Started, Acknowledgments*).
- [ ] **Validation is on one machine:** do a **fresh clone + fresh DB** test of `main` on that machine before the session.

---

## 11. Manual test scenarios (use for the demo)

**Prerequisites:** one admin, one actif citoyen, one actif plombier, one en_attente account.

1. **Block (happy path):** admin → list → *Bloquer* on the citizen → enter a reason → confirm → success message; badge “Bloqué”; history entry exists.
2. **Blocked login refused:** the citizen tries to log in with the correct password → refused with a clear message; with a wrong password → generic “identifiants incorrects”.
3. **Live session killed:** log in as the citizen in browser A, block him from browser B (admin), reload a protected page in A → redirected to login.
4. **Rule checks:** admin tries to block himself / another admin / an already-blocked user / a non-existent id → rejected with a message, nothing changes.
5. **Unblock:** admin → *Débloquer* → status “actif”, user can log in again; history entry exists.
6. **Access control:** a citizen posts to `user_block` (or opens its URL) → 403; unauthenticated → redirected to login; POST without/with a wrong CSRF token → refused.
7. **Edit profile (happy path):** user edits his info → success; session name updated; the info page shows the new values.
8. **Edit profile (errors):** empty name, 2-char name, 60-char name, invalid e-mail, e-mail used by another account → each shows the right message on **JS and PHP** (disable JS to prove the server check).
9. **Tampering:** add `role=admin` or `user_id=<other>` to the edit POST → ignored; own data only.
10. **XSS:** block reason or name containing `<script>alert(1)</script>` is displayed as text.
11. **Filters/lists:** “Bloqué” appears in Mariem's status filter; blocked users are included/identified in the Excel export and statistics.

---

## 12. Open questions (ask the user/team/professor — do not decide silently)

- **Q1.** Roles: does the DB use `user/admin` (workshop) or `citoyen/plombier/admin` (the “3 acteurs”)? Which is the final convention? (The plumber's exact label is unclear in the professor's notes.)
- **Q2.** Single `statut` column (proposal) or a separate `is_blocked` flag? Can a **pending** account be blocked? What status does an unblocked account return to?
- **Q3.** Can an admin block another admin? (Proposal: no.)
- **Q4.** Which fields count as “informations personnelles” in the real `users` table, and which are editable? Is the e-mail editable (impact on reset-password and OAuth)?
- **Q5.** Which history mechanism exists/will exist (Zayneb) and what signature should Isra call?
- **Q6.** Are **interfaces** mandatory for the OOP criterion? What does “1 commit”/“1 seule machine” require exactly (professor's handwritten notes are ambiguous; the email states validation happens on **one machine per group**)?
- **Q7.** Should a user be notified (e-mail) when blocked/unblocked? *(Not required by the brief — optional.)*
- **Q8.** UI language and layout of the new pages: reuse the repo's current layout, or the AquaFlow/NOVA templates (`NOVA_DESIGN_SYSTEM.md`)?

---

## 13. UI notes (consistent with the project's design system, if used)

- French copy: *Bloquer*, *Débloquer*, *Bloqué*, *Motif (facultatif)*, *Confirmer le blocage*, *Annuler*, *Modifier mes informations*, *Enregistrer les modifications*, « Le compte de {nom} a été bloqué. », « Le compte de {nom} a été débloqué. », « Vos informations ont été mises à jour. »
- Buttons: **Bloquer** = outline button with danger-colored text (never a filled red button); **Débloquer** = outline teal; primary actions = teal button with dark-navy bold text.
- Status pills: *Actif* green, *En attente* amber, *Bloqué* neutral grey (or danger-outline) and distinct from *Désactivé* — keep the same colors on every page.
- Confirmation modal: icon tile, title « Bloquer cet utilisateur ? », explanatory text, optional reason textarea, buttons `Annuler` + `Confirmer le blocage`.
- Edit form: card, labels above inputs, helper text for limits (« 3 à 50 caractères »), inline error text under the field, primary button `Enregistrer les modifications` + outline `Annuler`.

---

## 14. Definition of done (Isra)

- [ ] Features A, B, C meet all acceptance criteria in §2.
- [ ] Integration points in §3 agreed and implemented with the owners; no regression in their features.
- [ ] Migration SQL versioned; a fresh clone + DB setup works from the README.
- [ ] Validation present on server **and** client for every form; tests in §11 pass.
- [ ] Security checklist §9 fully ticked.
- [ ] Branches, commits, PRs and Project Board cards reflect Isra's work; README updated.
- [ ] Isra can explain every line (MVC separation, why prepared statements, why CSRF, why the status is re-checked, how the history is written).

---

## 15. How to answer the user

1. Start with the **Step 0 audit** (or ask for repo access).
2. Then give a **short plan** (files, order, who to coordinate with).
3. Then produce code **file by file**, each with a 2–3 line explanation (“what/why/security”).
4. End with the **test checklist** to run and the **PR description** text (what changed, which teammate's files are touched, how to test).
5. Mark anything uncertain as **(proposal)** and list what you need confirmed.
