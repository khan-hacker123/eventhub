-- Create the database
CREATE DATABASE IF NOT EXISTS mydatabase;
USE mydatabase;

-- Table 1: allowed_emails
CREATE TABLE IF NOT EXISTS allowed_emails (
    email VARCHAR(255) NOT NULL,
    PRIMARY KEY (email)
);

-- Table 2: customers
CREATE TABLE IF NOT EXISTS customers (
    name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (email_address),
    FOREIGN KEY (email_address) REFERENCES allowed_emails(email) ON DELETE CASCADE
);

-- Table 3: registrations
CREATE TABLE IF NOT EXISTS registrations (
    name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    PRIMARY KEY (email_address, event_name),
    FOREIGN KEY (email_address) REFERENCES customers(email_address) ON DELETE CASCADE
);
