CREATE TYPE location AS (
	street TEXT,
	houseNumber INT
);

CREATE TABLE persons (
	id SERIAL PRIMARY KEY,
	location location,
	content TEXT
);
