-- ============================================================
-- Kamadenu Goushala – Seed Data
-- Run AFTER schema.sql
-- ============================================================

USE `kamadhenu_goushala`;

-- ============================================================
-- ADMIN (password: admin123)
-- ============================================================
INSERT INTO `admins` (`username`, `email`, `password_hash`, `full_name`) VALUES
('admin', 'admin@kamadenugoushala.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- ============================================================
-- SETTINGS
-- ============================================================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
-- General
('site_name', 'Kamadenu Goushala', 'general'),
('site_tagline', 'Protecting Gau Mata. Preserving Our Heritage.', 'general'),
('site_description', 'Kamadhenu Goushala is dedicated to protecting indigenous Indian cow breeds and promoting sustainable agriculture practices rooted in our ancient traditions.', 'general'),
('site_logo', '', 'general'),
('site_favicon', '', 'general'),
('footer_text', 'Dedicated to protecting and serving Gau Mata with devotion. Your support helps us provide food, shelter, and medical care to rescued and protected cows.', 'general'),

-- Contact
('phone', '[PHONE NUMBER]', 'contact'),
('email', '[EMAIL ADDRESS]', 'contact'),
('whatsapp', '[WHATSAPP NUMBER]', 'contact'),
('address', '[GOUSHALA ADDRESS]', 'contact'),
('google_maps_url', '', 'contact'),

-- Social
('facebook_url', '', 'social'),
('instagram_url', '', 'social'),
('youtube_url', '', 'social'),
('twitter_url', '', 'social'),

-- Donation
('donation_upi_id', '[UPI ID]', 'donation'),
('razorpay_key_id', '', 'donation'),
('razorpay_key_secret', '', 'donation'),
('donation_enabled', '1', 'donation'),

-- Stats (editable from admin)
('stat_total_cows', '70', 'stats'),
('stat_rescued_cows', '45', 'stats'),
('stat_seva_programs', '8', 'stats'),
('stat_years_service', '6', 'stats');

-- ============================================================
-- BREEDS
-- ============================================================
INSERT INTO `breeds` (`name`, `slug`, `origin`, `description`, `milk_quality`, `characteristics`, `image`, `sort_order`) VALUES
('Gir', 'gir', 'Gir forest region, Gujarat', 
'The Gir cow is one of the most prized indigenous breeds of India, originally from the Gir forest region of Gujarat. Known for their distinctive appearance with a large rounded forehead and long pendulous ears, Gir cows are gentle, docile and highly adaptable. They are among the most productive indigenous dairy breeds and have been exported to many countries for improving local breeds.',
'Known for premium A2 milk with high nutritional value, useful in traditional ghrita and wellness formulations. Gir cows can produce 6-10 litres of milk per day with high fat content.',
'Distinctive rounded forehead, long pendulous ears, and curved horns. Hardy breed resistant to tropical diseases. Gentle temperament makes them ideal for goushalas.',
NULL, 1),

('Hallikar', 'hallikar', 'Southern Karnataka', 
'Hallikar is one of the premier draught cattle breeds of India, native to the dry regions of Southern Karnataka. These magnificent animals are known for their strength, endurance and compact muscular build. They have been an integral part of Karnataka''s agricultural heritage for centuries.',
'Moderate milk yield with notably rich fat content, traditionally used in desi dairy households. The milk is considered excellent for making ghee and curd.',
'Well-built compact body with a distinctive grey or white coat. Strong shoulders and well-developed dewlap. Known for their endurance in hot climates and resistance to diseases.',
NULL, 2),

('Malenadu Gidda', 'malenadu-gidda', 'Malnad region of Karnataka', 
'The Malenadu Gidda is a small-sized indigenous breed native to the Western Ghats region of Karnataka. Despite their compact size, they are remarkably hardy and well-adapted to the hilly, high-rainfall terrain of the Malnad region. They represent an important genetic resource for conservation.',
'High-fat milk (about 4-6%) traditionally valued for digestibility and medicated preparations. Though quantity is less, the quality of A2 milk is exceptional for traditional dairy products.',
'Small compact body suited to hilly terrain. Usually dark brown or black coat. Extremely hardy with natural resistance to parasites and diseases common in Western Ghats region.',
NULL, 3),

('Amritamahal', 'amritamahal', 'Karnataka', 
'Amritamahal is a famous draught breed of cattle from Karnataka, historically maintained by the kings of Mysore for their army. The name literally means "department of milk" in Kannada. They are known for their fiery temperament, speed, and incredible stamina.',
'Lower milk yield than specialized dairy breeds, but milk is valued in traditional desi systems. The milk has high medicinal value according to Ayurvedic traditions.',
'Tall with well-proportioned body, long face, and distinctive upward-curving horns. Grey or white coat with darker shading on neck and humps. Known for speed, endurance and spirited temperament.',
NULL, 4),

('Tharparkar', 'tharparkar', 'Thar desert region, Rajasthan-Sindh', 
'The Tharparkar breed originates from the Thar Desert region, spanning Rajasthan and the former Sindh province. This dual-purpose breed is one of the few indigenous Indian breeds valued equally for both milk production and draught capability. Their adaptation to extreme desert conditions makes them invaluable.',
'A2-rich milk with traditionally valued immune-supportive and probiotic qualities in rural health practice. Can produce 8-12 litres of milk per day, making it one of the highest-yielding indigenous breeds.',
'Medium to large-sized with a white or grey coat. Deep body with straight back. Excellent heat tolerance adapted to arid conditions. Known as one of the best dual-purpose indigenous breeds.',
NULL, 5);

-- ============================================================
-- COWS
-- ============================================================
INSERT INTO `cows` (`name`, `breed_id`, `gender`, `date_of_birth`, `description`, `rescue_story`, `health_status`, `is_adoptable`, `is_featured`, `status`, `monthly_adoption_amount`) VALUES
('Lakshmi', 1, 'Female', '2019-03-15', 
'Lakshmi is a beautiful Gir cow with a gentle and loving temperament. She is one of the most beloved residents of our Goushala and has been with us since 2020.',
'Lakshmi was rescued from a dairy farm where she was no longer considered productive. She was malnourished and in poor health when she arrived. With love and proper care, she has fully recovered and now lives a peaceful life at our Goushala.',
'Healthy', 1, 1, 'Available', 2500.00),

('Nandini', 3, 'Female', '2020-06-20', 
'Nandini is a spirited Malenadu Gidda cow. Despite her small size, she has an enormous personality and is a favourite among visitors and volunteers.',
'Nandini was found abandoned near a forest area in the Malnad region. Local villagers contacted us, and we brought her to the Goushala where she has thrived.',
'Healthy', 1, 1, 'Available', 2000.00),

('Kamala', 2, 'Female', '2018-11-08', 
'Kamala is a majestic Hallikar cow who commands respect with her dignified presence. She is one of the senior residents of our Goushala.',
'Kamala was rescued from a situation where she was being used for illegal transport. She arrived with injuries to her legs. After months of veterinary care, she recovered completely.',
'Healthy', 1, 1, 'Available', 2500.00),

('Gauri', 1, 'Female', '2021-01-10', 
'Gauri is a young Gir cow with beautiful markings. She is playful, curious, and loves human attention.',
'Gauri was born at a cattle camp during floods. Her mother could not be located, and she was brought to our Goushala as an orphan calf. She has grown into a healthy, happy cow.',
'Healthy', 1, 1, 'Available', 2500.00),

('Surabhi', 4, 'Female', '2017-08-25', 
'Surabhi is a magnificent Amritamahal cow with the characteristic spirited temperament of her breed. She is strong, active, and a natural leader in the herd.',
'Surabhi was rescued from a neglected cattle shed in rural Karnataka. She was severely underweight and had untreated hoof problems. After extensive medical care, she has made a remarkable recovery.',
'Healthy', 0, 0, 'Permanent Resident', 3000.00),

('Ganga', 5, 'Female', '2020-09-14', 
'Ganga is a beautiful Tharparkar cow known for her calm demeanour and excellent milk quality. She has adapted wonderfully to the Goushala environment.',
'Ganga was part of a group of cows rescued from an illegal slaughterhouse transport. She was traumatized when she arrived but has slowly regained trust in humans.',
'Healthy', 1, 1, 'Available', 2500.00),

('Dhenu', 3, 'Female', '2022-04-02', 
'Dhenu is one of the youngest members of our Malenadu Gidda family. She is energetic, playful and growing beautifully.',
'Dhenu was found wandering alone on a highway. A kind motorist contacted us, and we brought her to safety. She quickly bonded with our other Malenadu Gidda cows.',
'Healthy', 1, 0, 'Available', 2000.00),

('Parvati', 2, 'Female', '2019-12-30', 
'Parvati is a gentle Hallikar cow with a calm and maternal nature. She has helped nurture several orphaned calves at the Goushala.',
'Parvati was surrendered by a farmer who could no longer care for her. She arrived in good health and immediately became a calming presence for other cows.',
'Healthy', 1, 0, 'Adopted', 2500.00);

-- ============================================================
-- SEVA CATEGORIES
-- ============================================================
INSERT INTO `seva_categories` (`title`, `slug`, `icon`, `short_description`, `description`, `benefits`, `suggested_amounts`, `sort_order`) VALUES
('Feed a Cow', 'feed-a-cow', 'bi-heart-fill',
'Sponsor daily food for cows and support their nourishment.',
'Feeding a cow is one of the most sacred and meritorious acts in Sanatan Dharma. Your contribution ensures that our cows receive nutritious food including green fodder, dry fodder, grains, jaggery, and mineral supplements every single day. Each cow at our Goushala requires approximately ₹80-100 worth of food daily.',
'According to scriptures, feeding a cow is equivalent to performing a Yagna. It brings peace, prosperity and removes obstacles. Your Gau Graas seva directly sustains the life of sacred Gau Mata.',
'101,501,1001,2501,5001', 1),

('Adopt a Cow', 'adopt-a-cow', 'bi-house-heart-fill',
'Provide full monthly care and become a lifelong protector.',
'Adopting a cow means taking complete responsibility for her monthly care including food, shelter, medical expenses and daily maintenance. You can choose a specific cow from our Goushala and receive regular updates about her wellbeing. Adoption creates a personal bond between you and Gau Mata.',
'Cow adoption is considered one of the highest forms of Gau Seva. You become a direct protector of Gau Mata. You will receive regular photo and health updates of your adopted cow.',
'2000,2500,3000,5000', 2),

('Medical Seva', 'medical-seva', 'bi-plus-circle-fill',
'Support veterinary care, treatments and health camps for cows.',
'Our cows require regular veterinary checkups, vaccinations, deworming, and sometimes emergency medical treatment. Many rescued cows arrive with injuries, infections, or chronic conditions that need ongoing care. Your Medical Seva contribution helps us maintain a fully equipped medical facility.',
'Supporting cow medical care saves lives and alleviates suffering. Regular health camps benefit not just our cows but also those of local farmers in the community.',
'501,1001,2501,5001,11000', 3),

('Fodder Seva', 'fodder-seva', 'bi-flower1',
'Help us grow and procure quality fodder for our herd.',
'Quality fodder is the foundation of cow health. We maintain our own fodder cultivation and also procure additional fodder from local farmers. Your Fodder Seva helps us ensure a steady supply of nutritious green and dry fodder throughout the year, especially during the challenging summer months.',
'Fodder Seva directly contributes to the nutrition and health of every cow. Supporting fodder cultivation also helps local farmers and promotes sustainable agriculture.',
'501,1001,2501,5001', 4),

('Shelter Support', 'shelter-support', 'bi-building',
'Contribute to maintaining and expanding cow shelters.',
'A safe, clean, and comfortable shelter is essential for the well-being of our cows. Your contribution helps us maintain existing structures, build new shelters, and ensure proper drainage, ventilation, and hygiene. As we rescue more cows, the need for expanded shelter grows.',
'Providing shelter to Gau Mata is a sacred duty. Your contribution creates a safe haven where cows can live with dignity and comfort.',
'1001,2501,5001,11000,21000', 5),

('General Donation', 'general-donation', 'bi-gift-fill',
'Support the overall operations and mission of the Goushala.',
'A general donation supports all aspects of our Goushala operations — from daily cow care to infrastructure development, from community outreach to educational programs. Every rupee contributes to our mission of protecting and serving indigenous cows.',
'Your generous donation supports the holistic mission of Gau Seva. It enables us to respond to emergencies, plan for the future, and expand our service to more cows in need.',
'101,501,1001,2501,5001,11000', 6);

-- ============================================================
-- EVENTS
-- ============================================================
INSERT INTO `events` (`title`, `slug`, `event_date`, `event_time`, `location`, `short_description`, `description`, `is_featured`, `status`) VALUES
('Gau Seva Maha Annadana', 'gau-seva-maha-annadana', '2026-09-15', '09:00:00', 'Kavadi, Virajpet Taluk',
'A grand community gathering to celebrate Gau Seva through mass feeding and spiritual programs.',
'Join us for the Gau Seva Maha Annadana, a sacred event celebrating our devotion to Gau Mata. The event features community prayers, cow feeding ceremonies, distribution of Panchagavya products, and a grand Annadana (community feast). All are welcome to participate in this auspicious occasion.',
1, 'Upcoming'),

('Monthly Cow Health Camp', 'monthly-cow-health-camp', '2026-10-10', '10:00:00', 'Kamadhenu Goushala Campus',
'Regular veterinary health camp providing check-ups and treatments for all Goushala cows.',
'Our monthly health camp brings experienced veterinarians to the Goushala for comprehensive health check-ups of all our cows. Services include vaccination, deworming, dental check-ups, hoof trimming, and general health assessment. We also offer guidance to local farmers about indigenous cow health care.',
1, 'Upcoming'),

('Gopuja Mahotsava', 'gopuja-mahotsava', '2026-11-01', '06:00:00', 'Kamadhenu Goushala Campus',
'Annual grand celebration of Gau Mata with traditional Vedic rituals and community participation.',
'The Gopuja Mahotsava is our annual flagship event, featuring elaborate Vedic rituals honouring Gau Mata, cultural programs, discourses on the importance of indigenous cow protection, and a showcase of Goushala products. This year marks our 6th anniversary celebration.',
1, 'Upcoming'),

('Indigenous Breed Awareness Workshop', 'indigenous-breed-awareness-workshop', '2026-06-15', '10:00:00', 'Kavadi Community Hall',
'Educational workshop on the importance and characteristics of indigenous Indian cow breeds.',
'This workshop brought together farmers, students, and enthusiasts to learn about the rich heritage of indigenous Indian cow breeds. Topics covered included breed identification, A2 milk benefits, sustainable farming with indigenous cattle, and government support schemes for indigenous breed conservation.',
0, 'Completed');

-- ============================================================
-- NEWS CATEGORIES
-- ============================================================
INSERT INTO `news_categories` (`name`, `slug`) VALUES
('Goushala Updates', 'goushala-updates'),
('Cow Rescue', 'cow-rescue'),
('Events', 'events'),
('Education', 'education'),
('Community', 'community');

-- ============================================================
-- NEWS
-- ============================================================
INSERT INTO `news` (`title`, `slug`, `short_description`, `content`, `author`, `category_id`, `tags`, `published_date`, `status`, `seo_title`, `seo_description`) VALUES
('Kamadenu Goushala Completes 6 Years of Gau Seva', 'kamadenu-goushala-completes-6-years', 
'Celebrating six years of dedicated service to indigenous cow protection and Gau Seva.',
'<p>Kamadenu Goushala proudly marks its 6th anniversary of dedicated service to Gau Mata. Established on 24th August 2020 at Kavadi, Virajpet Taluk, Kodagu (Coorg), our journey began with a handful of cows and a vision rooted in ancient Indian traditions of cow protection.</p><p>Over these years, we have grown from a small shelter to a thriving Goushala that now cares for approximately 70 indigenous cows across five sacred breeds — Gir, Hallikar, Malenadu Gidda, Amritamahal, and Tharparkar.</p><p>Our heartfelt gratitude goes to all our donors, volunteers, and supporters who have made this journey possible. Your seva continues to transform lives — both of our beloved cows and the community we serve.</p>',
'Kamadenu Goushala', 1, 'anniversary,goushala,milestone', '2026-08-20', 'Published',
'Kamadenu Goushala Completes 6 Years of Gau Seva', 'Celebrating six years of dedicated service to indigenous cow protection at Kamadenu Goushala, Kavadi, Karnataka.'),

('Successful Rescue of Three Abandoned Cows', 'successful-rescue-three-abandoned-cows',
'Three indigenous cows rescued from neglect and brought to safety at our Goushala.',
'<p>Our rescue team recently responded to a distress call about three abandoned indigenous cows found in a dilapidated cattle shed on the outskirts of Virajpet. The cows — two Hallikar and one Malenadu Gidda — were severely malnourished and in need of immediate medical attention.</p><p>Thanks to the swift action of our volunteers and the cooperation of local authorities, all three cows were safely transported to Kamadenu Goushala. Our veterinary team immediately began treatment, providing nutrition supplements, deworming medication, and wound care.</p><p>After two weeks of dedicated care, all three cows have shown remarkable improvement and are now stable. They will continue to receive specialized care until they are fully recovered.</p>',
'Kamadenu Goushala', 2, 'rescue,cows,welfare', '2026-07-15', 'Published',
'Three Abandoned Cows Rescued by Kamadenu Goushala', 'Kamadenu Goushala rescues three abandoned indigenous cows and provides medical care and rehabilitation.'),

('Understanding A2 Milk: Benefits of Indigenous Cow Breeds', 'understanding-a2-milk-benefits',
'Learn about the health benefits of A2 milk from indigenous Indian cow breeds.',
'<p>In recent years, there has been growing awareness about the differences between A1 and A2 milk proteins. Indigenous Indian cow breeds such as Gir, Tharparkar, and Malenadu Gidda naturally produce A2 milk, which contains the A2 beta-casein protein.</p><p>Research suggests that A2 milk may be easier to digest and could offer several health benefits compared to conventional A1 milk from crossbred or European cattle breeds. Many people who experience discomfort with regular milk find A2 milk to be gentler on their digestive system.</p><p>At Kamadenu Goushala, we are committed to preserving these indigenous breeds not just for their cultural and spiritual significance, but also for the genuine health benefits their milk provides to the community.</p>',
'Kamadenu Goushala', 4, 'a2-milk,health,indigenous-breeds', '2026-06-01', 'Published',
'A2 Milk Benefits: Why Indigenous Cow Breeds Matter', 'Discover the health benefits of A2 milk from indigenous Indian cow breeds like Gir, Tharparkar, and Malenadu Gidda.');

-- ============================================================
-- GALLERY CATEGORIES
-- ============================================================
INSERT INTO `gallery_categories` (`name`, `slug`, `sort_order`) VALUES
('Goushala', 'goushala', 1),
('Our Cows', 'our-cows', 2),
('Seva Activities', 'seva-activities', 3),
('Events', 'events', 4),
('Volunteers', 'volunteers', 5),
('Festivals', 'festivals', 6),
('Rescue', 'rescue', 7);

-- ============================================================
-- PRODUCT CATEGORIES
-- ============================================================
INSERT INTO `product_categories` (`name`, `slug`) VALUES
('Ghee', 'ghee'),
('Panchagavya', 'panchagavya'),
('Cow Dung Products', 'cow-dung-products'),
('Dairy', 'dairy'),
('Organic', 'organic');

-- ============================================================
-- PRODUCTS
-- ============================================================
INSERT INTO `products` (`name`, `slug`, `description`, `price`, `category_id`, `stock_status`, `is_featured`, `is_active`) VALUES
('Pure Desi Cow Ghee (500ml)', 'pure-desi-cow-ghee-500ml',
'Handmade pure desi cow ghee from A2 milk of our indigenous cows. Made using traditional bilona method, this ghee retains all the natural goodness and aroma. Rich in vitamins A, D, E and K. Perfect for cooking, worship, and medicinal use.',
750.00, 1, 'In Stock', 1, 1),

('Pure Desi Cow Ghee (1 Litre)', 'pure-desi-cow-ghee-1litre',
'Premium 1-litre pack of handmade bilona ghee from A2 milk of indigenous breeds. Prepared with traditional methods to ensure maximum nutrition and authentic flavour.',
1400.00, 1, 'In Stock', 1, 1),

('Panchagavya Dhoop Batti', 'panchagavya-dhoop-batti',
'Hand-rolled dhoop battis made from cow dung, cow ghee, and natural herbs. Creates a purifying, aromatic atmosphere for prayer and meditation. Chemical-free and eco-friendly.',
150.00, 2, 'In Stock', 1, 1),

('Cow Dung Diyas (Set of 12)', 'cow-dung-diyas-set-12',
'Traditional diyas handcrafted from cow dung, perfect for Deepavali and daily pooja. Each diya is sun-dried and chemical-free, making them eco-friendly and sacred.',
200.00, 3, 'In Stock', 1, 1),

('Organic Gomutra Ark (500ml)', 'organic-gomutra-ark-500ml',
'Distilled and purified Gomutra Ark from indigenous cow breeds. Prepared following traditional Ayurvedic processes. Used for various health and spiritual purposes.',
250.00, 2, 'In Stock', 0, 1),

('Cow Dung Sambrani Cups (Pack of 20)', 'cow-dung-sambrani-cups',
'Fragrant sambrani cups made from cow dung and natural resins. Light them for a purifying, mosquito-repellent, and spiritually uplifting atmosphere in your home.',
180.00, 3, 'In Stock', 0, 1);

-- ============================================================
-- TESTIMONIALS
-- ============================================================
INSERT INTO `testimonials` (`name`, `location`, `message`, `rating`, `is_active`, `sort_order`) VALUES
('Ramesh Sharma', 'Mysore, Karnataka',
'I have been donating to Kamadenu Goushala for over two years. The transparency and dedication of the team is remarkable. Seeing the cows healthy and happy gives me immense peace. Truly a place of dharma.',
5, 1, 1),

('Priya Nair', 'Bangalore, Karnataka',
'I adopted a Gir cow named Gauri through the Goushala. The regular updates I receive about her health and well-being make me feel connected even from afar. The work being done here is truly commendable.',
5, 1, 2),

('Dr. Venkatesh Murthy', 'Coorg, Karnataka',
'As a veterinarian, I have visited many goushalas, but Kamadenu Goushala stands out for its genuine care and proper management. The cows are well-fed, healthy, and clearly loved. I wholeheartedly recommend supporting their mission.',
5, 1, 3),

('Sunita Hegde', 'Mangalore, Karnataka',
'The Panchagavya products from Kamadenu Goushala are authentic and of excellent quality. The ghee made from indigenous cow milk has a wonderful aroma and flavour. I am a regular customer and supporter.',
5, 1, 4),

('Anand Kulkarni', 'Hubli, Karnataka',
'Visiting Kamadenu Goushala was a life-changing experience for my family. My children learned so much about indigenous cow breeds and the importance of Gau Seva. We now contribute monthly to their feeding program.',
5, 1, 5);
