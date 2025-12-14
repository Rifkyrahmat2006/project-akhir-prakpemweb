-- Add chest position fields to rooms table
ALTER TABLE rooms 
ADD COLUMN IF NOT EXISTS chest_position_top VARCHAR(10) DEFAULT '70%',
ADD COLUMN IF NOT EXISTS chest_position_left VARCHAR(10) DEFAULT '85%';

-- Add is_correct field to user_quizzes table for tracking quiz scores
ALTER TABLE user_quizzes 
ADD COLUMN IF NOT EXISTS is_correct TINYINT(1) DEFAULT 0;
