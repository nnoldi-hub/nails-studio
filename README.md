# Nail Studio Andreea - Website & Management System

Un sistem web complet pentru salonul de unghii "Nail Studio Andreea", incluzând site-ul public și panoul de administrare.

## 📋 Caracteristici

### Site Public
- **Pagina principală** cu prezentarea salonului
- **Servicii** - lista completă a serviciilor oferite
- **Galerie** - prezentarea lucrărilor realizate
- **Cursuri de Coaching** - program educațional
- **Contact** - informații de contact și formular de mesaje
- **Sistem de programări online**

### Panou de Administrare
- **Dashboard** cu statistici generale
- **Gestionarea programărilor** - confirmare, modificare, anulare
- **Gestionarea serviciilor** - adăugare, editare, ștergere
- **Gestionarea galeriei** - upload și organizare imagini
- **Gestionarea cursurilor de coaching**
- **Mesaje de contact** - vizualizare și răspuns

## 🛠️ Tehnologii Utilizate

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Baza de date**: MySQL 5.7+
- **Server web**: Apache (WAMP/XAMPP)

## 📁 Structura Proiectului

```
A_nails/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── includes/
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── admin/
│   ├── includes/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── appointments.php
│   ├── services.php
│   ├── gallery.php
│   ├── coaching.php
│   └── messages.php
├── sql/
│   └── database_schema.sql
├── index.php
├── services.php
├── gallery.php
├── coaching.php
├── contact.php
├── appointment.php
└── README.md
```

## 🚀 Instalare și Configurare

### 1. Cerințe de Sistem
- PHP 7.4 sau superior
- MySQL 5.7 sau superior
- Apache Web Server
- WAMP/XAMPP/LAMP

### 2. Pași de Instalare

1. **Clonează/Descarcă proiectul** în directorul `www` al serverului local
2. **Configurează baza de date**:
   - Creează o bază de date nouă în phpMyAdmin
   - Importă fișierul `sql/database_schema.sql`
3. **Configurează conexiunea**:
   - Editează `includes/config.php`
   - Actualizează datele de conexiune la baza de date
4. **Configurează permisiunile**:
   - Asigură-te că directorul `assets/images/` are permisiuni de scriere

### 3. Configurare Avansată

#### Configurarea bazei de date (`includes/config.php`):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nail_studio_andreea');
```

#### Configurarea site-ului:
```php
define('SITE_NAME', 'Nail Studio Andreea');
define('SITE_URL', 'http://localhost/A_nails');
define('ADMIN_EMAIL', 'andreea@nailstudio.com');
```

## 👤 Acces Administrator

### Date de conectare implicite:
- **Username**: `admin`
- **Parola**: `admin123`

**Important**: Schimbă parola imediat după prima conectare!

## 📊 Funcționalități Baza de Date

### Tabele principale:
- `services` - Serviciile oferite
- `appointments` - Programările clienților
- `gallery` - Imaginile din galerie
- `coaching_sessions` - Sesiunile de coaching
- `coaching_bookings` - Rezervările pentru cursuri
- `contact_messages` - Mesajele de contact
- `admin_users` - Utilizatorii administratori

## 🎨 Personalizare

### Culori și Stiluri
Editează fișierul `assets/css/style.css` pentru a modifica:
- Culorile principale (variabilele CSS din `:root`)
- Fonturile și stilurile
- Layout-ul și aspectul

### Imagini
- Logo-ul salonului: `assets/images/logo.png`
- Imagini hero: `assets/images/hero-image.jpg`
- Imagini servicii: `assets/images/service-*.jpg`
- Imagini galerie: `assets/images/gallery-*.jpg`

## 📱 Responsive Design

Site-ul este complet responsive și optimizat pentru:
- Desktop (1200px+)
- Laptop (992px - 1199px)
- Tablet (768px - 991px)
- Mobile (576px - 767px)
- Mobile mic (sub 576px)

## 🔒 Securitate

### Măsuri implementate:
- Validarea și sanitizarea tuturor datelor de intrare
- Protecție împotriva SQL Injection
- Autentificare sigură pentru administratori
- Validarea permisiunilor pentru zonele administrative

### Recomandări suplimentare:
- Schimbă parola administratorului
- Folosește HTTPS în producție
- Actualizează regulat PHP și MySQL
- Fă backup-uri regulate ale bazei de date

## 📧 Configurarea Email-urilor

Pentru notificări email (programări, mesaje), configurează:
1. Serverul SMTP în `includes/config.php`
2. Credențialele de email
3. Activează funcțiile de notificare

## 🚀 Deployment în Producție

### Lista de verificare:
1. ✅ Configurează domeniul și hosting-ul
2. ✅ Încarcă fișierele pe server
3. ✅ Creează baza de date pe server
4. ✅ Actualizează `config.php` cu datele serverului
5. ✅ Configurează SSL/HTTPS
6. ✅ Testează toate funcționalitățile
7. ✅ Configurează backup-urile automate

## 🐛 Depanare

### Probleme comune:

**Eroare de conexiune la baza de date:**
- Verifică datele din `config.php`
- Asigură-te că MySQL rulează
- Verifică permisiunile utilizatorului

**Imagini care nu se afișează:**
- Verifică permisiunile directorului `assets/images/`
- Asigură-te că căile către imagini sunt corecte

**Probleme cu sesiunile:**
- Verifică configurarea PHP pentru sesiuni
- Asigură-te că directorul pentru sesiuni are permisiuni

## 📞 Support și Contact

Pentru întrebări sau probleme tehnice:
- Email: support@nailstudioandreea.ro
- Telefon: +40 123 456 789

## 📄 Licență

Acest proiect este protejat de drepturi de autor. Utilizarea comercială necesită acordul proprietarului.

---

**Dezvoltat cu ❤️ pentru Nail Studio Andreea**
