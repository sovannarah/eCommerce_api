# *Categories*

## Get all Categories

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

## Get specific Category and it's Articles

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
