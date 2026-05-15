-- =====================================================
-- База данных: qazaq_stay
-- Описание: База данных для сайта бронирования отелей
-- Импорт: phpMyAdmin → Import → выбрать этот файл
-- =====================================================

CREATE DATABASE IF NOT EXISTS qazaq_stay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qazaq_stay;

-- =====================================================
-- Таблица пользователей
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Таблица отелей
-- =====================================================
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    description TEXT,
    image_url VARCHAR(500),
    price_per_night DECIMAL(10, 2) NOT NULL,
    rating DECIMAL(2, 1) DEFAULT 0.0,
    reviews_count INT DEFAULT 0,
    amenities VARCHAR(500),
    rooms_available INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Таблица бронирований
-- =====================================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT DEFAULT 1,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Администратор по умолчанию
-- Логин: admin@qazaqstay.kz | Пароль: admin123
-- =====================================================
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@qazaqstay.kz', '$2y$10$YourHashWillBeHereAfterRegistration', 'admin');

-- =====================================================
-- Демо-отели по всем городам Казахстана
-- =====================================================
INSERT INTO hotels (name, city, address, description, image_url, price_per_night, rating, reviews_count, amenities) VALUES

-- АСТАНА
('Ritz-Carlton Astana', 'Астана', 'ул. Достык, 16', 'Роскошный 5-звёздочный отель в самом сердце столицы с видом на Байтерек. Спа, бассейн, мишленовские рестораны.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800', 55000, 4.9, 1247, 'Wi-Fi,Бассейн,Спа,Ресторан,Парковка,Фитнес'),
('Hilton Astana', 'Астана', 'пр. Кабанбай батыра, 48', 'Современный отель премиум-класса возле Экспо. Панорамные виды и первоклассный сервис.', 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800', 42000, 4.8, 982, 'Wi-Fi,Бассейн,Спа,Ресторан,Парковка'),
('Marriott Astana', 'Астана', 'пр. Туран, 6', 'Бизнес-отель мирового уровня в деловом центре города.', 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800', 38000, 4.7, 856, 'Wi-Fi,Бассейн,Ресторан,Парковка,Фитнес'),
('Astana Park Hotel', 'Астана', 'ул. Сарайшык, 2', 'Уютный отель с видом на парк президента и реку Есиль.', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800', 22000, 4.5, 543, 'Wi-Fi,Ресторан,Парковка'),

-- АЛМАТЫ
('InterContinental Almaty', 'Алматы', 'ул. Желтоксан, 181', 'Легендарный отель в центре Алматы с видом на горы Заилийского Алатау.', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800', 48000, 4.9, 1567, 'Wi-Fi,Бассейн,Спа,Ресторан,Парковка,Фитнес'),
('Rixos Almaty', 'Алматы', 'ул. Сейфуллина, 506/99', 'Премиум-отель турецкой сети с турецкой баней и роскошным спа-центром.', 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=800', 45000, 4.8, 1234, 'Wi-Fi,Бассейн,Спа,Ресторан,Хамам'),
('Kazzhol Almaty', 'Алматы', 'ул. Гоголя, 127/1', 'Комфортный отель в исторической части города. Близко к Зеленому базару.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800', 18000, 4.4, 678, 'Wi-Fi,Ресторан,Парковка'),
('Worldhotel Saltanat', 'Алматы', 'мкр. Самал-2, 77', 'Современный отель в престижном районе Алматы с видом на горы.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800', 25000, 4.6, 432, 'Wi-Fi,Бассейн,Ресторан'),

-- ШЫМКЕНТ
('Rixos Khadisha Shymkent', 'Шымкент', 'ул. Тауке хана, 4', 'Лучший отель Шымкента с восточным колоритом и современным комфортом.', 'https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=800', 32000, 4.7, 654, 'Wi-Fi,Бассейн,Спа,Ресторан,Хамам'),
('Shymkent Plaza Hotel', 'Шымкент', 'пр. Республики, 25', 'Современный бизнес-отель в деловом центре города.', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800', 20000, 4.5, 387, 'Wi-Fi,Ресторан,Парковка'),
('Sapar Hotel', 'Шымкент', 'ул. Байтурсынова, 18', 'Уютный отель в традиционном казахском стиле.', 'https://images.unsplash.com/photo-1559599189-fe84dea4eb79?w=800', 14000, 4.3, 234, 'Wi-Fi,Ресторан'),

-- КАРАГАНДА
('Cosmonaut Hotel', 'Караганда', 'пр. Бухар Жырау, 49', 'Знаменитый отель с историей в самом центре Караганды.', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800', 18000, 4.4, 432, 'Wi-Fi,Ресторан,Парковка'),
('Park Hotel Karaganda', 'Караганда', 'ул. Ерубаева, 39', 'Современный отель европейского уровня.', 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800', 22000, 4.6, 298, 'Wi-Fi,Бассейн,Ресторан'),

-- АТЫРАУ
('Renaissance Atyrau', 'Атырау', 'ул. Сатпаева, 15Б', 'Современный международный отель в нефтяной столице Казахстана.', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800', 35000, 4.7, 521, 'Wi-Fi,Бассейн,Спа,Ресторан,Парковка'),
('River Palace Atyrau', 'Атырау', 'пр. Азаттык, 55', 'Роскошный отель на берегу реки Урал.', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800', 28000, 4.5, 367, 'Wi-Fi,Ресторан,Парковка'),

-- АКТАУ
('Caspian Riviera Grand Palace', 'Актау', 'ул. 9 микрорайон, 49', 'Лучший отель на побережье Каспийского моря с собственным пляжем.', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800', 38000, 4.8, 687, 'Wi-Fi,Бассейн,Спа,Пляж,Ресторан'),
('Aktau Hotel', 'Актау', '4 микрорайон, 9', 'Комфортный отель с видом на Каспийское море.', 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=800', 20000, 4.4, 234, 'Wi-Fi,Ресторан,Парковка'),

-- ТУРКЕСТАН
('Turkistan Hotel', 'Туркестан', 'пр. Тауке хана, 30', 'Современный отель в духовной столице тюркского мира.', 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=800', 22000, 4.6, 412, 'Wi-Fi,Бассейн,Ресторан'),
('Karavansaray Turkistan', 'Туркестан', 'ул. Туркестанская, 1', 'Этно-отель в традиционном восточном стиле возле мавзолея Ходжи Ахмеда Ясави.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800', 25000, 4.7, 356, 'Wi-Fi,Спа,Ресторан,Хамам'),

-- КОСТАНАЙ
('Tobol Hotel', 'Костанай', 'ул. Аль-Фараби, 119', 'Лучший отель Костаная с современным дизайном.', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800', 16000, 4.4, 198, 'Wi-Fi,Ресторан,Парковка'),
('Medeo Hotel Kostanay', 'Костанай', 'ул. Победы, 79', 'Уютный городской отель в центре.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800', 13000, 4.2, 145, 'Wi-Fi,Ресторан'),

-- ПАВЛОДАР
('Pavlodar Hotel', 'Павлодар', 'ул. Академика Сатпаева, 79', 'Современный отель в центре Павлодара на берегу Иртыша.', 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800', 17000, 4.5, 287, 'Wi-Fi,Бассейн,Ресторан'),
('Irtysh Hotel', 'Павлодар', 'ул. Лермонтова, 95', 'Классический отель с видом на реку.', 'https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=800', 14000, 4.3, 156, 'Wi-Fi,Ресторан,Парковка'),

-- СЕМЕЙ
('Semey Hotel', 'Семей', 'ул. Кабанбай батыра, 11', 'Исторический отель в центре города Абая.', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800', 13000, 4.3, 167, 'Wi-Fi,Ресторан,Парковка'),
('Binar Hotel', 'Семей', 'ул. Найманбаева, 159', 'Современный отель с панорамным видом.', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800', 15000, 4.4, 198, 'Wi-Fi,Ресторан'),

-- УРАЛЬСК
('Pushkin Hotel', 'Уральск', 'пр. Достык, 173', 'Лучший отель Уральска с богатой историей.', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800', 18000, 4.5, 234, 'Wi-Fi,Ресторан,Парковка'),
('Chagala Uralsk', 'Уральск', 'ул. Сарайшык, 19', 'Современный международный отель.', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800', 22000, 4.6, 298, 'Wi-Fi,Бассейн,Спа,Ресторан'),

-- ТАРАЗ
('Taraz Hotel', 'Тараз', 'ул. Толе би, 73', 'Современный отель в древнем городе.', 'https://images.unsplash.com/photo-1559599189-fe84dea4eb79?w=800', 14000, 4.4, 187, 'Wi-Fi,Ресторан,Парковка'),
('Jambyl Hotel', 'Тараз', 'пр. Жамбыла, 51', 'Уютный городской отель с восточным колоритом.', 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800', 12000, 4.2, 134, 'Wi-Fi,Ресторан'),

-- КОКШЕТАУ
('Kokshetau Hotel', 'Кокшетау', 'ул. Ауельбекова, 144', 'Лучший отель в воротах Бурабая.', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800', 16000, 4.5, 245, 'Wi-Fi,Бассейн,Ресторан'),
('Burabay Resort', 'Кокшетау', 'ул. Кенесары, 22', 'Курортный отель рядом с национальным парком.', 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=800', 24000, 4.7, 378, 'Wi-Fi,Бассейн,Спа,Ресторан'),

-- УСТЬ-КАМЕНОГОРСК
('Ust-Kamenogorsk Hotel', 'Усть-Каменогорск', 'пр. Независимости, 19', 'Современный отель в центре города.', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800', 17000, 4.4, 212, 'Wi-Fi,Ресторан,Парковка'),
('Altai Palace', 'Усть-Каменогорск', 'ул. Кабанбай батыра, 158', 'Премиум-отель с видом на Алтайские горы.', 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800', 26000, 4.7, 312, 'Wi-Fi,Бассейн,Спа,Ресторан');
