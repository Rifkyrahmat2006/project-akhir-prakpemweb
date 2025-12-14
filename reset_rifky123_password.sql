-- Reset rifky123 password to: 123
-- This bcrypt hash is for the password "123"

UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhCa' 
WHERE username = 'rifky123';

-- Now you can login with:
-- Username: rifky123
-- Password: 123
