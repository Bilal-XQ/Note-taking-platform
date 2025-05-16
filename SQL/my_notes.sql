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