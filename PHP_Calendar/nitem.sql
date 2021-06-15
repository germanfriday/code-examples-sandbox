use test;

create table TB_SCHEDULE_ITEM
(
item_id int(4) not null auto_increment,
uid int(4) not null,
item varchar(30) not null,
item_time time not null,
item_ampm varchar(4) not null,
item_date date not null,
description text not null,
primary key(item_id),
foreign key(uid) references TB_USERS(uid)
);
