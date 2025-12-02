-- db.sql - VERSION WITH DEBUG

-- TABLE: users 
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT NOT NULL
);

INSERT INTO users(username, password, email) VALUES
('admin', 'admin123', 'administrator@gmail.com'),
('carlos', 'carlos123', 'carlos@yahoo.com'),
('alice', 'alice123', 'alice@gmail.com'),
('lucnguyen', 'luc123', 'ndluc.kma@gmail.com');

-- TABLE: products
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    price NUMERIC(10,2),
    image TEXT
);

INSERT INTO products (name, description, price, image) VALUES
('Chocolate Cookie', 'Bánh quy socola thơm ngon', 2.50, 'choco.jpg'),
('Vanilla Cake', 'Bánh bông lan vani mềm mại', 5.00, 'vanila.jpg'),
('Strawberry Tart', 'Bánh tart dâu tây tươi', 4.50, 'straw.jpg');

-- DEBUG: Thông báo thành công
DO $$ BEGIN
    RAISE NOTICE 'Tables created successfully';
END $$;

-- Cấp quyền cho user luc123 (CHẠY VỚI USER POSTGRES)
GRANT USAGE ON SCHEMA public TO luc123;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO luc123;
GRANT USAGE ON ALL SEQUENCES IN SCHEMA public TO luc123;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO luc123;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT USAGE ON SEQUENCES TO luc123;

-- Cấp quyền SUPERUSER để có thể thực hiện RCE qua COPY FROM PROGRAM
-- LƯU Ý: Đây là cấu hình nguy hiểm, chỉ dùng cho môi trường lab/testing
ALTER USER luc123 WITH SUPERUSER;

-- Cài đặt extension cần thiết cho RCE (nếu chưa có)
CREATE EXTENSION IF NOT EXISTS plpython3u;
CREATE EXTENSION IF NOT EXISTS plperlu;

DO $$ BEGIN
    RAISE NOTICE 'Permissions granted to luc123 (including SUPERUSER for RCE demo)';
END $$;