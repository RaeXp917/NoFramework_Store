-- Create the database
CREATE DATABASE IF NOT EXISTS student_eshop;
USE student_eshop;

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
);

-- Insert example product
INSERT INTO products (name, price, description, image_url)
VALUES ('Notebook', 3.50, 'A5 notebook for class notes.', 'images/notebook.jpg');
