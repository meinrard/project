-- CREATE DATABASE
CREATE DATABASE IF NOT EXISTS FINGERPRINT;
USE FINGERPRINT;

-- USERS TABLE (LOGIN SYSTEM)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
);

INSERT INTO users (username, password)
VALUES ('admin', '1234');

-- STUDENTS TABLE (FINGERPRINT REGISTRATION)
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100),
    reg_number VARCHAR(50) UNIQUE,
    course VARCHAR(100),
    semester INT,
    fingerprint_id INT UNIQUE
);

-- FEES TABLE (PAYMENT RECORDS)
CREATE TABLE fees (
    fee_id INT AUTO_INCREMENT PRIMARY KEY,
    reg_number VARCHAR(50),
    amount_paid INT,
    status VARCHAR(50),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- EXAMS TABLE
CREATE TABLE exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(100),
    course VARCHAR(100),
    course_code VARCHAR(50),
    exam_date DATE,
    start_time TIME,
    end_time TIME
);

-- ATTENDANCE TABLE
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    status VARCHAR(100),
    time_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);