# POST NEW TRANSPORTEUR
**Route: "/transporteur"** <br>
**Method: POST**<br>
**STRUCTURE REQUEST:**<br>
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
}
