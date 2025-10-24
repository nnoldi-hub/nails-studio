INSERT INTO products (slug, title, description, content_list, meta_keywords, image, category, level, brand, accent_color, badge_recommended, tutorial_link, price, status, is_active)
VALUES
-- 1. Kit Start Manichiură – Începători
('kit-start-manichiura', 'Kit Start Manichiură – Începători', 'Pachet complet pentru debutul în meseria de tehnician unghii.', '<ul>\n<li>Lampă UV/LED 48W</li>\n<li>Pile 100/180</li>\n<li>Buffer</li>\n<li>Primer fără acid</li>\n<li>Gel constructor transparent</li>\n<li>Pensulă gel #6</li>\n<li>Ulei cuticule</li>\n<li>Șabloane unghii</li>\n<li>Cleanser</li>\n<li>Dischete fără scame</li>\n</ul>', '', '', 'Kituri', 'Începător', 'Universal', '#E91E63', 1, 'https://exemplu.ro/tutorial-kit-start', 399, 'disponibil', 1),
-- 2. Pachet Avansat – Tehnici cu Gel
('pachet-avansat-gel', 'Pachet Avansat – Tehnici cu Gel', 'Produse pentru tehnicieni care lucrează cu geluri de construcție și design.', '<ul>\n<li>Gel cover nude</li>\n<li>Gel alb French</li>\n<li>Pensule fine pentru pictură</li>\n<li>Tipsuri transparente</li>\n<li>Lichid modelare</li>\n<li>Top coat fără strat de dispersie</li>\n<li>Cutter profesional</li>\n<li>Lampă LED 72W</li>\n</ul>', '', '', 'Kituri', 'Avansat', 'ProNails', '#9C27B0', 1, 'https://exemplu.ro/tutorial-gel-avansat', 599, 'disponibil', 1),
-- 3. Kit Nail Art – Creativitate și Detaliu
('kit-nail-art', 'Kit Nail Art – Creativitate și Detaliu', 'Set pentru realizarea modelelor artistice pe unghii.', '<ul>\n<li>Pensule nail art</li>\n<li>Pigmenți cromatici</li>\n<li>Folii transfer</li>\n<li>Stickere decorative</li>\n<li>Geluri colorate</li>\n<li>Dotting tools</li>\n<li>Top coat mat</li>\n</ul>', '', '', 'Kituri', 'Intermediar', 'NailArtX', '#FF9800', 0, 'https://exemplu.ro/tutorial-nail-art', 349, 'disponibil', 1),
-- 4. Pachet Igienă și Siguranță
('pachet-igiena-siguranta', 'Pachet Igienă și Siguranță', 'Produse esențiale pentru igienă, sterilizare și protecție.', '<ul>\n<li>Mănuși nitril</li>\n<li>Masca facială</li>\n<li>Dezinfectant suprafețe</li>\n<li>Spray antibacterian</li>\n<li>Sterilizator UV</li>\n<li>Șervețele alcoolice</li>\n</ul>', '', '', 'Consumabile', 'Toate nivelurile', 'SafeTouch', '#4CAF50', 0, 'https://exemplu.ro/tutorial-igiena', 199, 'disponibil', 1),
-- 5. Kit Cursant – Ideal pentru Workshopuri
('kit-cursant', 'Kit Cursant – Ideal pentru Workshopuri', 'Pachet dedicat cursanților din cadrul atelierelor de formare.', '<ul>\n<li>Mini lampă UV</li>\n<li>Gel starter kit</li>\n<li>Pile și buffer</li>\n<li>Ulei cuticule</li>\n<li>Pensulă universală</li>\n<li>Suport pentru mâini</li>\n</ul>', '', '', 'Kituri', 'Începător', 'EduNails', '#03A9F4', 0, 'https://exemplu.ro/tutorial-cursant', 249, 'disponibil', 1),
-- 1. Gel constructor transparent – 15ml
('gel-constructor-transparent', 'Gel constructor transparent – 15ml', 'Gel de construcție cu vâscozitate medie, ideal pentru extensii.', '', '', '', 'Consumabile', 'Toate nivelurile', 'CrystalNails', '#607D8B', 0, '', 49, 'disponibil', 1),
-- 2. Lampă UV/LED 48W
('lampa-uvled-48w', 'Lampă UV/LED 48W', 'Lampă profesională cu senzor și timer, potrivită pentru toate tipurile de gel.', '', '', '', 'Instrumente', 'Începător / Avansat', 'SunOne', '#795548', 0, '', 159, 'disponibil', 1),
-- 3. Primer fără acid – 10ml
('primer-fara-acid', 'Primer fără acid – 10ml', 'Protejează unghia naturală și oferă aderență excelentă pentru geluri.', '', '', '', 'Consumabile', 'Începător', 'BasePro', '#FF5722', 0, '', 29, 'disponibil', 1),
-- 4. Pensule nail art – set 5 bucăți
('pensule-nail-art', 'Pensule nail art – set 5 bucăți', 'Pensule fine pentru detalii, pictură și design artistic.', '', '', '', 'Accesorii', 'Intermediar / Avansat', 'ArtLine', '#9E9E9E', 0, '', 39, 'disponibil', 1),
-- 5. Top coat mat – 10ml
('top-coat-mat', 'Top coat mat – 10ml', 'Finish mat pentru unghii cu aspect modern și elegant.', '', '', '', 'Consumabile', 'Toate nivelurile', 'VelvetFinish', '#000000', 0, '', 35, 'disponibil', 1);
