CREATE TYPE location AS (
	"street" TEXT,
	"houseNumber" INT
);

CREATE TABLE persons (
	"id" SERIAL PRIMARY KEY,
	"isAdmin" BOOL DEFAULT FALSE NOT NULL,
	"location" location,
	"content" TEXT,
	"creditCardNumber_encrypted" TEXT
);
