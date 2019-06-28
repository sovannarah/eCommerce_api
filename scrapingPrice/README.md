Dependencies:
	python: version: 3.7
	pip for python3.7
	pip install: Flask bs4 requests geopy

Run Server:
	FLASK_APP=scrapingPrice.py flask run

### Route analyse methods="GET"
analyse the price of article from amazone fnac ldlc
url = /?article=(name of the article)&price=(price of the article)
return an array of other web site

### Route distance methods="GET"
calculate the distance
url = /?ad1=(first address)&ad2=(second address)
return distance in Km
