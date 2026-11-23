# 🚀 START HERE — which file to use, in which order

## The 5 files

| # | File | Use it for | Give it to the AI when… |
|---|---|---|---|
| 1 | **`ISRA_REPO_AUDIT_AND_PLAN.md`** | The **real state of the repo** (Oracle, `?page=` routing, what teammates built) + your step-by-step PR plan + the message to send to the group. **Overrides the brief when they conflict.** | **Always, first** — for block / unblock / edit profile |
| 2 | **`ISRA_USER_MANAGEMENT_BRIEF.md`** | Requirements and acceptance criteria for your 3 user-management features, security + validation rules, Git/Project Board workflow, demo tests | **Always, second** (together with #1) |
| 3 | **`NOVA_DESIGN_SYSTEM.md`** | Colors, fonts, layout, components → so every new page looks identical | When the AI **creates or changes a page / view** |
| 4 | **`AQUAFLOW_MODULES_SPEC.md`** | Explanation of the 6 modules and the **detailed spec of Gestion des événements** (pages, sample data, checklist, demo script) | When working on the **events module** (your graded part) or needing module context |
| 5 | **`TODO_Semaine_Prochaine.md`** | Weekly task list: GitHub repo rules (name, topics, README), diagrams, template integration steps | For the **GitHub / README / diagrams** side; ticking tasks off |

> Files you already have and may also give the AI: the professor's `php_project_professor_notes_for_ai.md`, the workshop PDF *Sessions et Sécurité*, and the PlantUML diagrams for the events module (`diagrammes_evenements/`).

## Order of work (short version)

1. Send the team message (`ISRA_REPO_AUDIT_AND_PLAN.md` §6) — blocking questions: status model, Oracle vs MySQL, `main` merge, DDL/seed accounts.
2. Pull the repo, branch from `origin/zayneb`, run the app locally.
3. PR 1 → PR 5 of the plan (status model → block/unblock backend → UI → edit profile → README/board).
4. In parallel, keep the events module moving (`AQUAFLOW_MODULES_SPEC.md` §4) and move your Project Board cards.

## Copy-paste starter prompt for the AI

> I am Isra, student at Esprit. Read `ISRA_REPO_AUDIT_AND_PLAN.md` first, then `ISRA_USER_MANAGEMENT_BRIEF.md` (the audit overrides the brief where they conflict). Repo: https://github.com/zayneb-gharbi/Esprit-PIDEV-2A3-2026-AquaFlow (baseline = `aquaflow_integration1/` on branch `zayneb`). Do **not** invent anything the files mark as a proposal or open question. Start with **PR 1** (status model) — first show me the exact files you will change and the SQL for Oracle, wait for my confirmation of decision D1, then write the code file by file with a short explanation of what/why/security for each. Follow the project's existing code style (French comments, `UtilisateurC`, `AdminBaseController`, `helpers.php`).

*(Replace “PR 1” with the PR you are working on. For page design, also attach `NOVA_DESIGN_SYSTEM.md`. For the events module, attach `AQUAFLOW_MODULES_SPEC.md`.)*

## Known caveats

- The audit was done on the repo state of **23/11/2026 ~22:00**; teammates were still pushing — `git pull` and re-check before coding.
- The **database question (Oracle vs MySQL)** is still open; Oracle-specific SQL in the plan must be adapted if the group chooses MySQL.
- Anything marked **(proposal)** is a suggestion, not a professor requirement.
