# Registration routes

## LOGIN

**Route: "/login"**<br>
**Method: POST**<br>
**Parmeters**:<br>
type: JSON:<br>
```json
{
    "email": "myadress@mail.com",
    "password": "userPlainPassword"
}
```

**Returns**:<br>
A JSON containing the user data, his role and the generated token to store in local storage.
You'll have to send the token as request header on every request.

ex:
```json
{
    "email": "useremail@mail.com",
    "role": ["ROLE_USER"],
    "token": "YTozOn[...]29tIjt9"
}
```

<!-- To find informations about user, you have to use the getters:<br>
```javascript
$user.getId(); //returns the user's id
$user.getEmail(); //returns the users's email
$user.getRoles(); //returns an array of user's roles
``` -->

## REGISTER

**Route: "/register"**<br>
**Method: POST**<br>
**Parmeters**:<br>
type: JSON:<br>
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
