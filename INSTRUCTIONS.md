# Student Information System (SIS) — Laravel Project Instructions

> **Purpose:** Build a simple, functional, beginner-friendly Student Information System for the laboratory activity **“Designing and Integrating a Computer-Based Information System.”** This file is the single source of truth for scope, architecture, development, quality, and submission requirements. Read `AGENTS.md` for team roles and workflow.
>
> **Main priority:** Demonstrate that the **user interface, application server, authentication module, network connection, and database** work together. Build the smallest secure system that meets the requirements—**not** an enterprise school platform.

## 1. Source requirements and scope

The provided laboratory handout describes a school SIS for **students, teachers, and administrators**, with these six functions:

1. Student registration.
2. Student profile management.
3. Subject enrollment.
4. Grade management.
5. Database storage.
6. User authentication.

The handout's programming challenge is **optional** and shows a frontend-only HTML/JavaScript registration demo. **This project intentionally goes further** by connecting registration to a real Laravel backend and MySQL database. Do not confuse that extension with an instructor requirement to deploy a complete production service.

### Essential lab artifacts (must be prepared)

- [ ] **Part A — Components identification table:** User Interface, Application Layer, Database Layer, Network Layer, Hardware Layer, Security Layer; provide a description and concrete example for each.
- [ ] **Part A guide question:** Explain why each component matters in an integrated information system.
- [ ] **Part B — Architecture diagram:** Show **Student, Teacher, Administrator, User Interface, Application Server, Database Server, Network Connection, and Authentication Module**. Draw directional arrows, name each component, label its function, and make the request/response path clear. Export as **PNG or PDF**, ideally from diagrams.net (Draw.io). Suggested diagram name: `Student_System_Architecture`.
- [ ] **Part C — Student registration demonstration:** Student submits information -> Laravel receives the request -> server validates inputs -> database stores the student -> UI shows confirmation. Demonstrate this end to end; no fabricated success message.
- [ ] **Part D — Analysis answers:** Answer the five integration questions listed in Section 12 below.
- [ ] **Section VIII — Reflection:** Answer the five guide/reflection questions in 1–5 sentences each.
- [ ] **Section IX — Conclusion:** A short paragraph explaining how components work together and why architecture/integration matter.
- [ ] **Short integration documentation:** Explain how the UI, backend, database, network, and authentication interact, preferably with screenshots and a simple workflow.

**Handout formatting:** Submit architecture diagram in PNG/PDF; provide program HTML source if the optional standalone HTML challenge is completed; compile answers/documentation in Word/PDF. Use the instructor's naming convention `Surname_SystemIntegration_LabActivity` (replace `Surname` with the actual surname). Keep outputs clearly labeled and organized. For a Laravel application, include the relevant project/source code and demo instructions as well if the instructor requests them; do **not** present Laravel Blade files as a standalone HTML demo without explaining the server dependency.

**Rubric (100 points):** components 15; architecture diagram 25; functional registration demonstration 25; integration analysis 20; documentation/presentation 15. Allocate effort accordingly.

## 2. Technology and design constraints

| Area | Preferred approach | Why |
|---|---|---|
| Backend | **Laravel + PHP** matching the actual installed version | Single cohesive framework, validation, routes, authentication, ORM |
| Frontend | **Blade**, mobile-first semantic HTML, existing CSS stack (Tailwind if already set up) | Fast to develop; few moving parts |
| Interactivity | Native HTML forms and minimal JavaScript; **Livewire only if already installed or materially helpful** | Avoid needless SPA/API complexity |
| Database | **MySQL** locally; use Eloquent and migrations | Fulfills real storage requirement |
| Auth | Existing **official Laravel authentication scaffolding/starter kit** where possible | Avoid writing a custom auth system |
| Diagram | diagrams.net (Draw.io) | Easy to edit and export |
| Development | VS Code, Composer, Node.js only when required, local PHP/MySQL setup | No paid services needed |
| Testing | Laravel's installed testing tools plus manual nontechnical walkthroughs | Technical correctness + usability |

### Version and repository compatibility are non-negotiable

1. **Inspect before changing anything:** `composer.json`, `composer.lock`, `package.json`, routes, models, migrations, controllers, views, configuration and existing tests.
2. Confirm installed PHP/Laravel and package versions using `php -v`, `php artisan --version`, `composer show --direct`, and `npm list --depth=0` as relevant. **Do not assume Laravel 13, package APIs, or a particular starter kit.**
3. Follow the existing repository's conventions, naming, dependencies, style, folder layout and testing framework. If this is a new project, choose the fewest well-supported dependencies.
4. Prefer official framework facilities to custom implementations. No React/Vue, microservices, Redis, Docker, third-party auth, cloud hosting or paid software **unless they already exist in the repository or are specifically requested**.
5. Local-first; hosting and deployment are outside this lab's minimum scope.

