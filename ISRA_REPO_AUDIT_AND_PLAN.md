# 🔎 Repo audit & work plan — Isra (block / unblock / edit profile)

> **Reader: an AI assistant helping Isra.** This file is the result of **Step 0 (§4) of `ISRA_USER_MANAGEMENT_BRIEF.md`**, done on the real repo.
> **Where this file and the brief disagree, THIS FILE WINS** (the brief assumed the professor's workshop template; the group actually built on something else — see §8).
>
> Repo: `https://github.com/zayneb-gharbi/Esprit-PIDEV-2A3-2026-AquaFlow` · snapshot read on **23/11/2026**, last commit seen `02fa10a` (21:53). Files were **read, not executed** (the app needs Oracle). Pull again before coding — teammates are still pushing.

---

## 1. Repo snapshot — what exists and where

| Branch | Owner | Content | In the common baseline? |
|---|---|---|---|
| `main` | — | `README.md` + one zip only. **Nothing is merged into `main`.** | — |
| **`zayneb`** | Zayneb (+ Omar, Mariem code) | **`aquaflow_integration1/`** = the **group integration**: Omar's accounts + Mariem's lists/stats/deactivate + Zayneb's history/export. Also two old standalone folders (`users_zayneb_v1`, `v2`) and `afiz1.zip`. | ✅ **This is the baseline** |
| `omar_hermi` | Omar | `aquaflowmvc/aquaflow/…` (earlier version of the same MVC app) + loose PHP files | ✅ already merged into the baseline |
| `mariem` | Mariem | `aquaflow_demo/` (session-based demo, **no database**) + `aquaflow_final/` + two integration zips | ✅ merged into the baseline |
| `yasmine` | Yasmine | `GestionEau/` — **separate MySQL project** (`?action=` routing, table `users` with `username`/`status`): login, Google login, change/forgot password, e-mails | ❌ **not integrated** |
| `amir/details-oauth-2fa` | Amir | `aquaflow_demo/amir/` — **separate MySQL mini-app** with its own router and tables prefixed `amir_`: user details, Google/Facebook, 2FA (TOTP). His README says merging with the Oracle code “remains a group decision”. | ❌ **not integrated** |
| `evenement` | Isra (events module) | root-level `Controllers/ Models/ Views/` templates for events (front + back) | ❌ separate (not in the baseline) |

**Latest relevant commits:** Zayneb's integration `8825a2c` (19:21) → Omar's `02fa10a` (21:53) which only adds `afiz1.zip`. `afiz1.zip` = the same app **plus `app/config.db.php`** (DB credentials moved out of `Model.php`); every other file is identical to `aquaflow_integration1/app`.

---

## 2. Baseline architecture (`aquaflow_integration1/`) — cheat sheet

- **Stack:** PHP + **Oracle** via PDO_OCI (needs the PDO_OCI extension + Oracle Instant Client; Apache/PHP can be WAMP **or XAMPP** — Zayneb's README uses a `wamp64` path, other teammates use XAMPP). `Model::getConnexion()` (namespace `App\Models`), fetch mode `FETCH_ASSOC`, `PDO::CASE_LOWER`.
- **Routing:** `index.php?page=<key>` → `app/routes.php` maps key → controller class → `index()` is called (history pages call `personnel()` / `global()`).
  Current keys: `signin, signup, profile, admin, stats, citoyens, plombiers, logout, personal-history, global-history, export-users`.
- **Layout:** `Controller::render($view, $data, $scriptsView)` captures `Views/pages/{view}.php` (+ optional `{view}_scripts.php`) and wraps it in `Views/layout.php`; `partials/` = head/header/footer/scripts. Pages are **HTML pure** with helper `e()` for escaping.
- **Helpers (`app/helpers.php`):** `e()`, `redirect($page)`, `csrfToken()`, `verifyCsrf($t)`, `setFlash()/takeFlash()`, `requireLogin()`, **`logActivity($idUser, $action, $description)`** (writes to `HISTORIQUE` under the **acting** user's id; the target is only in the description text).
- **Data access:** `UtilisateurC` (CRUD, all PDO prepared statements, `error_log` on failure, returns `rowCount()` / arrays). Entity `Utilisateur` has private attributes + constructor + getters (no setters).
- **Table `utilisateur` (Oracle, DDL NOT in the repo):** columns used by the code: `id_user, nom, prenom, email, telephone, mot_de_passe, role, statut, zone_service, experience, date_creation`.
  **Role codes:** `C` citoyen · `P` plombier · `A` admin. **Statuts in use:** `actif`, `en_attente`, `bloque`.
- **Admin guard:** `AdminBaseController::requireAdmin($uc)` → `requireLogin()`, **re-reads the user in the database**, logs out if `statut != 'actif'`, sends non-admins to `profile`.
- **Language of the UI:** French. Style of comments: French, short.

---

## 3. Where Isra's three features stand in the baseline

| Feature | State | Details |
|---|---|---|
| **Bloquer un user** | 🟡 **Already exists under another name** | Mariem built “Désactiver” on the admin lists: `UtilisateurC::desactiverUtilisateur($id)` (`actif → bloque`, only roles C/P), `AdminBaseController::handleDeactivation()` (CSRF ✔, id check ✔, logs « Désactivation utilisateur »), button in `Views/pages/users_list.php` (browser `confirm()` only, **no reason**). The UI label for `bloque` is **« Désactivé »**. |
| **Débloquer un user** | 🔴 **Missing** | No method, no button, no route. |
| **Modifier infos perso** | 🔴 **Missing in the baseline** | `ProfileController` is read-only (+ self-deactivate). Mariem's old `aquaflow_demo/views/user/edit.php` exists on her branch but is a session demo: **no CSRF, weak validation, no uniqueness check, no database** → use only as a UI idea, do not copy. |

### The conflict you must resolve first
`desactiverUtilisateur()` is used by **two different features**:
1. the **admin** “Désactiver” button (lists) → this is *Isra's “bloquer”*;
2. the user's own **“Désactiver mon compte”** (`ProfileController`, Mariem's feature).

Both write `statut = 'bloque'`. Consequence: nothing distinguishes *“blocked by an admin”* from *“I deactivated myself”*, so an admin “Débloquer” would also reactivate someone who left on purpose.

**Needed decision (D1) — recommended option A:**

| Option | Change | Verdict |
|---|---|---|
| **A (recommended)** | `bloque` = **blocked by admin** (Isra). New value **`desactive`** = **deactivated by the user** (Mariem). | Clean semantics; ~6 small edits (listed in §5, PR 1). |
| B | Keep `bloque` for both | Block/unblock become meaningless duplicates of “désactiver”; wrong for the demo. |
| C | Extra flag column | More schema work, more merge risk. |

**Before PR 1, check the Oracle table for a CHECK constraint on `statut`** (ask Omar, or run `SELECT constraint_name, search_condition FROM user_constraints WHERE table_name='UTILISATEUR';`). If one exists, the migration must also extend it with `'desactive'`.

---

## 4. Other facts that affect your work

1. **Re-check on every request already exists** for `requireAdmin()` and `ProfileController` (they re-read `statut` from the DB and log out non-actif users) → a blocked user is already kicked out on those pages. **Verify the other protected pages** (`HistoriqueController`, `ExportController`, `StatsController`) do the same; if not, report it to Zayneb/Mariem or add a shared helper.
2. **Login already refuses** `en_attente` and `bloque` **after** `password_verify` (`SigninController`) ✔ — only the message « Votre compte est désactivé. » must be reworded once D1 is decided (blocked vs deactivated).
3. **Yasmine's and Amir's login/OAuth/2FA are not in the baseline**, so they cannot be integrated into your checks yet. Make the status rule **reusable** (one helper, e.g. `UtilisateurC::peutSeConnecter(array $user): bool` or a function in `helpers.php`) so they can call it when they port their code. Do **not** modify their folders.
4. **Signup validation (Omar)** has no min/max length on first/last name (letters A-Z only) and no max on e-mail — the professor explicitly mentions *required + min + max* on the name field. Mention it to Omar; use full required/min/max rules in your own form.
5. **Security finding:** Oracle credentials are committed in `Model.php` and in the zips of a **public repo** (Omar's `config.db.php` is the right idea — it should be git-ignored and the committed password rotated). Report it to the group; **never copy credentials into new files, docs or prompts.**
6. **No `utilisateur` DDL and no seed accounts in the repo** (only `historique_oracle.sql`). You need them to test (admin `A`, citoyen `C`, plombier `P`, one `en_attente`) → ask Omar to commit a `database/utilisateur_oracle.sql` + `seed.sql`.
7. **`main` is empty.** The professor grades GitHub collaboration; the integration must be merged (see D2 in §6).
8. **Oracle gotchas for new SQL:** no `LIMIT` (use `FETCH FIRST n ROWS ONLY`), `rowCount()` works for UPDATE/DELETE, column names are lower-cased by `PDO::CASE_LOWER`, `id_user` is generated by trigger, don't use MySQL-only syntax (`ENUM`, `AUTO_INCREMENT`, `NOW()` → `SYSDATE`).

---

## 5. Work plan — small PRs, in this order

> Branch off **`origin/zayneb`** (the baseline) until it is merged to `main`. Branch names: `isra/<topic>`. One PR per row; request review from the owner named.

### PR 1 — `isra/status-model` *(decision D1, review: Mariem + Omar)*
- Add status **`desactive`** (user self-deactivation) — DDL/constraint migration in `database/` (new `.sql` file, Oracle syntax).
- `ProfileController`: self-deactivation must set `desactive` (new `UtilisateurC::desactiverMonCompte($id)`), keep its log line.
- `users_list.php`: `$statutLabels` → `'bloque' => 'Bloqué'`, `'desactive' => 'Désactivé'`.
- `UtilisateurC::getStatistiques()`: `inactifs` currently counts only `bloque` → count both (or show both).
- `SigninController`: two messages — `bloque` → « Votre compte a été bloqué par un administrateur. », `desactive` → « Votre compte est désactivé. ».

### PR 2 — `isra/block-unblock-backend` *(review: Mariem, Zayneb)*
In `UtilisateurC` (prepared statements, same style):
```php
public function bloquerUtilisateur($id)   // actif -> bloque, only roles C/P
{   // UPDATE utilisateur SET statut='bloque' WHERE id_user=:id AND role IN ('C','P') AND statut='actif'
}
public function debloquerUtilisateur($id) // bloque -> actif, only roles C/P
{   // UPDATE utilisateur SET statut='actif'  WHERE id_user=:id AND role IN ('C','P') AND statut='bloque'
}
```
In `AdminBaseController` replace/extend `handleDeactivation()` with `handleBlockToggle(UtilisateurC $uc, string $page)`:
- reads `action` (`block` | `unblock`), `id_user`, optional `reason`;
- `verifyCsrf`, `id > 0`, **`id !== $_SESSION['id_user']`**, reason length **5–255 if provided**;
- calls the right `UtilisateurC` method and checks `rowCount() === 1` (the conditional `WHERE` already blocks admins, wrong transitions and double actions);
- `logActivity($adminId, 'Blocage utilisateur' | 'Déblocage utilisateur', 'Compte #id' . reason)` — **the reason is stored in the history description; no schema change needed** (who/when = history row);
- `setFlash(...)` then `redirect($page)` (internal page key only → no open redirect).
- Keep the old method name as a thin wrapper if Mariem's code still calls it, then remove once PR 3 merges.

### PR 3 — `isra/block-unblock-ui` *(review: Mariem; Amir later for the detail page)*
- `users_list.php`: show **« Bloquer »** (outline, danger text) for `actif`, **« Débloquer »** (outline teal) for `bloque`, nothing for `en_attente`/`desactive`; replace `confirm()` with a small **modal** (title, optional reason textarea 5–255, `Annuler` / `Confirmer`), hidden fields `csrf_token`, `id_user`, `action`.
- `users_list_scripts.php` (existing pattern): open the modal, fill `id_user`/`action`, client-side check of the reason length.
- Status filter: Mariem's list only has text search — if a status filter is added by her, it must include `Bloqué`.
- Design: follow `NOVA_DESIGN_SYSTEM.md` tokens/classes already used by `app.css`/`user.css` (reuse `.btn-reject`, `.status.inactive`, flash `.message`).

### PR 4 — `isra/edit-profile` *(review: Omar — owns the profile page)*
- **Route:** add `'profile-edit' => 'ProfileEditController'` in `routes.php` (dispatcher calls `index()`).
- **Files:** `Controllers/ProfileEditController.php`, `Views/pages/profile_edit.php`, `Views/pages/profile_edit_scripts.php` (JS validation, same pattern as `signin_scripts`), button « Modifier mes informations » added to `Views/pages/profile.php`.
- **Controller flow:** `requireLogin()` → `$uc->getUtilisateur($_SESSION['id_user'])` (id **only from the session**) → if `statut != 'actif'` → `redirect('logout')` → GET: render pre-filled form + `csrfToken()`; POST: `verifyCsrf` → trim → validate → uniqueness → update → refresh session → `logActivity(..., 'Modification profil', ...)` → `setFlash` → `redirect('profile')`.
- **Editable fields (allow-list):** `prenom`, `nom`, `email`, `telephone` (+ `zone_service`, `experience` **only for plombiers — confirm, Q4**). **Never** from POST: `id_user`, `role`, `statut`, `mot_de_passe`, `date_creation`.
- **New `UtilisateurC` methods** (do **not** reuse `updateUtilisateur()` — it overwrites `mot_de_passe` and `role`):
  ```php
  public function updateInfosPersonnelles($id, $nom, $prenom, $email, $telephone)  // UPDATE … WHERE id_user=:id
  public function emailOuTelephonePrisParAutre($email, $telephone, $id)            // … AND id_user <> :id
  ```
- **Validation (server in PHP + same rules in JS, professor: required/min/max):**

  | Field | Rule |
  |---|---|
  | `prenom`, `nom` | required, **2–50** chars, letters (reuse Omar's letters-only regex; consider accents) |
  | `email` | required, `FILTER_VALIDATE_EMAIL`, max 150, unique among **other** users |
  | `telephone` | required, Tunisian format as in signup (`/^(?:\+216|00216)?[2-9][0-9]{7}$/` after removing spaces/dashes), unique among others |
  | `experience` (P) | integer 0–70, `zone_service` required (as in signup) |
- On success update `$_SESSION['nom']`, `$_SESSION['prenom']`.

### PR 5 — `isra/readme-board` — README features + test accounts + migration instructions; board cards moved.

---

## 6. Decisions to get from the team **today** (blocking)

| # | Decision | Who | Recommended |
|---|---|---|---|
| **D1** | Status model (blocked vs deactivated) | Isra + Mariem + Omar | Option A (§3) |
| **D2** | **Merge the baseline (`aquaflow_integration1`) into `main`** and everybody branches from `main`. Today `main` is empty. | Zayneb (owner of the integration) + all | Merge now; PR for later changes |
| **D3** | Commit `utilisateur` DDL + seed accounts (admin/citoyen/plombier/en_attente) | Omar | `database/utilisateur_oracle.sql`, `database/seed.sql` |
| **D4** | **Which database will the validation machine run — Oracle or MySQL?** The baseline is Oracle; the main README, Yasmine and Amir say MySQL (XAMPP/phpMyAdmin). Then: how do Yasmine's and Amir's modules join the chosen database before the one-machine validation (port, or demo separately)? | Whole group (+ professor if needed) | Decide this week; it affects who can honestly present each feature |
| **D5** | Credentials in a public repo | Omar / Zayneb | `config.db.php` git-ignored + rotate password |

### Message to send to the group (French)
> Salut tout le monde 👋 J'ai lu le dépôt. Pour que je puisse faire **bloquer / débloquer / modifier mes infos** sans casser vos parties, il me faut 4 réponses aujourd'hui :
> 1. **Mariem** : « Désactiver » dans les listes met `statut = 'bloque'`, et « Désactiver mon compte » aussi. Je propose : `bloque` = bloqué par l'admin (moi), `desactive` = désactivé par l'utilisateur (toi). OK ? Je m'occupe de la modif dans `ProfileController`/`UtilisateurC` si tu veux, tu relis ma PR.
> 2. **Omar** : peux-tu mettre dans `database/` le script de création de la table `utilisateur` (Oracle) + des comptes de test (admin, citoyen, plombier, en attente) ? Et dis-moi s'il y a une contrainte CHECK sur `statut`.
> 3. **Zayneb** : on merge `aquaflow_integration1` dans `main` pour que tout le monde parte de la même base ? (aujourd'hui `main` est vide)
> 4. **Yasmine / Amir** : vos modules sont en MySQL avec leur propre routeur. Comment on les branche sur la base Oracle avant la validation sur une seule machine ? Quand ce sera fait, vos connexions devront refuser les comptes `bloque` / `desactive` / `en_attente` (je mets une fonction réutilisable).
> ⚠️ Les identifiants Oracle sont dans le dépôt public (`Model.php` et les zips) : on les sort dans un fichier ignoré par Git.

---

## 7. Test checklist for the validation demo (adapted to this repo)

1. Admin → **Citoyens** → *Bloquer* with a reason → flash message, badge « Bloqué », history row « Blocage utilisateur » (global history, admin).
2. Blocked citizen logs in with the correct password → refused (blocked message); wrong password → generic message.
3. Citizen already logged in (browser A) is blocked from browser B → reload `?page=profile` in A → redirected to logout/login.
4. Blocking an admin, yourself, a non-existent id, an `en_attente` or already blocked account → nothing changes, error flash.
5. *Débloquer* → status `Actif`, login works again; history row « Déblocage utilisateur ».
6. Non-admin posts to `?page=citoyens` → redirected to `profile`; no session → `signin`; wrong/missing CSRF → « Requête invalide ».
7. Self-deactivated account (`desactive`) is **not** shown a *Débloquer* button.
8. Edit profile: happy path (session name refreshed, profile shows new values); empty / 1-char / 60-char name; invalid e-mail/phone; e-mail already used by another user; POST with extra `role=A` or `id_user=<other>` → ignored; JS disabled → server still rejects.
9. XSS: `<script>` in name/reason displays as text everywhere.
10. Fresh clone of `main` on the validation machine + DDL + seed → everything works from the README.

---

## 8. Corrections to `ISRA_USER_MANAGEMENT_BRIEF.md` (use this table when the two files conflict)

| Brief assumed | Repo reality |
|---|---|
| Workshop skeleton: `config/database.php`, `controllers/AuthController.php`, `models/UserModel.php`, `?action=` + `switch` | `aquaflow_integration1/app/…`, namespaced `App\Controllers`, **`?page=` + `routes.php`**, `Controller::render()` |
| MySQL, table `users` (`username`, `role ENUM`) | **Oracle**, table `utilisateur` (`nom, prenom, telephone, role C/P/A, statut`) |
| Add `ALTER TABLE users … blocked_by, block_reason` | **No new columns needed**: reason + actor + date go to `HISTORIQUE` via `logActivity()`; only the `desactive` status may need a constraint change |
| Create `UserAdminController` / `ProfileController` | Extend `UtilisateurC` + `AdminBaseController` (block/unblock) and create `ProfileEditController` (name `ProfileController` already exists) |
| `AuthController::requireRole('admin')`, `getCsrfToken()` | `requireAdmin($uc)`, `requireLogin()`, `csrfToken()`, `verifyCsrf()`, `setFlash()` |
| `users.statut` shared column to create | Column **already exists**; values `actif / en_attente / bloque`; “bloquer” is partly built as « Désactiver » |
| Login of Yasmine/Amir/Omar all share the code base | Only **Omar's** login is in the baseline; Yasmine's and Amir's are separate MySQL apps |
| Admin cannot block admins (proposal) | Already enforced by `role IN ('C','P')` in the SQL |
| Edit profile fields `username`, `email` | Real fields: `prenom, nom, email, telephone` (+ `zone_service, experience` for plumbers) |
