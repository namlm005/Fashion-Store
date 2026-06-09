CREATE DATABASE IF NOT EXISTS fashion_store;
USE fashion_store;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  role ENUM('admin','staff','customer') DEFAULT 'customer',
  last_login DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category_id INT NULL,
  original_price DECIMAL(12,2) DEFAULT 0,
  price DECIMAL(12,2) NOT NULL,
  quantity INT DEFAULT 0,
  image_url TEXT,
  description TEXT,
  brand VARCHAR(255),
  manufacture_date DATE NULL,
  rating DECIMAL(2,1) DEFAULT 5.0,
  status ENUM('active','hidden') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cart_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  fullname VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address TEXT NOT NULL,
  total_price DECIMAL(12,2) NOT NULL,
  payment_method ENUM('cod','bank','visa','vnpay','paypal') DEFAULT 'cod',
  shipping_method ENUM('same_day','standard','scheduled') DEFAULT 'standard',
  delivery_time VARCHAR(255),
  status ENUM('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(255) NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  quantity INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  subject VARCHAR(255),
  message TEXT NOT NULL,
  status ENUM('new','read','replied') DEFAULT 'new',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users(fullname,email,password,phone,role) VALUES
('Quản trị viên','admin@gmail.com','123456','0123456789','admin'),
('Nhân viên bán hàng','staff@gmail.com','123456','0987654321','staff'),
('Khách hàng mẫu','customer@gmail.com','123456','0911222333','customer');

INSERT INTO categories(name,description) VALUES
('Áo','Các loại áo thời trang'),('Quần','Các loại quần thời trang'),('Mũ','Các loại mũ thời trang'),('Phụ kiện','Túi, ví, kính, thắt lưng');

INSERT INTO products(name,category_id,original_price,price,quantity,image_url,description,brand,manufacture_date,rating) VALUES
('Áo thun nam basic',1,250000,199000,50,'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=800','Áo thun nam form rộng, chất cotton mềm mịn.','Fashion Store','2025-05-10',4.8),
('Áo sơ mi trắng',1,420000,350000,35,'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=800','Áo sơ mi trắng thanh lịch, phù hợp đi học, đi làm.','Fashion Store','2025-06-15',4.7),
('Quần jean xanh',2,550000,450000,40,'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=800','Quần jean xanh trẻ trung, dễ phối đồ.','Denim House','2025-04-20',4.6),
('Quần short kaki',2,300000,249000,60,'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?q=80&w=800','Quần short kaki thoải mái cho mùa hè.','Summer Wear','2025-03-12',4.5),
('Mũ lưỡi trai đen',3,180000,129000,80,'https://images.unsplash.com/photo-1521369909029-2afed882baee?q=80&w=800','Mũ lưỡi trai màu đen cá tính.','Cap Style','2025-02-18',4.9),
('Túi đeo chéo',4,390000,299000,25,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRt5067vwKERJkgF7xLesFfIlJn4leNLm3Ovw&s','Túi đeo chéo nhỏ gọn, phù hợp đi chơi.','Urban Bag','2025-01-20',4.7);
