-- ============================================================
-- Populate gallery table from cows, breeds, and events
-- ============================================================

USE `kamadhenu_goushala`;

-- 1. Insert Cow Photos
INSERT INTO `gallery` (`category_id`, `image_path`, `caption`, `alt_text`, `sort_order`, `is_active`)
SELECT 
    2 as category_id,
    CONCAT('../cows/', c.photo) as image_path,
    CONCAT(c.name, ' - ', COALESCE(b.name, 'Indigenous'), ' Breed') as caption,
    c.name as alt_text,
    100 as sort_order,
    1 as is_active
FROM cows c
LEFT JOIN breeds b ON c.breed_id = b.id
WHERE c.photo IS NOT NULL AND c.photo != ''
  AND NOT EXISTS (
      SELECT 1 FROM gallery WHERE image_path = CONCAT('../cows/', c.photo)
  );

-- 2. Insert Breed Photos
INSERT INTO `gallery` (`category_id`, `image_path`, `caption`, `alt_text`, `sort_order`, `is_active`)
SELECT 
    1 as category_id,
    CONCAT('../breeds/', br.image) as image_path,
    CONCAT(br.name, ' Breed') as caption,
    br.name as alt_text,
    110 as sort_order,
    br.is_active
FROM breeds br
WHERE br.image IS NOT NULL AND br.image != ''
  AND NOT EXISTS (
      SELECT 1 FROM gallery WHERE image_path = CONCAT('../breeds/', br.image)
  );

-- 3. Insert Event Photos
INSERT INTO `gallery` (`category_id`, `image_path`, `caption`, `alt_text`, `sort_order`, `is_active`)
SELECT 
    4 as category_id,
    CONCAT('../events/', ev.image) as image_path,
    ev.title as caption,
    ev.title as alt_text,
    120 as sort_order,
    1 as is_active
FROM events ev
WHERE ev.image IS NOT NULL AND ev.image != ''
  AND NOT EXISTS (
      SELECT 1 FROM gallery WHERE image_path = CONCAT('../events/', ev.image)
  );
