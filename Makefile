build:
	box build

install:
	cp dist/drupdates.phar /usr/local/bin/drupdates

clean:
	rm dist/drupdates.phar
