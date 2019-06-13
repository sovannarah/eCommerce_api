# ROUTES FOR PROJECT E_COMMERCE - API

## *Registration*

### LOGIN

**Route: "/login"**<br>
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
