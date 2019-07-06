
# Categories routes
## Get all Categories
`GET` `/category`
#### Return `json`
```json
[
    {
        "id": 1,
        "name": "Ecran",
        "sub": []
    },
    {
        "id": 2,
        "name": "Peripherique",
        "sub": [
            {
                "id": 7,
                "name": "Clavier"
                "sub": [
                    {
                        "id": 9,
                        "name": "Retroeclaire",
                        "sub": []
                    }
                ]
            },
            {
                "id": 8,
                "name": "Souris"
                "sub": []
            }
        ]
    },
    {
        "id": 3,
        "name": "Ordinateurs",
        "sub": [
            {
                "id": 6,
                "name": "Tour"
                "sub": []
            },
        {/* ... */}
]
```

  

## Get specific Category and it's Articles
`GET` `/category/{id}`
#### Params
`...`
#### Return
`...`
