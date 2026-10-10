# CRRMS Database Design

## Overview
This document outlines the complete database schema for the Campus Resource Reservation & Management System (CRRMS).

## Database Design Principles
- Normalized PostgreSQL database
- Foreign key constraints for referential integrity
- Soft deletes for data recovery
- UUID support where appropriate
- Indexes for performance optimization
- Timestamps for tracking

## Tables (42 Total)

### 1. Users & Authentication (5 tables)

#### users
- id (UUID, primary key)
- employee_id (string, unique)
- student_id (string, unique)
- name (string)
- email (string, unique)
- email_verified_at (timestamp)
- password (string)
- profile_photo_path (string)
- phone (string)
- two_factor_secret (string)
- two_factor_confirmed_at (timestamp)
- two_factor_recovery_codes (json)
- remember_token (string)
- current_team_id (UUID)
- profile_photo_url (string)
- status (enum: active, inactive, suspended)
- last_login_at (timestamp)
- last_login_ip (string)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### roles
- id (UUID, primary key)
- name (string, unique)
- display_name (string)
- description (text)
- created_at (timestamp)
- updated_at (timestamp)

#### permissions
- id (UUID, primary key)
- name (string, unique)
- display_name (string)
- description (text)
- module (string)
- created_at (timestamp)
- updated_at (timestamp)

#### role_user
- id (UUID, primary key)
- role_id (UUID, foreign key)
- user_id (UUID, foreign key)
- created_at (timestamp)

#### permission_role
- id (UUID, primary key)
- permission_id (UUID, foreign key)
- role_id (UUID, foreign key)
- created_at (timestamp)

### 2. User Profile (3 tables)

