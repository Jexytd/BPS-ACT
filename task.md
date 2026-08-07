# BPS ACT — Task Tracker

## Step 1: Project Setup & Firebase Integration
- [/] Install Laravel 13 project structure
- [ ] Install and configure `kreait/laravel-firebase`
- [ ] Install Tailwind CSS v4, Alpine.js, FullCalendar 6 via Vite
- [ ] Define BPS OKLCH color tokens in `resources/css/app.css`
- [ ] Configure Tailwind theme with BPS semantic tokens

## Step 2: Authentication & Role Management
- [ ] Implement session auth with `admin` and `staff` roles
- [ ] Seed initial divisions and user profiles in Firestore

## Step 3: Activity Repository & CRUD API
- [ ] Create `ActivityService` abstraction over Firestore
- [ ] Implement Form Requests with conflict checking
- [ ] Build Activity CRUD Blade views with Alpine.js

## Step 4: Calendar & Resource Timeline Integration
- [ ] Mount FullCalendar 6 with Month/Week/Day views
- [ ] Connect JSON feeds from Laravel backend
- [ ] Add detail drawer and drag-to-reschedule handler
- [ ] Resource Timeline plugin with division grouping

## Step 5: Executive Dashboard & Quality Assurance
- [ ] Build dashboard analytics
- [ ] Add loading skeletons, empty states
- [ ] Write feature tests and manual verification
