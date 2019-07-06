# User routes
## Get user informations
`GET`  `/user`
#### Params
```
header: {
    "token": "user's token recieved on login"
}
```
#### Returns `json`
```json
{
    "email": "useremail@mail.com",
    "roles": [
        "ROLE_USER"
    ]
}
```
## Update user
`POST`  `/user`
#### Params
```
headers: {
    "token": "user's token recieved on login"
}
body: {
    "email": "newuseremail@mail.fr"
    //add every key you need to update, as soon as it exists in database
}
```
#### Returns `json`
```json

```
## Check user
* Is it a **user**: `GET`  `/user/checkuser`
* Is it an **admin**: `GET`  `/user/isAdmin`
#### Params
```
headers: {
    "token": "user's token recieved on login"
}
```
#### Returns `json`
```json
{
    "email": "useremail@mail.com"
}
```
or Error message with status
# User address routes
## Get user's address
`GET`  `/address`
#### Params
```
header: {
    "token": "user's token recieved on login"
}
```
#### Returns `json`|`null`
```json
{
    "street": "42 rue de la biere",
    "pc": 60066
}
```
## Create / Update user's address
`POST`  `/address`
If the user already have an address, it replace the old one by the new one.
Else it creates it.
#### Params
```
header: {
    "token": "user's token recieved on login"
}
body: {
    "street": "66 rue du Metal"
    "pc": 24373
}
```
#### Returns `json`|`null`
```json
{
    "street": "66 rue du Metal",
    "pc": 24373
}
```
