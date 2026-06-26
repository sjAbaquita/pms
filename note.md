Overall Goal

Build a modern Resort Booking Management System that allows:

Guests to book rooms online
Staff to manage reservations
Prevent double bookings
Manage room inventory
Track check-ins/check-outs
Manage rates
Generate reports

Stack:

Laravel 13
Vue 3
Inertia.js
TailwindCSS
MySQL
Laravel Breeze (Authentication)
Spatie Permission (Roles)
Laravel Queue
Laravel Notifications
Current Accommodation Analysis

The resort currently offers these accommodation categories:

Room Type	Base Capacity	Pricing
Standard Room	2 Guests	₱3,300
Standard Deluxe Ground Floor	2 Guests	₱3,850
Standard Deluxe Ocean View	2 Guests	₱4,400
Family Room	4 Guests	₱6,380
Villa Room	2 Guests	₱6,930

Additional business rules found:

Standard Room

maximum 2
bed sharing allowed
extra person ₱650

Standard Deluxe

max 2
up to 2 extra mattresses
extra mattress ₱900
extra person ₱650

Family Room

max 4
up to 2 extra mattresses
same pricing

Villa

max 2
extra persons allowed
extra mattress
premium room

These pricing rules should NOT be hardcoded.

Store them in the database.

System Modules
1. Public Booking Website

Visitors can:

Browse rooms
View room gallery
Check room availability
Select dates
Select guests
Calculate price
Book online
Receive confirmation email
2. Admin Dashboard

Dashboard cards

Today's arrivals
Today's departures
Occupied rooms
Available rooms
Pending reservations
Revenue today
Revenue this month

Charts

Monthly bookings
Occupancy Rate
Revenue
3. Room Type Management

Instead of creating individual rooms first,

Create Room Types.

Example

Standard Room

title
description
max guests
base price
amenities
images
4. Individual Room Management

Example

Room 201

belongs to

Standard Room

Attributes

room number
floor
status
notes

Status

Available
Occupied
Maintenance
Cleaning
Reserved
5. Room Amenities

Separate table

Example

Wifi

TV

Mini Refrigerator

Aircon

Hot Shower

Breakfast

Ocean View

Many-to-many relationship.

6. Reservation Module

Reservation lifecycle

Pending

↓

Confirmed

↓

Checked In

↓

Checked Out

or

Cancelled

or

No Show

Reservation stores

Guest

Room

Dates

Guests

Extra persons

Extra mattresses

Discount

Special requests

Payment

Status

7. Guest Management

Guest Profile

Personal info

History

Past bookings

Current booking

ID upload

Notes

8. Calendar Booking

A visual calendar like Booking.com

Rows

Rooms

Columns

Dates

Color coding

Green

Available

Blue

Occupied

Orange

Reserved

Gray

Maintenance

Drag booking

Resize booking

Move booking

9. Availability Checker

Core feature.

Input

Check-in

Check-out

Guests

Returns

Available rooms only

Must prevent overlapping bookings.

10. Pricing Engine

Instead of

price = room price

Use

Room Base Rate

Extra Guests

Extra Mattress

Seasonal Pricing

Discount

Coupon

Future ready.

11. Seasonal Rates

Example

Holy Week

Christmas

Summer

Peak Season

Weekend

Each can override

Base room price.

12. Payment Module

Phase 1

Cash

Bank Transfer

GCash

Manual Verification

Phase 2

PayMongo

Stripe

PayPal

13. Check In Module

Upon arrival

Verify booking

Assign room

Collect balance

Generate receipt

Update room status

Occupied

14. Check Out Module

Calculate

Remaining balance

Damages

Extra charges

Late checkout

Generate invoice

Room becomes

Cleaning

15. Housekeeping

Cleaning Queue

Dirty rooms

Clean rooms

Maintenance

Housekeeping notes

16. Reports

Revenue

Occupancy

Room Performance

Reservation Report

