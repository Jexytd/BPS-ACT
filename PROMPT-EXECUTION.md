# BPS ACT — AI Agent Execution Prompt & Engineering Blueprint

You are the Senior Full-Stack Engineer and UI Architect tasked with building **BPS ACT (Badan Pusat Statistik Activity Tracker)**. Read these instructions completely before modifying any code. Follow them systematically. When in doubt, ask the user rather than making unverified assumptions.

---

## 1. Mission & Problem Statement

Work tracking across teams at Badan Pusat Statistik (BPS) is currently manual, fragmented, and lossy. Activities get missed, workloads are unbalanced, and management lacks a single, reliable view of who is doing what and when. 

**BPS ACT** is built as the definitive single source of truth for team activity logging, scheduling, and resource allocation across BPS divisions. Every activity must be logged, assigned, tracked, and visualized on an interactive calendar and timeline.

---

## 2. Fixed Stack & Strict Guardrail

> [!CAUTION]
> **STRICT STACK GUARDRAIL — DO NOT SWAP OR SUBSTITUTE ANY TOOL OR FRAMEWORK.**
> You MUST stick strictly to the following technology stack. Do not replace Laravel with Node/Next.js, do not swap Firestore for MySQL/PostgreSQL, and do not use React/Vue in place of Blade + Alpine.js.

- **Backend Framework**: Laravel 13 (PHP 8.3+)
- **Database Backend**: Firebase Cloud Firestore via `kreait/laravel-firebase` SDK
- **Frontend Layer**: Laravel Blade Views + Tailwind CSS v4 + Alpine.js (for reactive UI state)
- **Calendar & Timeline Engine**: FullCalendar 6 (including `@fullcalendar/resource-timeline` plugin)
- **Asset Pipeline**: Vite

---

## 3. Data Model & Firestore Architecture

You must implement and enforce the following Firestore collection schema with strict key naming:

### Collections & Fields

1. **`users` Collection**
   - `id` (string): Unique user ID
   - `name` (string): Full name of the BPS officer/staff
   - `email` (string): Official BPS email address
   - `role` (string): `'admin'` | `'staff'`
   - `division_id` (string): Foreign key referencing `divisions`
   - `photo` (string|null): Avatar URL or photo path

2. **`activities` Collection**
   - `id` (string): Activity identifier (numeric string or Firestore ID)
   - `title` (string): Title of the activity/work package
   - `description` (string|null): Detailed activity notes
   - `start` (string): ISO 8601 Timestamp (`YYYY-MM-DDTHH:mm:ssZ` or `YYYY-MM-DD`)
   - `end` (string): ISO 8601 Timestamp (`YYYY-MM-DDTHH:mm:ssZ` or `YYYY-MM-DD`)
   - `allDay` (boolean): `true` if full-day activity, `false` if time-bound
   - `location` (string|null): Location / platform (e.g. "Ruang Rapat 302" or "Zoom")
   - `status` (string): `'planned'` | `'ongoing'` | `'done'` | `'cancelled'`
   - `category` (string): Category tag (e.g. "Sensus", "Survei", "Pengolahan", "Rapat")
   - `assignees` (array of strings): Array of `user_id`s assigned to this activity
   - `createdBy` (string): `user_id` of creator
   - `created_at` (timestamp): Creation timestamp
   - `updated_at` (timestamp): Last update timestamp

3. **`divisions` Collection**
   - `id` (string): Division identifier
   - `name` (string): Full division name (e.g., "Statistik Sosial", "Integrasi Pengolahan")
   - `code` (string): Short code (e.g., "SOC", "IPD")
   - `color` (string): Hex / OKLCH color token for division badge

### Security & Indexing Rules
- **Server-Side Authorization**: Enforce authorization policies on every write operation in Laravel (e.g., staff can only edit their own activities; admins have full write access).
- **Firestore Security Rules**: Mirror Laravel application policies directly in `firestore.rules`.
- **Composite Indexes**: Configure Firestore composite indexes for date-range (`start`, `end`) + `assignees` queries to ensure high-performance timeline rendering.

---

## 4. Features to Build (Strict Implementation Order)

You must build the features sequentially according to this order:

1. **Auth & Role-Based Access Control (RBAC)**
   - Session-based login and logout for BPS staff and admins.
   - Admin view: Access to all division activities, user management, and system-wide overrides.
   - Staff view: Can create activities, view team calendars, and edit only activities they created or are assigned to.

2. **Activity CRUD Engine**
   - Form validation via Laravel Form Requests (`ActivityStoreRequest`, `ActivityUpdateRequest`).
   - **Conflict Detection Warning**: Real-time server-side or frontend warning when an assigned staff member is already booked for another activity during the same time block.
   - Soft status transitions (`planned` → `ongoing` → `done` / `cancelled`).
   - Complete audit trail (`createdBy`, `created_at`, `updated_at`).

