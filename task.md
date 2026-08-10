# BPS ACT — Task Tracker

## Step 1: Project Setup & Firebase Integration
- [x] Install Laravel 13 project structure
- [x] Install and configure `kreait/laravel-firebase`
- [x] Install Tailwind CSS v4, Alpine.js, FullCalendar 6 via Vite
- [x] Define BPS OKLCH color tokens in `resources/css/app.css`
- [x] Configure Tailwind theme with BPS semantic tokens

## Step 2: Authentication & Role Management
- [x] Implement session auth with `admin` and `staff` roles
- [x] Seed initial divisions and user profiles in Firestore

## Step 3: Activity Repository & CRUD API
- [x] Create `ActivityService` abstraction over Firestore
- [x] Implement Form Requests with conflict checking
- [x] Build Activity CRUD Blade views with Alpine.js

## Step 4: Calendar & Resource Timeline Integration
- [x] Mount FullCalendar 6 with Month/Week/Day views
- [x] Connect JSON feeds from Laravel backend
- [x] Add detail drawer and drag-to-reschedule handler
- [x] Resource Timeline plugin with division grouping

## Step 5: Executive Dashboard & Quality Assurance
- [x] Build dashboard analytics
- [x] Add loading skeletons, empty states
- [x] Write feature tests and manual verification

