use test;

create table TB_USERS
(
uid int(4) not null auto_increment,
user_name varchar(30) not null,
password varchar(30) not null,
first_name varchar(30) not null,
last_name varchar(30) not null,
email varchar(30) not null,
date_of_birth date,
primary key(uid)
);
