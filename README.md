# React-Php-Form
A full-stack form submission application built with React (frontend) and PHP (backend)

## Author

This application was created by **Leca Marian-Cristian**.

### Estimated Time
- **Frontend**: 2 hours
- **Server-Side Logic**: 2 hour

 
# Full-Stack React + PHP App (No Frameworks)

Această aplicație Full-Stack a fost construită folosind React pe frontend și PHP (stil OOP) pe backend, fără a utiliza framework-uri. Aplicația include un formular pe frontend și o API pe backend pentru gestionarea datelor trimise.

## Features

### Frontend (React)
- Formular responsiv cu următoarele câmpuri:
  - **Email** (text)
  - **Name** (text)
  - **Consent** (checkbox)
  - **Image Upload** (file input)
- Validare de bază pe client
- Stilizat pentru a se potrivi unui șablon NicePage

### Backend (PHP, OOP, fără Framework-uri)
- **POST /api/submit.php**
  - Acceptă datele formularului
  - Validare câmpuri:
    - Consimțământul este obligatoriu dacă imaginea este încărcată
    - Imaginea este redimensionată dacă lățimea/înălțimea depășește 500px
  - Salvează imaginea pe disc și stochează un record în MySQL
- **GET /api/records.php**
  - Returnează toate înregistrările trimise în format JSON
  - Suportă parametrii opționali:
    - `?sort=email&order=asc`
    - `?page=1&limit=10`
- **GET /api/export.php**
  - Exportă înregistrările în format CSV, codificat UTF-8

- **Middleware de autentificare**
  - Toate endpoint-urile necesită autorizare: 
    `Authorization: Bearer YOUR_STATIC_TOKEN_HERE`
  - Returnează 401 Unauthorized dacă token-ul este lipsă sau invalid

## Tech Stack

- **Frontend:** React, HTML5, CSS3
- **Backend:** PHP (OOP), GD Library pentru procesarea imaginilor
- **Database:** MySQL

## Setup Instructions

### Backend

1. Creaza DB:
   ```sql
   CREATE DATABASE users;

2. Clonează repo-ul și configurează baza de date:
   ```sql
   CREATE TABLE submissions (
     id INT AUTO_INCREMENT PRIMARY KEY,
     email VARCHAR(255),
     name VARCHAR(255),
     consent BOOLEAN,
     image_path VARCHAR(255),
     created_at DATETIME DEFAULT CURRENT_TIMESTAMP
   );


2. Copiază fișierul `.env.example` și creează un fișier `.env` în folderul principal. (./REACT-PHP-FORM)

3. Porneste serverul: 

php -S localhost:8000


### Frontend
1. Navighează în directorul ReactForm și instalează dependințele:

cd ReactForm
npm install
npm start

2. Toate endpoint-urile backend necesită un token static în header:

Authorization: Bearer YOUR_STATIC_TOKEN_HERE