## 3. Roles and functional permissions

The system has **exactly three application roles** (not to be confused with the three development agents in `AGENTS.md`). Use server-side authorization, not just hidden buttons.

| Function | Student | Teacher | Administrator |
|---|---|---|---|
| Register student account | Yes, public student registration | No public teacher self-assignment | Can add/manage students |
| Log in / log out | Yes | Yes | Yes |
| View student profile | Own | Assigned students as needed | All |
| Edit student profile | Own allowed personal fields | No, unless explicitly assigned | Yes |
| Browse subjects | Yes | Assigned subjects | All |
| Enroll in subject | Own enrollment | No | Can manage enrollments |
| View grades | Own only | Only for assigned subjects/students | All |
| Enter/update grades | No | Yes, for assigned students | Yes |
| Manage subjects, users, roles | No | No | Yes |

**Role safety:** Public signup **always creates a student**, never allows users to choose `admin`/`teacher`. Make demo teacher/admin accounts with a local seeder or an approved local setup process. No hardcoded real-user credentials. Changes must be forbidden by the backend when the role is unauthorized.

## 4. Required pages and user experience

Build the following pages; a full dashboard is useful but should remain straightforward:

- **Public:** Login, student registration. Clear links between them, helpful validation errors, password visibility option only if simple.
- **Student:** Dashboard, student profile, available subjects, my enrollments, my grades.
- **Teacher:** Dashboard, assigned subjects, enrolled students, enter/update grades.
- **Administrator:** Dashboard, students management, subjects management, enrollments overview, teacher/user management (basic create/assign as needed).
- **Shared:** Logout, clearly labeled navigation, not-found/unauthorized messaging, success/error notices.

### Registration: minimum fields

The handout's example uses **Student Name, Student ID, Course**. The real Laravel implementation should include these, plus **email, password, password confirmation** for authentication. Additional fields (e.g., year level) are optional unless already planned. Validate all fields on the server; enforce uniqueness for Student ID and email; store password using Laravel's secure password hashing; show a confirmation only after saving.

### Visual and mobile requirements

- Professional, clean, calm school-themed UI; clear hierarchy and consistent spacing, colors, forms, tables and messages.
- **Mobile-first:** support ~320px phone widths through desktop; no forced horizontal scroll, clipped table action buttons or unreadable text.
- Keyboard-friendly and accessible: visible labels, focus indicators, meaningful page titles, adequate contrast, inline field errors, logical tab order, usable touch targets, confirmation before destructive actions.
- Display a useful empty state when no enrollments or grades exist.
- Avoid fake numbers, fake grades, hardcoded success alerts, or decorative buttons that do nothing.
- Keep text simple for students and nontechnical school staff. Use clear language such as **“Save Student”**, **“Enroll”**, **“Record Grade”** instead of developer jargon.

## 5. Simple, integrated three-tier architecture

```text
 STUDENT / TEACHER / ADMINISTRATOR
              |
              | Web browser (network over HTTP/HTTPS)
              v
      PRESENTATION LAYER
      Laravel Blade HTML pages
              |
              | Form submissions / requests
              v
       APPLICATION LAYER
        Laravel web server
     Routing -> Auth -> Validation
     Authorization -> Business logic
              |
              | Eloquent / SQL
              v
           DATA LAYER
         MySQL database
    Users | Students | Teachers
   Subjects | Enrollments | Grades
              |
              +--- Data / result returned to Laravel
                        |
                        +--- Response shown in browser
```

**Network Layer:** The browser communicates with the Laravel web server over a local network or internet using HTTP/HTTPS; the application uses a database connection to MySQL. HTTPS should be used outside a trusted local-only demonstration.

**Hardware Layer:** Student/teacher/admin phone or computer; computer/server running Laravel; computer/server running MySQL (may be the *same physical machine* for the lab). It is a logical three-tier architecture even if all components run locally.

**Security Layer:** Laravel authentication, sessions, CSRF protection, server-side validation, password hashing and authorization checks. In diagrams, clearly place the Authentication Module in or alongside the application tier—not between browser and database as a bypass.

**Flow:** Browser -> Laravel route/controller -> auth/authorization + validation -> Eloquent model -> MySQL -> Laravel response -> browser. The browser must **never directly query MySQL**.

## 6. Minimum data model

Use migrations and Eloquent relationships. Start with the fewest tables that still make the modules actually work.

