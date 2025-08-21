drop table if exists post_blobs;
drop table if exists blobs;
drop table if exists likes;
drop table if exists comments;
drop table if exists posts;
drop table if exists users;

create table users (
	userId char(32) primary key not null,
	password char(42),
	userName char(32),
	description char(255),
	profilePic char(255),
	created datetime DEFAULT CURRENT_TIMESTAMP
);

create table posts (
	postId int primary key not null auto_increment,
	text text,
	posted datetime DEFAULT CURRENT_TIMESTAMP,
	userId char(32),
	foreign key (userId) references users(userId)
);

create table comments (
	commentId int primary key not null auto_increment,
	text text,
	posted datetime DEFAULT CURRENT_TIMESTAMP,
	postId int,
	userId char(32),
	foreign key (postId) references posts(postId),
	foreign key (userId) references users(userId)
);

create table likes (
	postId int,
	commentId int null,
	userId char(32),
	foreign key (postId) references posts(postId),
	foreign key (commentId) references comments(commentId),
	foreign key (userId) references users(userId)
);

CREATE TABLE blobs (
    blobId char(32) PRIMARY KEY
);

CREATE TABLE post_blobs (
	postId INT,
    blobId char(32),
    PRIMARY KEY (postId, blobId),
    FOREIGN KEY (postId) REFERENCES posts(postId) ON DELETE CASCADE,
    FOREIGN KEY (blobId) REFERENCES blobs(blobId) ON DELETE CASCADE
);