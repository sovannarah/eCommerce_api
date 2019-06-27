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

See [article.md](article.md)

## Categories

See [category.md](category.md) 

## Order (User, Admin)

See [order.md](order.md) 


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

