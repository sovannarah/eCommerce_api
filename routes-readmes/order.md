# Orders

There are 2 types of order: 

 * UserOrder : `/order` Any User can create, and can only read own orders, readall is of the user
 * StockOrder : `/stock/order` Only admin can read and create, with no ownership

Other than that, the usage is identical.

All subroutes require token in header 

Read and ReadAll are standard `GET [route]` and `GET [route]/{id}`

Example JSON representation of an order:

```json
{
    "id": 1,
    "userId": 5,
    "send": {
        "date": "2019-06-27 12:46:55.000000",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "receive": {
        "date": "2019-06-27 16:00:00.000000",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "items": [
        {
            "id": 1,
            "quantity": 1,
            "article": {
                "id": 1,
                "title": "Brody Lesch",
                "description": "Laborum doloribus corrupti...",
                "price": 1347296984,
                "nb_views": 1397579985,
                "stock": 505909706,
                "images": [
                    "3b59f4c4-8911-3cab-8e0c-88277b893083.jpg",
                    "85a3d4d3-6b67-3fff-bbed-76c93ca93da5.jpg"
                ]
            }
        },
        ...
    ]
}
```

## Create

`POST [route]`

### Body

Array of Object containing:
 * `id` of the article
 * `quantity` positive int
 
Example:

```json
[
  {"id": 1, "quantity": 5},
  {"id": 3, "quantity": 1},
  ...
]
```

### Return

Status `201` with newly created order object
