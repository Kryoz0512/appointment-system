# Combined Project Rules & Architecture

This document serves as the master reference for the Appointment Booking System, combining the database schema, architectural constraints, and the system flowchart.

## System Flowchart
check the flowchart file
![System Flowchart](c:/xampp/htdocs/appointment_system/combined_flowchart.pdf)

*(Note: The flowchart above outlines the complete User and Admin booking flows, including quota checks, date availability, and auto-approval mechanisms).*

---

## 1. Architectural & Design Rules (from `mustread.md`)

We are building a robust appointment booking system for tax transactions. The backend will be powered by **Vanilla PHP** and **MySQL** (using PDO), and the frontend will use standard HTML, CSS, and Vanilla JavaScript. We are not using any PHP or JS frameworks.

### Pre-Coding Architectural & Performance Audit
*   **N+1 Query Prevention (Vanilla PHP):** Specifically analyze how we will query the database for the Admin Dashboard when viewing schedules. Identify exactly where N+1 problems will occur if we query related Users or Transaction settings inside a PHP `while` or `foreach` loop. Dictate the exact SQL `JOIN` strategies we must use to fetch everything in a single query.
*   **Race Conditions:** How will we handle concurrency if two users attempt to book the final remaining slot in a daily quota at the exact same millisecond? Recommend a raw SQL approach using database transactions and row-level locking (e.g., `SELECT ... FOR UPDATE`).
*   **Database Indexing:** What indexes should be applied to the `Appointments` and `BlockedDates` tables to ensure lightning-fast calendar rendering as the dataset grows?

### Modern UI/UX Design System
Given the need for a highly polished front-end, act as a UI/UX expert to define the visual language before we build the HTML structures.
*   Propose a modern, clean, and accessible design system using **Tailwind CSS**. 
*   We will be using the Tailwind Browser CDN. You must explicitly include this script tag in all frontend files: `<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>`.
*   Outline a logical UI structure that elevates the experience beyond a standard, clunky government portal, focusing on clean typography and whitespace.
*   Define the visual cues and state management for the calendar interface (e.g., how to clearly and beautifully distinguish between available, full, and blocked dates using plain JavaScript and Tailwind utility classes).

---

## 2. Database Schema Rules (from `sql.md`)

```sql
-- 1. Create the Users Table
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('User', 'Admin') DEFAULT 'User' NOT NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create the Transactions Table
CREATE TABLE Transactions (
    TransactionID INT AUTO_INCREMENT PRIMARY KEY,
    TransactionName VARCHAR(100) NOT NULL,
    Requirements TEXT,
    DailyQuota INT NOT NULL DEFAULT 0
);

-- 3. Create the Appointments Table
CREATE TABLE Appointments (
    AppointmentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    TransactionID INT NOT NULL,
    ApptDate DATE NOT NULL,
    ApptTime TIME NOT NULL,
    Status ENUM('Confirmed', 'Rescheduled', 'Cancelled') DEFAULT 'Confirmed' NOT NULL,
    CancelReason TEXT,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (TransactionID) REFERENCES Transactions(TransactionID) ON DELETE CASCADE
);

-- 4. Create the Blocked Dates Table
CREATE TABLE BlockedDates (
    BlockedID INT AUTO_INCREMENT PRIMARY KEY,
    BlockedDate DATE NOT NULL UNIQUE,
    Reason VARCHAR(255)
);
```
