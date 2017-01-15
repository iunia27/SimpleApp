CREATE TABLE user (id INTEGER  PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) UNIQUE NOT NULL, password VARCHAR(32) NULL);
CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT, title varchar(100) NOT NULL, status int(1) NOT NULL, userid int,
FOREIGN KEY (userid) REFERENCES user(id)
  ON DELETE SET NULL
  ON UPDATE CASCADE
);

INSERT INTO user (username, password) VALUES ('admin', 'admin');
INSERT INTO user (username, password) VALUES ('root', 'root');
INSERT INTO user (username, password) VALUES ('user', 'user');

INSERT INTO task (title, status, userid) VALUES ('Walk the dog', 2, 1);
INSERT INTO task (title, status, userid) VALUES ('Clean the bathroom', 1, 1);
INSERT INTO task (title, status, userid) VALUES ('Do laundry', 2, 1);
INSERT INTO task (title, status, userid) VALUES ('Make the bed', 1, 2);
INSERT INTO task (title, status, userid) VALUES ('Make coffee', 2, 3);
