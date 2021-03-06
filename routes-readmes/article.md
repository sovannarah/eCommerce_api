# Articles routes
## Get all articles
`GET`  `/article`
#### Returns `json`

```json
[
    {
        "id": int,
        "title": string,
        "description": string,
        "price": int,
        "nb_views": int,
        "stock": int,
        "images": array,
        "category": {
            "id": int,
            "name": string,
            "parent": null | {/*recursive*/}
        },
        "variants": array
    },
    { /* ... */ }
]
```
## Get article by id
`GET`  `/article/{id}`
#### Returns `json`
```json
{
    "id": int,
    "title": string,
    "description": string,
    "price": int
    "images": [string], //of file names
    "nb_views": int,
    "stock": int
    "category": {
        "id": int,
        "name": string,
        "parent": null | {
            "id": int,
            "name": string,
            "parent" : null | {/*recursive*/}
        }
    },
    "variants": array
 }

```
**! ATTENTION** To access image, use its name under '/uploads/images'. For example:
```
const fileName = fetchedArticle.images[3]; // filename == 'nn234nkl43.jpg'
img.src = apiUrl + '/uploads/images' + fileName // '10.43.12.3:8000/uploads/images/nn234nkl43.jpg'
```
## Increment Article views
`PUT` | `PATCH`  `/article/{id}/increment`
Increments the nb_views on an Article
#### Returns
Updated Article `json` (like in read `GET`)
## Add / Update Article
`POST`
* Add: `/article` 
* Update: `/article/{id}`

  
#### Params
```
headers: {
    "Content-Type": "multipart/form-data"
    "token": "admin token recieved on login"
}
body: {
    "title": string,
    "description": string,
    "price": int,
    "images": image file array,
    "nb_views": int,
    "stock": int,
    "category": int (id)
}
```
#### Returns
* Add: HTTP Status: `201` | Update: HTTP Status: `200`
* Body: `{ /* new Article data (like GET) */ }`
## Delete Article
`DELETE`  `/article/{id}`
#### Params
```
headers: {
    "token": "admin token recieved on login"
}
```
#### Returns
`void`
