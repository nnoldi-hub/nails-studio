<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Array of initial pages
$initial_pages = [
    [
        'slug' => 'index',
        'title' => 'Acasă',
        'content' => '<h2>Bine ați venit la Nail Studio Andreea!</h2>',
        'meta_title' => 'Nail Studio Andreea - Acasă',
        'meta_description' => 'Salon profesional de manichiură, pedichiură și nail art.',
        'meta_keywords' => 'nails, salon, manichiura, pedichiura',
        'color' => '#e91e63',
        'image' => 'assets/images/gallery/default_home.jpg'
    ],
    [
        'slug' => 'services',
        'title' => 'Servicii',
        'content' => '<h2>Serviciile Noastre</h2>',
        'meta_title' => 'Servicii Nail Studio Andreea',
        'meta_description' => 'Listă servicii manichiură, pedichiură, nail art.',
        'meta_keywords' => 'servicii, manichiura, pedichiura, nail art',
        'color' => '#17a2b8',
        'image' => 'assets/images/services/default_services.jpg'
    ],
    [
        'slug' => 'gallery',
        'title' => 'Galerie',
        'content' => '<h2>Galerie Foto</h2>',
        'meta_title' => 'Galerie Nail Studio Andreea',
        'meta_description' => 'Imagini cu lucrări de manichiură și pedichiură.',
        'meta_keywords' => 'galerie, poze, nail art',
        'color' => '#ffc107',
        'image' => 'assets/images/gallery/default_gallery.jpg'
    ],
    [
        'slug' => 'coaching',
        'title' => 'Coaching',
        'content' => '<h2>Sesiuni de coaching</h2>',
        'meta_title' => 'Coaching Nail Studio Andreea',
        'meta_description' => 'Cursuri și sesiuni de coaching pentru profesioniști.',
        'meta_keywords' => 'coaching, cursuri, nail art',
        'color' => '#4caf50',
        'image' => 'assets/images/gallery/default_coaching.jpg'
    ],
    [
        'slug' => 'appointment',
        'title' => 'Programare',
        'content' => '<h2>Rezervă o programare</h2>',
        'meta_title' => 'Programare Nail Studio Andreea',
        'meta_description' => 'Rezervă online o programare la salon.',
        'meta_keywords' => 'programare, rezervare, salon',
        'color' => '#6f42c1',
        'image' => 'assets/images/gallery/default_appointment.jpg'
    ],
    [
        'slug' => 'contact',
        'title' => 'Contact',
        'content' => '<h2>Contactează-ne</h2>',
        'meta_title' => 'Contact Nail Studio Andreea',
        'meta_description' => 'Formular de contact și date de localizare.',
        'meta_keywords' => 'contact, programare, salon',
        'color' => '#5a5c69',
        'image' => 'assets/images/gallery/default_contact.jpg'
    ]
];

foreach ($initial_pages as $page) {
    // Verifică dacă slug-ul există deja
    $check = $conn->prepare("SELECT id FROM pages WHERE slug = ? LIMIT 1");
    $check->bind_param('s', $page['slug']);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO pages (slug, title, content, meta_title, meta_description, meta_keywords, color, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'ssssssss',
            $page['slug'],
            $page['title'],
            $page['content'],
            $page['meta_title'],
            $page['meta_description'],
            $page['meta_keywords'],
            $page['color'],
            $page['image']
        );
        $stmt->execute();
        $stmt->close();
    }
    $check->close();
}

echo "Pagini inițiale adăugate cu succes!";
