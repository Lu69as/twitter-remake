drop table if exists likes;
drop table if exists comments;
drop table if exists posts;
drop table if exists users;

create table users (
	userId char(30) primary key not null,
	password char(40),
	userName char(30),
	description char(255),
	profilePic char(255),
	created datetime DEFAULT CURRENT_TIMESTAMP
);

create table posts (
	postId int primary key not null auto_increment,
	text text,
	posted datetime DEFAULT CURRENT_TIMESTAMP,
	userId char(30),
	foreign key (userId) references users(userId)
);

create table comments (
	commentId int primary key not null auto_increment,
	text text,
	posted datetime DEFAULT CURRENT_TIMESTAMP,
	postId int,
	userId char(30),
	foreign key (postId) references posts(postId),
	foreign key (userId) references users(userId)
);

create table likes (
	postId int,
	commentId int null,
	userId char(30),
	foreign key (postId) references posts(postId),
	foreign key (commentId) references comments(commentId),
	foreign key (userId) references users(userId)
);

insert into users (userId, password, userName, description, profilePic) values 
	( 'lu69as', 'Pass123', 'Lukas Okkenhauger', 'En yngre gutt', 'https://static.wikia.nocookie.net/unanything/images/4/4b/Redditor.webp' ),
	( 'lokas', 'Pass123', 'Ich bin mich', 'En yngre gutt', 'https://static.wikia.nocookie.net/unanything/images/4/4b/Redditor.webp' );

insert into posts (text, userId) values ( 'En helt ny postingmetode', 'lu69as' );

insert into comments (text, postId, userId) values ( 'Kommentarer funker også!', 1, 'lu69as' );

insert into likes (postId, userId) values ( 1, 'lu69as' ), ( 1, 'lokas' );
insert into likes (postId, commentId, userId) values ( 1, 1, 'lu69as' );