INSERT INTO products (slug, title, description, content_list, meta_keywords, image, category, level, brand, accent_color, badge_recommended, tutorial_link, price, status, is_active)
VALUES
-- 1. Kit Start Manichiura - Incepatori
('kit-start-manichiura', 'Kit Start Manichiura - Incepatori', 'Pachet complet pentru debutul in meseria de tehnician unghii.', '<ul>\n<li>Lampa UV/LED 48W</li>\n<li>Pile 100/180</li>\n<li>Buffer</li>\n<li>Primer fara acid</li>\n<li>Gel constructor transparent</li>\n<li>Pensula gel #6</li>\n<li>Ulei cuticule</li>\n<li>Sabloane unghii</li>\n<li>Cleanser</li>\n<li>Dischete fara scame</li>\n</ul>', '', '', 'Kituri', 'Incepator', 'Universal', '#E91E63', 1, 'https://exemplu.ro/tutorial-kit-start', 399, 'disponibil', 1),
-- 2. Pachet Avansat - Tehnici cu Gel
('pachet-avansat-gel', 'Pachet Avansat - Tehnici cu Gel', 'Produse pentru tehnicieni care lucreaza cu geluri de constructie si design.', '<ul>\n<li>Gel cover nude</li>\n<li>Gel alb French</li>\n<li>Pensule fine pentru pictura</li>\n<li>Tipsuri transparente</li>\n<li>Lichid modelare</li>\n<li>Top coat fara strat de dispersie</li>\n<li>Cutter profesional</li>\n<li>Lampa LED 72W</li>\n</ul>', '', '', 'Kituri', 'Avansat', 'ProNails', '#9C27B0', 1, 'https://exemplu.ro/tutorial-gel-avansat', 599, 'disponibil', 1),
-- 3. Kit Nail Art - Creativitate si Detaliu
('kit-nail-art', 'Kit Nail Art - Creativitate si Detaliu', 'Set pentru realizarea modelelor artistice pe unghii.', '<ul>\n<li>Pensule nail art</li>\n<li>Pigmenti cromatici</li>\n<li>Folii transfer</li>\n<li>Stickere decorative</li>\n<li>Geluri colorate</li>\n<li>Dotting tools</li>\n<li>Top coat mat</li>\n</ul>', '', '', 'Kituri', 'Intermediar', 'NailArtX', '#FF9800', 0, 'https://exemplu.ro/tutorial-nail-art', 349, 'disponibil', 1),
-- 4. Pachet Igiena si Siguranta
('pachet-igiena-siguranta', 'Pachet Igiena si Siguranta', 'Produse esentiale pentru igiena, sterilizare si protectie.', '<ul>\n<li>Manusi nitril</li>\n<li>Masca faciala</li>\n<li>Dezinfectant suprafete</li>\n<li>Spray antibacterian</li>\n<li>Sterilizator UV</li>\n<li>Servetele alcoolice</li>\n</ul>', '', '', 'Consumabile', 'Toate nivelurile', 'SafeTouch', '#4CAF50', 0, 'https://exemplu.ro/tutorial-igiena', 199, 'disponibil', 1),
-- 5. Kit Cursant - Ideal pentru Workshopuri
('kit-cursant', 'Kit Cursant - Ideal pentru Workshopuri', 'Pachet dedicat cursantilor din cadrul atelierelor de formare.', '<ul>\n<li>Mini lampa UV</li>\n<li>Gel starter kit</li>\n<li>Pile si buffer</li>\n<li>Ulei cuticule</li>\n<li>Pensula universala</li>\n<li>Suport pentru maini</li>\n</ul>', '', '', 'Kituri', 'Incepator', 'EduNails', '#03A9F4', 0, 'https://exemplu.ro/tutorial-cursant', 249, 'disponibil', 1),
-- 1. Gel constructor transparent - 15ml
('gel-constructor-transparent', 'Gel constructor transparent - 15ml', 'Gel de constructie cu vascozitate medie, ideal pentru extensii.', '', '', '', 'Consumabile', 'Toate nivelurile', 'CrystalNails', '#607D8B', 0, '', 49, 'disponibil', 1),
-- 2. Lampa UV/LED 48W
('lampa-uvled-48w', 'Lampa UV/LED 48W', 'Lampa profesionala cu senzor si timer, potrivita pentru toate tipurile de gel.', '', '', '', 'Instrumente', 'Incepator / Avansat', 'SunOne', '#795548', 0, '', 159, 'disponibil', 1),
-- 3. Primer fara acid - 10ml
('primer-fara-acid', 'Primer fara acid - 10ml', 'Protejeaza unghia naturala si ofera aderenta excelenta pentru geluri.', '', '', '', 'Consumabile', 'Incepator', 'BasePro', '#FF5722', 0, '', 29, 'disponibil', 1),
-- 4. Pensule nail art - set 5 bucati
('pensule-nail-art', 'Pensule nail art - set 5 bucati', 'Pensule fine pentru detalii, pictura si design artistic.', '', '', '', 'Accesorii', 'Intermediar / Avansat', 'ArtLine', '#9E9E9E', 0, '', 39, 'disponibil', 1),
-- 5. Top coat mat - 10ml
('top-coat-mat', 'Top coat mat - 10ml', 'Finish mat pentru unghii cu aspect modern si elegant.', '', '', '', 'Consumabile', 'Toate nivelurile', 'VelvetFinish', '#000000', 0, '', 35, 'disponibil', 1);