Guest Report

Cancellation Report

Payment Report

17. User Roles

Admin

Manager

Receptionist

Housekeeping

Cashier

Suggested Database Design
users

roles

permissions

guests

room_types

rooms

room_images

amenities

amenity_room_type

reservations

reservation_rooms

payments

payment_methods

season_rates

discounts

coupons

housekeeping_logs

maintenance_logs

activity_logs
Suggested Reservation Flow
Homepage

↓

Select Dates

↓

Available Rooms

↓

Choose Room

↓

Guest Information

↓

Review Booking

↓

Payment Option

↓

Reservation Created

↓

Confirmation Email

↓

Admin Confirms

↓

Guest Arrives

↓

Check In

↓

Stay

↓

Check Out

Suggested Folder Structure
app/

    Actions/

    DTOs/

    Enums/

    Models/

    Services/

    Repositories/

    Policies/

    Notifications/

    Jobs/

resources/

    js/

        Pages/

            Booking/

            Dashboard/

            Rooms/

            Reservations/

            Guests/

            Reports/

            Settings/

        Components/

        Layouts/

        Composables/

        Types/

routes/

database/

Suggested Features (MVP)
Public
Homepage
Room Listing
Room Details
Availability Search
Booking Form
Booking Confirmation
Staff
Login
Dashboard
Reservations
Guests
Rooms
Room Types
Calendar
Payments
Reports
Users
Nice-to-have Features (Phase 2)
QR Check-in
SMS Notifications
Email Notifications
Digital Registration Form
E-signature
Receipt Generator (PDF)
Walk-in Reservations
POS Integration
Restaurant Charges
Inventory
Event Hall Booking (The Grand Hall & Azucena Hall)
Multi-property Support (to accommodate Bugnaw Si-e Beach Resort II in the future)
Channel Manager (Booking.com, Agoda)




GitHub Copilot Master Prompt
# Project Overview

Build a modern Resort Property Management and Online Booking System for Bugnaw Si-e Beach Resort using Laravel 13, Vue 3, Inertia.js, Tailwind CSS, MySQL, and Vite.

## Architecture

- Backend: Laravel 13
- Frontend: Vue 3 + TypeScript
- SPA: Inertia.js
- Styling: Tailwind CSS
- Database: MySQL
- Authentication: Laravel Breeze
- Authorization: Spatie Laravel Permission
- File Storage: Laravel Storage
- Queue: Database Queue
- Notifications: Laravel Notifications

## Design Principles

- Service Layer Pattern
- Repository Pattern where appropriate
- Form Requests for validation
- Eloquent Relationships
- Reusable Vue components
- Mobile-first responsive UI
- TypeScript for all frontend code
- Strict typing
- RESTful controllers
- Clean architecture
- SOLID principles

## Core Modules

- Authentication
- Dashboard
- Room Types
- Rooms
- Amenities
- Guests
- Reservations
- Availability Search
- Booking Engine
- Calendar View
- Seasonal Pricing
- Payments
- Housekeeping
- Reports
- User & Role Management

## Reservation Rules

- Prevent overlapping bookings
- Support multiple room types
- Calculate total cost dynamically
- Support extra guests and extra mattresses
- Support seasonal pricing
- Support discounts and coupons
- Maintain reservation status workflow

## Reservation Status

Pending
Confirmed
Checked In
Checked Out
Cancelled
No Show

## Room Status

Available
Reserved
Occupied
Cleaning
Maintenance

## Code Standards

- Use TypeScript everywhere in Vue
- Use Composition API
- Use `<script setup>`
- Keep components small and reusable
- Avoid duplicated logic
- Create composables for reusable frontend logic
- Create service classes for business logic
- Use enums for statuses
- Use database seeders for demo data
- Write feature tests for critical booking workflows

## Goal

Develop a scalable, production-ready resort management system that can later support multiple resorts, online payments, event hall reservations, and integrations with OTA platforms.