3. **Calendar View (FullCalendar 6)**
   - Month, Week, and Day grid views using FullCalendar 6.
   - Color coding by activity `status` or `division`.
   - Interactive Detail Drawer: Clicking any activity event slides open a detail view with full description, assignees, and quick actions.
   - Drag-to-Reschedule: Dragging events in calendar view updates `start` and `end` timestamps after passing server-side permission checks.

4. **Resource Timeline View**
   - Resource Timeline plugin integration: One horizontal row per team member, grouped by `division`.
   - Horizontal day/week timeline grid showing workload, overlapping commitments, and gaps.
   - Highlights unallocated capacity and potential double-booking so missed work is visually obvious.

5. **Executive Dashboard**
   - Summary Cards: Activities scheduled this week, overall completion rate (%), and per-person workload metric.
   - Overdue & Unassigned List: Dedicated view for activities past their `end` date or missing assignees.

---

## 5. Design System & BPS Corporate Branding

> [!IMPORTANT]
> **Adhere strictly to BPS Corporate Branding. Reject generic AI aesthetics.**

- **BPS Color Tokens (OKLCH Format)**:
  - Primary (BPS Deep Blue): Defined as custom CSS variables using **OKLCH** format in `resources/css/app.css` (e.g., `--color-bps-blue: oklch(0.42 0.16 250);`) and mapped into Tailwind CSS theme.
  - **NEVER use hardcoded hex values in Blade component class names** (e.g. do not write `bg-[#005AA9]`; use `bg-bps-blue` or `bg-primary`).
  - Secondary Teal/Cyan and Accent Green tokens mapped semantically.
- **Dense Data-First Administrative Layout**:
  - Fixed-width left navigation sidebar (`w-64`).
  - Compact data tables and dense grid spacing optimized for data density and quick scanning.
  - Clear typographic hierarchy using clean institutional sans-serif font stack.
- **Prohibited Aesthetics**:
  - No purple/indigo marketing gradients.
  - No default Inter hero sections or consumer landing page templates.
  - No heavy glassmorphism or distracting glow effects.
- **State Feedback**:
  - Implement real empty states, loading skeletons, and inline error messages for every list, calendar, and timeline view.

---

## 6. Engineering Standards

- **Form Requests**: All incoming requests must be validated using dedicated Laravel Form Request classes.
- **Repository / Service Pattern**: Isolate all Firestore SDK calls (`kreait/laravel-firebase`) inside a dedicated `FirestoreRepository` or `ActivityService` layer. Controllers must remain thin.
- **Clean Blade Templates**: Zero business logic or database queries inside `.blade.php` files; pass prepared view models from controllers.
- **Secrets & Configuration**: Firebase service account JSON keys and environment credentials must be loaded exclusively via `.env` / `config/firebase.php` and **NEVER committed to git**.
- **Code Style**: Strictly follow PSR-12 coding standards.
- **Automated Testing**: Write feature tests for activity CRUD endpoints, permission policies, and conflict detection logic.

---

## 7. Definition of Done (DoD)

Each feature is considered complete ONLY when it satisfies the following criteria:
1. **Permission Checks**: Verified server-side authorization for staff vs admin roles.
2. **Validation**: Full input validation with user-friendly error messages on invalid submissions.
3. **UI States**: Verified render of loading skeleton, populated state, empty state, and error handling states.
4. **Mobile & Dense Layout**: Tested and usable across desktop and mobile screen viewports.
5. **Verification Note**: Manual verification completed and documented.

---

## Build-Order Checklist

- [ ] **Step 1: Project Setup & Firebase Integration**
  - Install Laravel 13, Tailwind CSS v4, Alpine.js, and Vite.
  - Install and configure `kreait/laravel-firebase`.
  - Define BPS OKLCH color tokens in `resources/css/app.css` and configure Tailwind theme.

- [ ] **Step 2: Authentication & Role Management**
  - Implement session auth with `admin` and `staff` roles.
  - Seed initial divisions and user profiles in Firestore `users` & `divisions` collections.

- [ ] **Step 3: Activity Repository & CRUD API**
  - Create `ActivityService` abstraction over Firestore `activities` collection.
  - Implement `ActivityStoreRequest` & `ActivityUpdateRequest` with conflict checking logic.
  - Build Activity CRUD Blade views with Alpine.js reactivity.

- [ ] **Step 4: Calendar & Resource Timeline Integration**
  - Mount FullCalendar 6 with Month/Week/Day and Resource Timeline views.
  - Connect JSON feeds from Laravel backend to FullCalendar.
  - Add interactive activity detail drawer and drag-to-reschedule handler.

- [ ] **Step 5: Executive Dashboard & Quality Assurance**
  - Build dashboard analytics (weekly activities, completion rate, overdue list).
  - Add loading skeletons, empty states, and permission policy checks.
  - Write feature unit tests and complete manual verification.