#### departments
- id (UUID, primary key)
- name (string, unique)
- code (string, unique)
- description (text)
- head_of_department_id (UUID, foreign key)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### organizations
- id (UUID, primary key)
- name (string, unique)
- code (string, unique)
- description (text)
- advisor_id (UUID, foreign key)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### user_profiles
- id (UUID, primary key)
- user_id (UUID, foreign key, unique)
- department_id (UUID, foreign key)
- organization_id (UUID, foreign key, nullable)
- student_number (string, nullable)
- employee_number (string, nullable)
- year_level (integer, nullable)
- course (string, nullable)
- position (string, nullable)
- bio (text, nullable)
- address (text, nullable)
- city (string, nullable)
- country (string, nullable)
- postal_code (string, nullable)
- birth_date (date, nullable)
- gender (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### 3. Building Management (3 tables)

#### buildings
- id (UUID, primary key)
- name (string, unique)
- code (string, unique)
- description (text)
- address (text)
- contact_person (string)
- contact_phone (string)
- contact_email (string)
- number_of_floors (integer)
- image_url (string)
- status (enum: active, inactive, maintenance)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### floors
- id (UUID, primary key)
- building_id (UUID, foreign key)
- floor_number (integer)
- name (string)
- description (text)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### rooms
- id (UUID, primary key)
- building_id (UUID, foreign key)
- floor_id (UUID, foreign key)
- room_number (string, unique)
- name (string)
- type (enum: classroom, laboratory, office, storage, restroom, other)
- capacity (integer)
- area (decimal)
- description (text)
- status (enum: available, occupied, maintenance, reserved)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

### 4. Facility Reservation (4 tables)

#### facilities
- id (UUID, primary key)
- room_id (UUID, foreign key)
- name (string)
- type (enum: classroom, computer_lab, science_lab, engineering_lab, avr, library_discussion_room, auditorium, gymnasium, function_hall, conference_room, multi_purpose_hall, quadrangle, covered_court, open_grounds)
- category (enum: academic, events, outdoor)
- capacity (integer)
- description (text)
- amenities (json)
- photos (json)
- rules (text)
- availability_schedule (json)
- status (enum: available, unavailable, maintenance)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### facility_reservations
- id (UUID, primary key)
- facility_id (UUID, foreign key)
- user_id (UUID, foreign key)
- organization_id (UUID, foreign key, nullable)
- title (string)
- description (text)
- start_date (date)
- end_date (date)
- start_time (time)
- end_time (time)
- status (enum: pending, approved, reserved, checked_in, completed, rejected, cancelled)
- approval_notes (text, nullable)
- rejection_reason (text, nullable)
- qr_code (string)
- checked_in_at (timestamp, nullable)
- checked_in_by (UUID, foreign key, nullable)
- checked_out_at (timestamp, nullable)
- checked_out_by (UUID, foreign key, nullable)
- approved_by (UUID, foreign key, nullable)
- approved_at (timestamp, nullable)
- cancelled_by (UUID, foreign key, nullable)
- cancelled_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### facility_reservation_conflicts
- id (UUID, primary key)
- reservation_id (UUID, foreign key)
- conflicting_reservation_id (UUID, foreign key)
- conflict_type (enum: time_overlap, facility_unavailable, capacity_exceeded)
- resolved (boolean, default: false)
- resolved_by (UUID, foreign key, nullable)
- resolved_at (timestamp, nullable)
- created_at (timestamp)

#### facility_schedules
- id (UUID, primary key)
- facility_id (UUID, foreign key)
- day_of_week (integer)
- start_time (time)
- end_time (time)
- is_available (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

### 5. Equipment Management (5 tables)

#### equipment_categories
- id (UUID, primary key)
- name (string, unique)
- code (string, unique)
- description (text)
- parent_id (UUID, foreign key, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### equipment
- id (UUID, primary key)
- category_id (UUID, foreign key)
- asset_number (string, unique)
- barcode (string, unique)
- qr_code (string, unique)
- brand (string)
- model (string)
- serial_number (string, unique, nullable)
- purchase_date (date)
- warranty_expiry (date, nullable)
- supplier (string)
- cost (decimal, nullable)
- building_id (UUID, foreign key)
- room_id (UUID, foreign key, nullable)
- condition (enum: excellent, good, fair, poor, damaged)
- status (enum: available, borrowed, reserved, maintenance, lost, retired)
- description (text)
- specifications (json)
- photos (json)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### equipment_borrow_requests
- id (UUID, primary key)
- user_id (UUID, foreign key)
- organization_id (UUID, foreign key, nullable)
- status (enum: pending, approved, ready_for_pickup, released, returned, completed, rejected, cancelled)
- borrow_date (date)
- return_date (date)
- purpose (text)
- notes (text, nullable)
- approval_notes (text, nullable)
- rejection_reason (text, nullable)
- approved_by (UUID, foreign key, nullable)
- approved_at (timestamp, nullable)
- released_by (UUID, foreign key, nullable)
- released_at (timestamp, nullable)
- returned_by (UUID, foreign key, nullable)
- returned_at (timestamp, nullable)
- cancelled_by (UUID, foreign key, nullable)
- cancelled_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### equipment_borrow_items
- id (UUID, primary key)
- borrow_request_id (UUID, foreign key)
- equipment_id (UUID, foreign key)
- quantity (integer)
- condition_at_borrow (string)
- condition_at_return (string, nullable)
- damage_notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

#### equipment_history
- id (UUID, primary key)
- equipment_id (UUID, foreign key)
- action (enum: borrowed, returned, reserved, maintenance, transferred, damaged, lost, retired)
- user_id (UUID, foreign key, nullable)
- reference_id (UUID, nullable)
- reference_type (string, nullable)
- old_status (string)
- new_status (string)
- notes (text, nullable)
- performed_by (UUID, foreign key)
- created_at (timestamp)

### 6. Inventory Management (3 tables)

#### inventory_transfers
- id (UUID, primary key)
- equipment_id (UUID, foreign key)
- from_building_id (UUID, foreign key)
- from_room_id (UUID, foreign key, nullable)
- to_building_id (UUID, foreign key)
- to_room_id (UUID, foreign key, nullable)
- transfer_date (date)
- reason (text)
- performed_by (UUID, foreign key)
- approved_by (UUID, foreign key, nullable)
- approved_at (timestamp, nullable)
- created_at (timestamp)

#### maintenance_requests
- id (UUID, primary key)
- equipment_id (UUID, foreign key, nullable)
- facility_id (UUID, foreign key, nullable)
- type (enum: equipment, facility)
- priority (enum: low, medium, high, urgent)
- title (string)
- description (text)
- status (enum: pending, assigned, in_progress, completed, cancelled)
- assigned_to (UUID, foreign key, nullable)
- assigned_at (timestamp, nullable)
- completed_at (timestamp, nullable)
- completed_by (UUID, foreign key, nullable)
- estimated_cost (decimal, nullable)
- actual_cost (decimal, nullable)
- photos (json)
- created_by (UUID, foreign key)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### maintenance_logs
- id (UUID, primary key)
- maintenance_request_id (UUID, foreign key)
- technician_id (UUID, foreign key)
- action (string)
- notes (text)
- photos (json)
- hours_spent (decimal, nullable)
- created_at (timestamp)

### 7. Service Requests (2 tables)

#### service_requests
- id (UUID, primary key)
- user_id (UUID, foreign key)
- type (enum: additional_chairs, cleaning, internet_setup, projector_setup, electrical_assistance, technical_support, other)
- title (string)
- description (text)
- location (string)
- building_id (UUID, foreign key, nullable)
- room_id (UUID, foreign key, nullable)
- priority (enum: low, medium, high, urgent)
- status (enum: pending, assigned, in_progress, completed, cancelled)
- assigned_to (UUID, foreign key, nullable)
- assigned_at (timestamp, nullable)
- scheduled_date (date, nullable)
- scheduled_time (time, nullable)
- completed_at (timestamp, nullable)
- completed_by (UUID, foreign key, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

#### service_request_assignments
- id (UUID, primary key)
- service_request_id (UUID, foreign key)
- assigned_to (UUID, foreign key)
- assigned_by (UUID, foreign key)
- assigned_at (timestamp)
- notes (text, nullable)

### 8. Chat System (5 tables)

#### conversations
- id (UUID, primary key)
- type (enum: private, group)
- name (string, nullable)
- description (text, nullable)
- avatar (string, nullable)
- created_by (UUID, foreign key)
- is_pinned (boolean, default: false)
- created_at (timestamp)
- updated_at (timestamp)

#### conversation_participants
- id (UUID, primary key)
- conversation_id (UUID, foreign key)
- user_id (UUID, foreign key)
- role (enum: admin, member)
- joined_at (timestamp)
- last_read_at (timestamp, nullable)
- is_muted (boolean, default: false)
- left_at (timestamp, nullable)

#### messages
- id (UUID, primary key)
- conversation_id (UUID, foreign key)
- sender_id (UUID, foreign key)
- content (text)
- type (enum: text, image, file, system)
- reply_to_id (UUID, foreign key, nullable)
- is_edited (boolean, default: false)
- edited_at (timestamp, nullable)
- is_deleted (boolean, default: false)
- deleted_at (timestamp, nullable)
- deleted_by (UUID, foreign key, nullable)
- created_at (timestamp)
- updated_at (timestamp)

#### message_attachments
- id (UUID, primary key)
- message_id (UUID, foreign key)
- file_name (string)
- file_path (string)
- file_size (integer)
- file_type (string)
- created_at (timestamp)

#### message_reactions
- id (UUID, primary key)
- message_id (UUID, foreign key)
- user_id (UUID, foreign key)
- emoji (string)
- created_at (timestamp)

### 9. Notifications (2 tables)

#### notifications
- id (UUID, primary key)
- type (string)
- notifiable_id (UUID)
- notifiable_type (string)
- data (json)
- read_at (timestamp, nullable)
- created_at (timestamp)

#### notification_settings
- id (UUID, primary key)
- user_id (UUID, foreign key, unique)
- email_notifications (boolean, default: true)
- push_notifications (boolean, default: true)
- reservation_approved (boolean, default: true)
- reservation_rejected (boolean, default: true)
- reservation_reminder (boolean, default: true)
- equipment_ready (boolean, default: true)
- return_reminder (boolean, default: true)
- overdue_reminder (boolean, default: true)
- maintenance_complete (boolean, default: true)
- new_message (boolean, default: true)
- announcement (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)

### 10. Announcements (1 table)

#### announcements
- id (UUID, primary key)
- title (string)
- content (text)
- audience (enum: all, students, faculty, staff, specific_departments, specific_organizations)
- audience_ids (json, nullable)
- attachments (json)
- is_pinned (boolean, default: false)
- published_at (timestamp, nullable)
- expires_at (timestamp, nullable)
- created_by (UUID, foreign key)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp)

### 11. Activity Logs (1 table)

#### activity_logs
- id (UUID, primary key)
- user_id (UUID, foreign key, nullable)
- action (string)
- module (string)
- description (text)
- ip_address (string, nullable)
- user_agent (string, nullable)
- metadata (json, nullable)
- created_at (timestamp)

### 12. Audit Trail (1 table)

#### audit_trails
- id (UUID, primary key)
- user_id (UUID, foreign key, nullable)
- table_name (string)
- record_id (UUID)
- action (enum: created, updated, deleted, restored)
- old_values (json, nullable)
- new_values (json, nullable)
- changed_fields (json, nullable)
- ip_address (string, nullable)
- user_agent (string, nullable)
- created_at (timestamp)

### 13. Calendar Events (1 table)

#### calendar_events
- id (UUID, primary key)
- title (string)
- description (text, nullable)
- start_date (date)
- end_date (date)
- start_time (time, nullable)
- end_time (time, nullable)
- is_all_day (boolean, default: false)
- type (enum: reservation, borrow, maintenance, event, holiday)
- reference_id (UUID, nullable)
- reference_type (string, nullable)
- color (string, nullable)
- created_by (UUID, foreign key)
- created_at (timestamp)
- updated_at (timestamp)

### 14. Reports (1 table)

#### generated_reports
- id (UUID, primary key)
- name (string)
- type (enum: reservation, inventory, borrow, building_utilization, damage, maintenance)
- format (enum: pdf, excel, csv)
- parameters (json)
- file_path (string)
- file_size (integer)
- generated_by (UUID, foreign key)
- generated_at (timestamp)

## Relationships Summary

### Users
- users → roles (many-to-many via role_user)
- users → permissions (many-to-many via roles)
- users → user_profiles (one-to-one)
- users → departments (many-to-one via user_profiles)
- users → organizations (many-to-one via user_profiles)

### Buildings
- buildings → floors (one-to-many)
- floors → rooms (one-to-many)
- rooms → facilities (one-to-many)
- buildings → equipment (one-to-many)

### Facilities
- facilities → facility_reservations (one-to-many)
- facilities → facility_schedules (one-to-many)
- facilities → maintenance_requests (one-to-many)

### Equipment
- equipment → equipment_categories (many-to-one)
- equipment → equipment_borrow_items (one-to-many)
- equipment → equipment_history (one-to-many)
- equipment → inventory_transfers (one-to-many)
- equipment → maintenance_requests (one-to-many)

### Reservations
- facility_reservations → facility_reservation_conflicts (one-to-many)
- equipment_borrow_requests → equipment_borrow_items (one-to-many)

### Chat
- conversations → conversation_participants (one-to-many)
- conversations → messages (one-to-many)
- messages → message_attachments (one-to-many)
- messages → message_reactions (one-to-many)

### Service
- service_requests → service_request_assignments (one-to-many)
- maintenance_requests → maintenance_logs (one-to-many)

## Indexes

### Performance Indexes
- users.email
- users.employee_id
- users.student_id
- equipment.asset_number
- equipment.barcode
- equipment.qr_code
- equipment.serial_number
- facility_reservations.user_id
- facility_reservations.facility_id
- facility_reservations.status
- facility_reservations.start_date
- facility_reservations.end_date
- equipment_borrow_requests.user_id
- equipment_borrow_requests.status
- equipment_borrow_requests.borrow_date
- equipment_borrow_requests.return_date
- activity_logs.user_id
- activity_logs.created_at
- audit_trails.table_name
- audit_trails.record_id
- messages.conversation_id
- messages.sender_id
- messages.created_at
