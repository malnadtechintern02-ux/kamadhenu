-- ============================================================
-- Update Seed Data with Actual Image Filenames
-- Run after schema.sql and seed.sql
-- ============================================================

USE `kamadhenu_goushala`;

-- 1. Update Breeds
UPDATE `breeds` SET `image` = 'gir.jpg' WHERE `slug` = 'gir';
UPDATE `breeds` SET `image` = 'hallikar.jpg' WHERE `slug` = 'hallikar';
UPDATE `breeds` SET `image` = 'malenadu-gidda.jpg' WHERE `slug` = 'malenadu-gidda';
UPDATE `breeds` SET `image` = 'amritamahal.jpg' WHERE `slug` = 'amritamahal';
UPDATE `breeds` SET `image` = 'tharparkar.jpg' WHERE `slug` = 'tharparkar';

-- 2. Update Cows
UPDATE `cows` SET `photo` = 'gir-cow-1.jpg' WHERE `name` = 'Lakshmi';
UPDATE `cows` SET `photo` = 'balarama.jpg' WHERE `name` = 'Nandini';
UPDATE `cows` SET `photo` = 'nandi.jpg' WHERE `name` = 'Kamala';
UPDATE `cows` SET `photo` = 'gauri.jpg' WHERE `name` = 'Gauri';
UPDATE `cows` SET `photo` = 'kamadhenu.jpg' WHERE `name` = 'Surabhi';
UPDATE `cows` SET `photo` = 'gir-cow-1.jpg' WHERE `name` = 'Ganga';
UPDATE `cows` SET `photo` = 'balarama.jpg' WHERE `name` = 'Dhenu';
UPDATE `cows` SET `photo` = 'nandi.jpg' WHERE `name` = 'Parvati';

-- 3. Update Products
UPDATE `products` SET `image` = 'ghee.jpg' WHERE `slug` = 'pure-desi-cow-ghee-500ml';
UPDATE `products` SET `image` = 'ghee.jpg' WHERE `slug` = 'pure-desi-cow-ghee-1litre';
UPDATE `products` SET `image` = 'dhoop-batti.jpg' WHERE `slug` = 'panchagavya-dhoop-batti';
UPDATE `products` SET `image` = 'diyas.jpg' WHERE `slug` = 'cow-dung-diyas-set-12';
UPDATE `products` SET `image` = 'gomutra.jpg' WHERE `slug` = 'organic-gomutra-ark-500ml';
UPDATE `products` SET `image` = 'sambrani.jpg' WHERE `slug` = 'cow-dung-sambrani-cups';

-- 4. Update Events
UPDATE `events` SET `image` = 'annadana.jpg' WHERE `slug` = 'gau-seva-maha-annadana';
UPDATE `events` SET `image` = 'medical-camp.jpg' WHERE `slug` = 'monthly-cow-health-camp';
UPDATE `events` SET `image` = 'gopashtami.jpg' WHERE `slug` = 'gopuja-mahotsava';
UPDATE `events` SET `image` = 'medical-camp.jpg' WHERE `slug` = 'indigenous-breed-awareness-workshop';

-- 5. Update News
UPDATE `news` SET `featured_image` = 'gopashtami.jpg' WHERE `slug` = 'kamadenu-goushala-completes-6-years';
UPDATE `news` SET `featured_image` = 'medical-camp.jpg' WHERE `slug` = 'successful-rescue-three-abandoned-cows';
UPDATE `news` SET `featured_image` = 'annadana.jpg' WHERE `slug` = 'understanding-a2-milk-benefits';
