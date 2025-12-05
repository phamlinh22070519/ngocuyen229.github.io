-- Tạo mới database LoginReg
CREATE DATABASE LoginReg;

-- Sử dụng database này
USE LoginReg;

-- Tạo bảng userReg để lưu thông tin người dùng
CREATE TABLE userReg (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  student_id VARCHAR(50),
  class_name VARCHAR(50),
  country VARCHAR(50)
);