| Table | Key fields / relationship | Notes |
|---|---|---|
| `users` | `id`, `name`, `email` (unique), hashed `password`, `role` | Reuse built-in auth table; only `student`, `teacher`, `admin` values |
| `students` | `id`, `user_id` (unique FK), `student_number` (unique), `course`, optional `year_level` | Student profile linked to user |
| `teachers` | `id`, `user_id` (unique FK), optional `employee_number` | Teacher profile linked to user |
| `subjects` | `id`, `code` (unique), `name`, `teacher_id` (nullable FK) | One assigned teacher per subject is enough for this demo |
| `enrollments` | `id`, `student_id` FK, `subject_id` FK, `school_year`, `semester` | Unique student + subject + school year + semester; avoid duplicate enrollment |
| `grades` | `id`, `enrollment_id` (unique FK), `grade` | One final grade per enrollment is sufficient for the demo |

**Pragmatic simplifications:** Do not build payment, attendance, timetables, prerequisites, multi-campus, parent portals, audit microservices or a complex grading engine unless asked. Keep `school_year` and `semester` simple validated text/enum fields consistent with your chosen design. Grade scale should be documented and validated; do not assume a particular Philippine grading scale without confirmation—pick a clear demonstration scale and label it.

### Data integrity

- Use foreign keys, `unique` constraints, indexes where appropriate, and safe deletion behavior.
- Student registration needs **one user and one student profile**, saved atomically (transaction). No half-registered accounts.
- Do not allow duplicate subject enrollment within the same school year/semester.
- The teacher cannot grade a student not enrolled in a subject assigned to that teacher.
- Students must never see or update someone else's records by manipulating IDs in URLs or forms.
- Do not put secrets in version control; use `.env`, and commit only `.env.example` with placeholders.

## 7. Implementation rules: minimal code, maximum clarity

**Preferred development approach:** Laravel's native routes, Blade templates, Eloquent models/relationships, migration files, authorization policies/gates and validation. Use standard pagination and session flash messages. Let the framework do most of the work.

1. Before adding a class, package or helper, check whether Laravel or the repository already solves that problem.
2. Reuse authentication scaffolding, layouts, form components, existing models and CSS utilities. Avoid duplicate functionality.
3. Avoid unnecessary repository/service interfaces, command buses, custom routers, front-end SPA frameworks, generic CRUD builders and over-engineered abstractions.
4. **Less code does not mean code golf**: clear names, secure defaults, tests and simple readable logic take priority over fewest characters.
5. Controllers should be short and understandable. Use a Form Request if rules become repetitive or complex; otherwise simple request validation is adequate.
6. Use database transactions for multi-record operations. Use parameterized Eloquent queries and escaped Blade output.
7. Favor server-rendered interactions, minimal JavaScript, and standard PRG (Post/Redirect/Get) after successful form submissions.
8. Keep changes targeted. Never rewrite unrelated areas or add packages without a real need.
9. Do not blindly overwrite an existing project; inspect first and build incrementally. Ask only if a decision is unsafe/irreversible or materially changes scope.
10. After every module, run the relevant tests and address errors before moving on. Never claim a test was run if it was not.

## 8. Development milestones and acceptance criteria

### Phase 0 — Inspect and prepare

- [ ] Inspect repo/dependency versions and record assumptions in project notes.
- [ ] Configure local MySQL and `.env` without exposing secrets.
- [ ] Confirm the app runs and migrations work in the development environment.

### Phase 1 — Authentication and registration (**highest priority**)

- [ ] Student submits name, Student ID, course, email and password.
- [ ] Server validates inputs and provides understandable field-specific errors.
- [ ] Unique email and Student ID are enforced by validation + DB constraints.
- [ ] Transaction creates `users` + `students` records with hashed password.
- [ ] Confirmation is shown only on success; registered user can log in.
- [ ] Public form does not permit admin/teacher role escalation.

### Phase 2 — Profile and academic records

- [ ] Student sees only own profile; can update only permitted personal fields.
- [ ] Admin can view/edit student records.
- [ ] Admin can create/edit subjects and assign teacher(s) via simple one-teacher model.
- [ ] Student can enroll once per subject per term and see current enrollments.
- [ ] Teacher can enter/update grades for their assigned students.
- [ ] Student can view own grades; no student can edit grades.

### Phase 3 — Polish, security and demonstration

- [ ] All three roles have appropriate, usable navigation and mobile layouts.
- [ ] Unauthorized routes fail safely (403 or redirect) and don't reveal records.
- [ ] Tests cover core success paths, invalid data, duplicate registration/enrollment, and access control.
- [ ] Complete the Part A components table and Part B architecture diagram.
- [ ] Demonstrate Part C with real UI -> application -> database -> confirmation flow.
- [ ] Prepare Parts D, VIII, IX, and the short integration documentation.

### Definition of done

A feature is done only when it **works end to end, persists/retrieves real data where required, enforces permissions, handles invalid input, is usable on desktop and phone, and has been tested**. A completed lab also needs all deliverables—not code alone.

