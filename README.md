# Student Management Portal – LEMP Stack on AWS EC2

A simple PHP and MySQL-based Student Management Application deployed on an Amazon EC2 instance using the LEMP stack (Linux, Nginx, MySQL/MariaDB, and PHP).

This project demonstrates how to deploy a dynamic web application on AWS EC2 using Nginx as the web server, PHP-FPM for processing PHP requests, and MariaDB as the database.

## 1. Project Overview

The Student Management Portal allows users to manage student registrations and view available training courses.

### Application Features

- Dashboard displaying total registered students.
- Student registration functionality.
- View all registered students.
- Delete student records.
- Display available training courses.
- MySQL database integration.
- Common header and footer components.

## 2. Technologies Used

| Technology | Purpose |
|---|---|
| Amazon Linux 2023 | Operating System |
| AWS EC2 | Cloud Hosting |
| Nginx | Web Server |
| PHP | Backend Programming |
| PHP-FPM | PHP Request Processing |
| MariaDB | Database |
| HTML | Frontend Structure |
| CSS | Styling |
| JavaScript | Client-side Interaction |

## 3. Application Pages

1. **Dashboard (index.php)**
   - Displays total registered students.
   - Displays available training programs.

2. **Student Management (students.php)**
   - Add new students.
   - View registered students.
   - Delete student records.

3. **Courses (courses.php)**
   - Displays available training programs.
   - Shows course descriptions and durations.

## 4. Project Directory Structure

```text
student-portal/
│
├── index.php
├── students.php
├── courses.php
│
├── config/
│   └── db.php
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── assets/
│   └── style.css
│
└── README.md
```

## 5. Prerequisites

Before starting the deployment, make sure you have:

- AWS Account.
- Amazon Linux 2023 EC2 instance.
- SSH access to the EC2 instance.
- Security Group configured.
- GitHub account.
- Basic knowledge of Linux commands, PHP and MySQL.

## 6. Step-by-Step Deployment on AWS EC2

### Step 1: Launch EC2 Instance

1. Login to AWS Management Console.
2. Navigate to EC2.
3. Click Launch Instance.
4. Configure the instance:

| Configuration | Value |
|---|---|
| Name | Student-Portal-Server |
| AMI | Amazon Linux 2023 |
| Instance Type | t2.micro / t3.micro |
| Key Pair | Create or select an existing key pair |
| Storage | 8 GB or more |

### Step 2: Configure Security Group

Allow the following inbound traffic:

| Type | Port | Source |
|---|---|---|
| SSH | 22 | Your IP |
| HTTP | 80 | 0.0.0.0/0 |
| HTTPS | 443 | 0.0.0.0/0 |

Click Launch Instance.

### Step 3: Connect to EC2

Connect using SSH:

```bash
ssh -i your-key.pem ec2-user@YOUR-EC2-PUBLIC-IP
```

Update the system:

```bash
sudo dnf update -y
```

### Step 4: Install LEMP Stack

Install Nginx:

```bash
sudo dnf install nginx -y
```

Install PHP and PHP-FPM:

```bash
sudo dnf install php php-fpm php-mysqli -y
```

Install MariaDB:

```bash
sudo dnf install mariadb105-server -y
```

### Step 5: Start and Enable Services

Start Nginx:

```bash
sudo systemctl start nginx
sudo systemctl enable nginx
```

Start PHP-FPM:

```bash
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
```

Start MariaDB:

```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

Verify services:

```bash
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status mariadb
```

### Step 6: Configure PHP-FPM

Open the PHP-FPM configuration file:

```bash
sudo vi /etc/php-fpm.d/www.conf
```

Update the following parameters:

```ini
user = nginx
group = nginx

listen = /run/php-fpm/www.sock

listen.owner = nginx
listen.group = nginx
listen.mode = 0660
```

Restart PHP-FPM:

```bash
sudo systemctl restart php-fpm
```

### Step 7: Configure MariaDB Database

Secure MariaDB:

```bash
sudo mysql_secure_installation
```

Login to MariaDB:

```bash
sudo mysql -u root -p
```

Create the database:

```sql
CREATE DATABASE student_portal;

USE student_portal;
```

Create the students table:

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Insert sample records:

```sql
INSERT INTO students (name, email, course)
VALUES
('Rahul Sharma', 'rahul@gmail.com', 'AWS'),
('Priya Patil', 'priya@gmail.com', 'DevOps'),
('Amit Verma', 'amit@gmail.com', 'Full Stack');
```

Create an application database user:

```sql
CREATE USER 'studentuser'@'localhost'
IDENTIFIED BY 'Student@123';

