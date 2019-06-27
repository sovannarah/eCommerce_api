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

## Articles

see [article.md](article.md)

## *Categories*

### GET ALL CATEGORIES

**Route: "/category"**<br>
**Method: GET**<br>

**Return**:<br>
Returns a json containing all the main categories and their childrens.

ex:
```json
[
        {   "id": 1,
            "name": "Ecran",
            "sub": []
        },
        {   "id":  2,
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
        {   "id": 3,
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
```json

```
