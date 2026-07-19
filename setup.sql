-- 1. Create the Users Table
CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('User', 'Admin') DEFAULT 'User' NOT NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create the Transactions Table
CREATE TABLE IF NOT EXISTS Transactions (
    TransactionID INT AUTO_INCREMENT PRIMARY KEY,
    TransactionName VARCHAR(100) NOT NULL,
    Requirements TEXT,
    DailyQuota INT NOT NULL DEFAULT 0
);

-- 3. Create the Appointments Table
CREATE TABLE IF NOT EXISTS Appointments (
    AppointmentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    TransactionID INT NOT NULL,
    ApptDate DATE NOT NULL,
    ApptTime TIME NOT NULL,
    Status ENUM('Pending', 'Confirmed', 'Pending_Reschedule', 'Cancelled', 'Completed') DEFAULT 'Pending' NOT NULL,
    CancelReason TEXT,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (TransactionID) REFERENCES Transactions(TransactionID) ON DELETE CASCADE
);

-- 4. Create the BlockedDates Table
CREATE TABLE IF NOT EXISTS BlockedDates (
    BlockedID INT AUTO_INCREMENT PRIMARY KEY,
    BlockedDate DATE NOT NULL UNIQUE,
    Reason VARCHAR(255)
);

-- 5. Add Performance Indexes
-- Helps count confirmed appointments per date for quota checking quickly
CREATE INDEX idx_appt_date_status ON Appointments (ApptDate, Status);
-- Helps filter quotas and appointments by specific tax transactions
CREATE INDEX idx_transaction_date ON Appointments (TransactionID, ApptDate);


-- Default Transactions
INSERT IGNORE INTO Transactions (TransactionName, Requirements, DailyQuota) VALUES 
('RPT', 'Latest Tax Declaration, Previous Year Receipt', 50),
('Transfer Tax', 'Deed of Sale, Certified True Copy of Title, Tax Clearance', 30),
('Business Tax', 'Barangay Clearance, DTI/SEC Registration, Previous Year Gross Sales', 100),
('Tax Clearance', 'Valid ID, Authorization Letter (if representative)', 150),
('Posting Certification', 'Request Letter, Valid ID', 20);
