# Sinovi: Secure Copyright Registration

## Overview
Sinovi is a secure platform designed to protect National ID and Passport images for students and teachers, ensuring the confidentiality of sensitive information. The project leverages **AES-256 encryption** for data security and **Tesseract OCR** to streamline the extraction of text from uploaded images, enhancing the copyright registration process.

---

## Key Features

### 🔒 **Secure Storage**  
- All sensitive data, including National ID and Passport images, is encrypted using **AES-256 encryption**, ensuring it remains protected from unauthorized access and data breaches.

### 📋 **Waterfall Development Methodology**  
- The project follows a **Waterfall** development approach, ensuring each phase of the project is completed in a structured, linear manner to maintain efficiency and quality.

### 🔐 **AES-256 Encryption**  
- Data is encrypted using **Advanced Encryption Standard (AES-256)**, one of the most secure encryption methods available.

### 📄 **OCR with Tesseract**  
- Utilizes **Tesseract OCR** to extract text from images, allowing seamless data retrieval for students and teachers.

### 🎓 **User-Friendly Platform**  
- Accessible and easy to use for both **students and teachers**, simplifying the copyright registration process.

---

## Technology Stack

- **Framework:** Laravel  
- **Encryption:** AES-256  
- **OCR Engine:** Tesseract OCR  
- **Development Methodology:** Waterfall

---

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd sinovi
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Configure the .env file:

    i. Set the database connection details in the .env file.
    ```bash
    cp .env.example .env
    ```
    ii. Set the required variables, including an AES encryption key, Mailtrap credentials, and database configuration (leave the password field empty).
        Use the following sample configuration:
        .env File Configuration:

        APP_NAME=Sinovi
        APP_ENV=local
        APP_KEY=base64:YOUR_AES_ENCRYPTION_KEY_HERE
        APP_DEBUG=true
        APP_URL=http://localhost

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=sinovi_db
        DB_USERNAME=root
        DB_PASSWORD=

        MAIL_MAILER=smtp
        MAIL_HOST=smtp.mailtrap.io
        MAIL_PORT=2525
        MAIL_USERNAME=your_mailtrap_username
        MAIL_PASSWORD=your_mailtrap_password

        AES_ENCRYPTION_KEY=YOUR_AES_ENCRYPTION_KEY_HERE

    iii. Generate a new Application Key in the .env file:
        ```bash
        php artisan key:generate
       ```
    iv. Migrate the database:
        ```bash
        php artisan migrate
        ```
    v. Seed the database:
        ```bash
        php artisan db:seed
        ```
    vi. Run the application:
        ```bash
        php artisan serve
        ```


