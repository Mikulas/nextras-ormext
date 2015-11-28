CREATE TYPE location AS (
	"street" TEXT,
	"houseNumber" INT
);

CREATE TABLE persons (
	"id" SERIAL PRIMARY KEY,
	"isAdmin" BOOL DEFAULT FALSE NOT NULL,
	"location" location,
	"content" TEXT,
	"creditCardNumber_encrypted" TEXT,
	"favoriteNumbers" INT[] CONSTRAINT at_least_one_number CHECK (array_length("favoriteNumbers", 1) > 0),
	"largestFavoriteNumber" INT
);


CREATE FUNCTION compute_largestFavoriteNumber() RETURNS trigger AS $$
BEGIN
	NEW."largestFavoriteNumber" := (
		SELECT max(numbers) FROM unnest(NEW."favoriteNumbers") AS numbers
	);
	RETURN NEW;
END; $$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_compute_largestFavoriteNumber
BEFORE INSERT OR UPDATE OF "favoriteNumbers" ON persons
FOR EACH ROW EXECUTE PROCEDURE compute_largestFavoriteNumber();
