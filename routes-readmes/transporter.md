# POST NEW TRANSPORTEUR
`POST` `/transport`
#### STRUCTURE REQUEST
```
{
    "name": "...",
    "offer":
    [
        {
            "name": "...",
            "spec":
            [
                {
                    "name": "...",
                    "unity": "...", (Km, Kg, m3)
                    "minValue": "...",
                    "price": "..."
                },
                {
                    "name": "...",
                    "unity": "...", (Km, Kg, m3)
                    "minValue": "...",
                    "price": "..."
                },
                ...
            ]
        },
        {
            "name": "...",
            "spec":
            [
                {
                "name": "...",
                "unity": "...", (Km, Kg, m3)
                "minValue": "...",
                "price": "..."
                },
                ...
            ]
        },
        ...
    ]
}
```
Exemple:

```
{
    "name": "chronopasta",
    "offer":
    [
        {
            "name": "stand d'art",
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
            "name": "ekspresse",
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
}