GRANT ALL PRIVILEGES ON student_portal.*
TO 'studentuser'@'localhost';

FLUSH PRIVILEGES;
```

Exit MariaDB:

```sql
EXIT;
```

### Step 8: Clone GitHub Repository

Install Git:

```bash
sudo dnf install git -y
```

Navigate to the web directory:

```bash
cd /var/www/html
```

Clone the repository:

```bash
sudo git clone YOUR_GITHUB_REPOSITORY_URL student-portal
```

Navigate into the project:

```bash
cd student-portal
```

### Step 9: Configure Database Connection

Open the database configuration file:

```bash
sudo vi config/db.php
```

Update the database credentials:

```php
<?php

$host = "localhost";
$user = "studentuser";
$password = "Student@123";
$database = "student_portal";

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database Connection Failed: " .
        $conn->connect_error);
}

?>
```

Note: Use your own database credentials. Do not commit production passwords to GitHub.

### Step 10: Configure Nginx

Create a new Nginx configuration file:

```bash
sudo vi /etc/nginx/conf.d/student-portal.conf
```

Add the following configuration:

```nginx
server {
    listen 80;

    server_name _;

    root /var/www/html/student-portal;

    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {

        try_files $uri =404;

        include fastcgi_params;

        fastcgi_pass unix:/run/php-fpm/www.sock;

        fastcgi_index index.php;

        fastcgi_param SCRIPT_FILENAME
            $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

Test Nginx configuration:

```bash
sudo nginx -t
```

Restart Nginx:

```bash
sudo systemctl restart nginx
```

### Step 11: Configure File Permissions

Set ownership:

```bash
sudo chown -R nginx:nginx /var/www/html/student-portal
```

Set directory permissions:

```bash
sudo find /var/www/html/student-portal \
-type d -exec chmod 755 {} \;
```

Set file permissions:

```bash
sudo find /var/www/html/student-portal \
-type f -exec chmod 644 {} \;
```

### Step 12: Access the Application

Copy the Public IPv4 address of your EC2 instance.

Open your browser:

```text
http://YOUR-EC2-PUBLIC-IP/
```

The Student Management Portal should now be accessible.

## 7. Application Testing

Perform the following tests after deployment:

- [ ] Open the application using the EC2 Public IP.
- [ ] Navigate through all three pages.
- [ ] Register a new student.
- [ ] Verify student details in MariaDB.
- [ ] Refresh the page and verify records persist.
- [ ] Delete a student record.
- [ ] Verify the updated student count on the dashboard.
- [ ] Stop Nginx and observe the application.
- [ ] Restart Nginx and test again.
- [ ] Stop MariaDB and observe the database connection error.
- [ ] Restart MariaDB and verify database connectivity.

## 8. Troubleshooting

### Issue 1: Nginx is not starting

Check Nginx configuration:

```bash
sudo nginx -t
```

Check service logs:

```bash
sudo journalctl -u nginx -xe
```

### Issue 2: PHP files are downloading instead of executing

Verify PHP-FPM status:

```bash
sudo systemctl status php-fpm
```

Check the Nginx PHP configuration and FastCGI socket path.

### Issue 3: Database connection failed

Check MariaDB:

```bash
sudo systemctl status mariadb
```

Verify database credentials and database availability.

### Issue 4: Permission denied

Check directory ownership:

```bash
ls -ld /var/www/html/student-portal
```

Check PHP-FPM and Nginx permissions.

## 9. Additional Practical Challenges

Try implementing the following enhancements:

1. Attach an Elastic IP to the EC2 instance.
2. Create an AMI of the configured server.
3. Take a database backup using mysqldump.
4. Configure a custom domain using Route 53.
5. Enable HTTPS using SSL.
6. Migrate the database from EC2 to Amazon RDS.
7. Configure an Application Load Balancer.
8. Configure CloudWatch monitoring and alarms.
9. Implement student login and authentication.
10. Add an Edit Student functionality.

## 10. Learning Outcomes

After completing this project, you will understand:

- How to launch and configure an EC2 instance.
- How to install and configure the LEMP stack.
- How Nginx communicates with PHP-FPM.
- How PHP connects to MariaDB.
- How to deploy a PHP application from GitHub.
- How to configure Nginx virtual hosts.
- How to troubleshoot web server and database issues.
- How to host a dynamic web application on AWS.

## 11. Author


**Swati Zampal**
**Fortune Cloud Technologies – Hadapsar, Pune**

AWS Cloud Computing | DevOps | Full Stack Development

---

**Project:** Student Management Portal

**Deployment Platform:** AWS EC2

**Stack:** LEMP (Linux, Nginx, MariaDB, PHP)
