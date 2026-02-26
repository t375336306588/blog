USE mysql;
CREATE USER IF NOT EXISTS 'xxxxxxxxx'@'%' IDENTIFIED BY 'xxxxxxxxx';
GRANT ALL PRIVILEGES ON blog.* TO 'xxxxxxxxx'@'%';
FLUSH PRIVILEGES;