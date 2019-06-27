# ROUTES FOR PROJECT E_COMMERCE - API

## *Registration*

### LOGIN

**Route: "/login"**<br>
**Method: POST**<br>
**Parmeters**:<br>
JSON:<br>
```json
{
	"email": "myadress@mail.com",
		"password": "userPlainPassword"
}
```

**Return**:<br>
A JSON containing the user data, his role and the generated token to store in local storage.
You'll have to send the token as request header on every request.

ex:
```json
{
	"user": User::class,
		"role": ["ROLE_ADMIN"],
		"token": "YTozOn[...]29tIjt9"
}
```

To find informations about user, you have to use the getters:<br>
```javascript
$user.getId(); //returns the user's id
$user.getEmail(); //returns the users's email
$user.getRoles(); //returns an array of user's roles
```

### REGISTER

**Route: "/register"**<br>
**Method: POST**<br>
**Parmeters**:<br>
JSON:<br>
```json
{
	"email": "myadress@mail.com",
		"password": "userPlainPassword"
}
```

**Return**:<br>
A JSON containing the user email, and his new id.

ex:
```json
{
	"email": "myadress@mail.com",
		"user_id": 2,
}
```

## *Articles*


### Get All

`GET` `/article`

##### Returns
````
[
{
	//article json ...
},
	...
]
````



### Get Article by id
`GET` `/article/{id}`

##### Returns
````
{
id: int,
	title: string,
	description: string,
	price: int
		images: [string], //of file names
	nb_views: int,
	stock: int|null
		category: {
id: 	int,
		name: 	string,
		parent: null | {
id: 	 int,
		 name: 	 string,
		 parent : null | {/*recursive*/}
		}
		}
}
````

**! ATTENTION** To access image, use its name under '/uploads/images'. For example:

````
const fileName = fetchedArticle.images[3]; // filename == 'nn234nkl43.jpg'
img.src = apiUrl + '/uploads/images' + fileName // '10.43.12.3:8000/uploads/images/nn234nkl43.jpg'
````


### Increment Article views
`PUT` | `PATCH` `/article/{id}/increment`

Increments the nb_views on an Article 

#### Returns
Updated Article JSON (like in read `GET`)


### Add Article
	`POST` `/article`

#### Sent Data
##### Headers
	* `Content-Type`: `multipart/form-data`,
	* `token`: admin token

##### Body
	* `title`: string
	* `description`: string
	* `price`: int
	* `images`: image file array
	* `nb_views`: int
	* `stock`: int //optional
	* `category`: int //id

#### Returns
	* HTTP Status: `201`
	* Body: `{ /* data of new Article (like GET) */ }`



### Update Article

	`POST` `/article/{id}`

##### Headers, Body

	Same as 'Add'

#### Returns

	* HTTP Status: `200`
	* Body: same as 'Add'



### Delete Article

	`DELETE` `/article/{id}`

	Headers must contain token.

	Empty response


## *Categories*

### GET ALL CATEGORIES

	**Route: "/category"**<br>
	**Method: GET**<br>

	**Return**:<br>
	Returns a json containing all the main categories and their childrens.

	ex:
	```json
	[
{
	"1": {
		"name": "Ecran",
			"sub": []
	},
		"2": {
			"name": "Peripherique",
			"sub": [
				[
				{
					"id": 7,
					"name": "Clavier"
				}
				],
				[
				{
					"id": 8,
					"name": "Souris"
				}
				]
			]
		},
		"3": {
			"name": "Ordinateurs",
			"sub": [
				[
				{
					"id": 6,
					"name": "Tour"
				}
				],
				```
					...

### GET SPECIFIC CATEGORY AND IT'S ARTICLES

					**Route: "/category/{id}"**<br>
					**Method: GET**<br>

					**Parmeters**:<br>
					URL:<br>
					name | type | description
					id | 
					"id": int: id of category

					**Return**:<br>
					Returns a json containing all the main categories and their childrens.

					ex:
					```JSON
					...

# POST NEW TRANSPORTEUR
**Route: "/transporteur"** <br>
**Method: POST<br>
**STRUCTURE REQUEST:<br>
{<br>
	"name": "...",<br>
	"offer":<br>
	[<br>
		{<br>
			"name": "...",<br>
			"spec":<br>
			[<br>
				{<br>
					"name": "...",<br>
					"unity": "...", (Km, Kg, m3)<br>
					"minValue": "...",<br>
					"price": "..."<br>
				},<br>
				{<br>
					"name": "...",<br>
					"unity": "...", (Km, Kg, m3)<br>
					"minValue": "...",<br>
					"price": "..."<br>
				},... <br>
			]<br>
		},<br>
		{<br>
			"name": "...",<br>
			"spec":<br>
			[<br>
				{<br>
					"name": "...",<br>
					"unity": "...", (Km, Kg, m3)<br>
					"minValue": "...",<br>
					"price": "..."<br>
				},... <br>
			]<br>
		},... <br>
	]<br>
}<br>

				eme": "chronopost",
					"offer":
						[
						{
							"name": "standart",
							"spec":
								[
								{
									"name": "distance",
									"unity": "km",
									"minValue": 50,
									"price": 2
								},
								{
									"name": "height",
									"unity": "cm",
									"minValue" : 17,
									"price": 0.5
								}
								]
						},
						{
							"name": "expresse",
							"spec":
								[
								{
									"name": "distance",
									"unity": "km",
									"minValue": 10,
									"price": 1
								}
								]
						}
				]
		}xample:

	```
