# ROUTES FOR PROJECT E_COMMERCE - API

## *Registration*

### LOGIN

**Route: "/login"**<br>
**Parameters**:<br>
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




## Article


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



### Get article by id
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



### Add article
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
 * Body: `{ /* data of new Article (like get) */ }`



### Update article

`POST` `/article/{id}`

##### Headers

Same as 'Add'

##### Body

Same as 'Add' + `_method: 'PUT' | 'Patch`

#### Returns

 * HTTP Status: `200`
 * Body: same as 'Add'


### Delete article

`DELETE` `/article/{id}'

Headers must contain token.

Empty response
