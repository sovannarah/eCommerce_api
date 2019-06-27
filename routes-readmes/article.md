# *Articles*


## Get All

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



## Get Article by id

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
	stock: int
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


## Increment Article views

`PUT` | `PATCH` `/article/{id}/increment`

Increments the nb_views on an Article 

#### Returns
Updated Article JSON (like in read `GET`)


## Add Article

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
 * `stock`: int
 * `category`: int (id)
 
#### Returns
 * HTTP Status: `201`
 * Body: `{ /* data of new Article (like GET) */ }`



## Update Article

`POST` `/article/{id}`

#### Sent Data

Same as 'Add'

#### Returns

 * HTTP Status: `200`
 * Body: same as 'Add'



## Delete Article

`DELETE` `/article/{id}`

Headers must contain token.

Empty response
