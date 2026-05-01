# 🗄️ Database Schema

## Database: `nqobileq_db`

### Table: `users`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | User ID |
| email | VARCHAR(100) UNIQUE | User email |
| password | VARCHAR(255) | Hashed password |
| full_name | VARCHAR(100) | User's full name |
| phone | VARCHAR(20) | Contact number |
| created_at | TIMESTAMP | Registration date |

### Table: `service_bookings`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Booking ID |
| name | VARCHAR(100) | Customer name |
| email | VARCHAR(100) | Customer email |
| phone | VARCHAR(20) | Customer phone |
| service_type | VARCHAR(50) | Service selected |
| preferred_date | DATE | Preferred date |
| message | TEXT | Additional details |
| status | VARCHAR(20) | pending/completed |
| created_at | TIMESTAMP | Booking date |

### Table: `package_bookings`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Booking ID |
| name | VARCHAR(100) | Customer name |
| email | VARCHAR(100) | Customer email |
| phone | VARCHAR(20) | Customer phone |
| package_name | VARCHAR(50) | Package selected |
| status | VARCHAR(20) | pending/active |
| created_at | TIMESTAMP | Booking date |

### Table: `inquiries`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Inquiry ID |
| name | VARCHAR(100) | Customer name |
| email | VARCHAR(100) | Customer email |
| phone | VARCHAR(20) | Customer phone |
| message | TEXT | Inquiry message |
| status | VARCHAR(20) | new/read/replied |
| created_at | TIMESTAMP | Submission date |

### Table: `testimonials`
| Column | Type | Description |
|--------|------|-------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Testimonial ID |
| name | VARCHAR(100) | Customer name |
| email | VARCHAR(100) | Customer email |
| message | TEXT | Review text |
| rating | INT | 1-5 stars |
| status | VARCHAR(20) | pending/approved |
| created_at | TIMESTAMP | Submission date |
