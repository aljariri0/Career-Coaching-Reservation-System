# Career Coaching Reservation System

## Features

**Users**
- Register and log in
- Browse upcoming time slots (filter by date)
- Reserve a slot with an optional note
- View and cancel their own reservations

**Admins**
- Dashboard with statistics and a chart
- Create and delete time slots
- Confirm, reject, or cancel reservations (search by name/email, filter by status)
- Activate or deactivate users

## Setup

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy the project folder to `C:\xampp\htdocs\`.
3. Open [phpMyAdmin](http://localhost/phpmyadmin), go to the **Import** tab, and import `schema.sql`. This creates the `coaching_db` database, tables, sample slots, and default accounts.
4. If your MySQL credentials are not `root` with an empty password, edit `config/db.php`.
5. Open `http://localhost/Career-Coaching-Reservation-System/login.php`.

## Default Accounts

| Role  | Email            | Password  |
|-------|------------------|-----------|
| Admin | admin@mail.com   | 1234567   |
| User  | issa@mail.com    | 1234567   |

Change these passwords before using the system outside local development.

## Project Structure

```
├── admin/
│   ├── dashboard.php      # Statistics and chart
│   ├── reservations.php   # Manage reservations
│   ├── slots.php          # Manage time slots
│   └── users.php          # Manage users
├── assets/css/style.css   # Styles
├── config/db.php          # DB connection, session, helper functions
├── index.php              # Available slots + booking
├── my_reservations.php    # User's reservations
├── login.php
├── register.php
├── logout.php
└── schema.sql             # Database schema and seed data
```

## Business Rules

- A slot cannot be booked if it is in the past or full.
- A user cannot book the same slot twice.
- Only `Pending` and `Confirmed` reservations count toward a slot's capacity.
- Users can only cancel their own future reservations.
- A slot with `Pending` or `Confirmed` reservations cannot be deleted.
- Deactivated users are logged out immediately and cannot log in.
- Admins cannot deactivate their own account.