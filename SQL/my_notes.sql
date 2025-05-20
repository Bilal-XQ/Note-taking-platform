create table admins
(
    id       int auto_increment
        primary key,
    username varchar(50)  not null,
    password varchar(255) not null,
    constraint username
        unique (username)
);

create table modules
(
    id         int auto_increment
        primary key,
    name       varchar(100)                        not null,
    created_at timestamp default CURRENT_TIMESTAMP null,
    updated_at timestamp default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP,
    constraint name
        unique (name)
);

create table notes
(
    id         int auto_increment
        primary key,
    content    text                                not null,
    student_id int                                 not null,
    module_id  int                                 not null,
    created_at timestamp default CURRENT_TIMESTAMP null,
    updated_at timestamp default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP
);

create index idx_notes_module
    on notes (module_id);

create index idx_notes_student
    on notes (student_id);

create table student_modules
(
    student_id int not null,
    module_id  int not null,
    primary key (student_id, module_id)
);

create index module_id
    on student_modules (module_id);

create table students
(
    id        int auto_increment
        primary key,
    full_name varchar(100) not null,
    username  varchar(50)  not null,
    password  varchar(255) not null,
    admin_id  int          null,
    constraint username
        unique (username)
);

create index admin_id
    on students (admin_id);

ALTER TABLE students
    ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL,
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- AI-Integration Database Updates

-- 1. Add a column to notes table for AI-generated summaries
ALTER TABLE notes
    ADD COLUMN ai_summary TEXT NULL AFTER content,
    ADD COLUMN summary_generated_at TIMESTAMP NULL DEFAULT NULL;

-- 2. Create table for auto-generated quizzes
CREATE TABLE quizzes (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         note_id INT NOT NULL,
                         title VARCHAR(255) NOT NULL,
                         description TEXT NULL,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         CONSTRAINT fk_quiz_note FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);

-- 3. Create table for quiz questions
CREATE TABLE quiz_questions (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                quiz_id INT NOT NULL,
                                question_text TEXT NOT NULL,
                                question_type ENUM('multiple_choice', 'true_false', 'short_answer') NOT NULL DEFAULT 'multiple_choice',
                                difficulty ENUM('easy', 'medium', 'hard') NOT NULL DEFAULT 'medium',
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- 4. Create table for quiz answers
CREATE TABLE quiz_answers (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              question_id INT NOT NULL,
                              answer_text TEXT NOT NULL,
                              is_correct BOOLEAN NOT NULL DEFAULT FALSE,
                              explanation TEXT NULL,
                              CONSTRAINT fk_answer_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

-- 5. Create table for student quiz attempts
CREATE TABLE quiz_attempts (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               student_id INT NOT NULL,
                               quiz_id INT NOT NULL,
                               score DECIMAL(5,2) NOT NULL DEFAULT 0,
                               total_questions INT NOT NULL DEFAULT 0,
                               completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               CONSTRAINT fk_attempt_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
                               CONSTRAINT fk_attempt_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- 6. Add categories/topics table for better organization
CREATE TABLE categories (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(100) NOT NULL,
                            description TEXT NULL,
                            module_id INT NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            CONSTRAINT fk_category_module FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
                            CONSTRAINT unique_category_name_per_module UNIQUE (name, module_id)
);

-- 7. Add category_id to notes table
ALTER TABLE notes
    ADD COLUMN category_id INT NULL AFTER module_id,
    ADD CONSTRAINT fk_note_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- 8. Add title column to notes table (currently missing and needed for better organization)
ALTER TABLE notes
    ADD COLUMN title VARCHAR(255) NOT NULL AFTER id;

-- 9. Create index for faster queries
CREATE INDEX idx_notes_category ON notes(category_id);