## 9. Preferred routes and implementation shape (illustrative)

Use Laravel's route naming conventions and existing folder patterns; these are *examples*, not a requirement to create bespoke route files or API endpoints:

```text
GET  /register            -> student signup form
POST /register            -> validate + save user/student
GET  /login               -> login form
POST /login               -> authenticate
POST /logout              -> end session
GET  /dashboard           -> role-appropriate dashboard
GET  /profile             -> own student profile
GET  /subjects            -> list of available subjects
POST /enrollments         -> enroll authenticated student
GET  /grades              -> own grade view
... /admin/students       -> admin CRUD
... /admin/subjects       -> admin CRUD
... /teacher/subjects     -> assigned subjects
... /teacher/grades       -> grade input for authorized students
```

Only add routes actually needed. Auth scaffolding may already define several of these: **reuse it** instead of creating conflicting routes. A simple role-based dashboard redirect is enough.

## 10. Demo script (required to prove integration)

Use **fictional** sample information, never real school data:

1. Start the local Laravel app and MySQL. Show the registration form in the browser.
2. Register a new student (`Name`, `Student ID`, `Course`, `Email`, `Password`).
3. Show success confirmation or resulting dashboard.
4. Open the database (e.g., a MySQL GUI) and show the new `users` + `students` rows. Password must be a hash, not plaintext.
5. Log out, then log back in as that student; confirm saved profile values appear.
6. Enroll in an available subject and show an enrollment saved in MySQL.
7. Log in as an assigned teacher; record the student's grade.
8. Log back in as student; show the grade is viewable but cannot be edited.
9. Briefly demonstrate an invalid registration and a forbidden action.

If time is limited, steps 1–5 are the minimum **registration integration demonstration** from Part C; the remaining steps demonstrate the broader functional scenario.

## 11. Documentation and output organization

Suggested folder layout (adapt if instructor or existing repo differs):

```text
project-root/
  AGENTS.md
  INSTRUCTIONS.md
  app/                 # Laravel application code
  routes/
  resources/views/
  database/migrations/
  tests/
  docs/lab/
    Student_System_Architecture.png  # or .pdf
    System_Components_Table.md
    Integration_Explanation.md
    Analysis_And_Reflection.md
    Demo_Steps.md
    screenshots/                # optional evidence
```

Export a **single Word/PDF report** with your table, answers, reflection, conclusion and integration explanation when ready. Do not leave final handout requirements scattered across code comments.

## 12. Exact question coverage for written submission

**Part A guide question**

- Why is each component important in the development of an integrated information system?

**Part D — Analyze System Integration**

1. How does the user interface communicate with the application server?
2. What is the role of the database server in the system?
3. What might happen if the database server becomes unavailable?
4. Why is authentication important in an integrated information system?
5. How does system integration improve the overall performance of an organization?

**Section VIII — Guide Questions and Reflection** (answer in **1–5 sentences each**)

1. What is the difference between system integration and system architecture?
2. Why should system architects consider scalability when designing an information system?
3. What are the possible challenges encountered when integrating different software applications?
4. How can proper system architecture improve system security and reliability?
5. What did you learn from designing and demonstrating an integrated information system?

**Section IX — Conclusion**

Write a short paragraph about the experience designing the Student Information System, how the components work together, and why system integration and architecture are important in modern information systems.

## 13. Critical quality and security checks

- Authenticated user actions must be authorized **server-side** by role and ownership/assignment.
- Forms use CSRF protection and server-side validation; password hashing is automatic/secure.
- Escape untrusted output; do not concatenate user input into raw SQL or use unsafe HTML rendering.
- Handle database outages gracefully: show a neutral error and avoid claiming saved data.
- Use demo/fake data only; do not display passwords or sensitive values in logs/screenshots.
- Migrations should run on a clean database; demo seeders must be clearly development-only.
- Automated tests should use isolated test data and **never destroy real user data**.
- Test keyboard accessibility, mobile layout, friendly error messages and clear navigation.
- Avoid redundant code, dependencies, and hidden magic. Make the project easy to explain to the instructor.

## 14. How to use these instructions

1. Put `INSTRUCTIONS.md` and `AGENTS.md` in the Laravel project's root.
2. Tell your coding agent: **“Read AGENTS.md and INSTRUCTIONS.md first. Inspect the repository, follow the three-agent workflow, and implement Phase 0 then Phase 1 before anything else.”**
3. Work through the phases and use the final checklist. Keep a short report of files changed, tests run, outstanding issues, and next steps after each milestone.
4. If an existing implementation differs from these assumptions, preserve working conventions, document the difference, and choose the simplest compatible solution that still meets the lab handout.
