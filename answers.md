### PHP Task 1 - Answers in plain English
The APIs are available at http://localhost:8000/api/orders and provide a simple way to filter data through the use of query params. They also provide pagination.

1. Count the total number of orders? <br>
Answer: ```GET http://localhost:8000/api/orders``` provides a paginated list of orders. <br>
The response provides a pagination object which specifies the total number of orders that match the request, considering filters where applicable. <br>
```
"pagination": {
        "total": 380,
        "per_page": 10,
        "current_page": 1,
        "last_page": 38
    },
```

2. Count the number of orders that were FREE? <br>
The API allows you to filter min and max prices. You can utilise this to retrieve the orders that were free.
Answer: ```GET http://localhost:8000/api/orders?price_min=0&price_max=0``` <br>
You can view the number of free orders returned by this via the total mentioned at the bottom of the data via the pagination obj: <br>
```
{
    "orders": [
        {
            "uuid": "9fece653-3b2a-431d-8cdb-99a963576ede",
            "customer_id": "67b49e163255b78bbb0fe9d9",
            "customer_snapshot": {
                "first_name": "Angelia",
                "last_name": "Grant",
                "email": "angelia.grant@example.com",
                "billing_address": {
                    "postcode": "MO7 8PR",
                    "county": "Glamorgan",
                    "city": "Kiskimere",
                    "street": "73 Highland Place"
                },
                "shipping_address": {
                    "postcode": "MO7 8PR",
                    "county": "Glamorgan",
                    "city": "Kiskimere",
                    "street": "73 Highland Place"
                }
            },
            "product_id": "67b49e163255b78bbb0fe9d8",
            "product_snapshot": {
                "title": "sunt Lorem esse ex laborum",
                "price": 0,
                "currency": "EUR"
            },
            "created_at": "2016-08-13T04:39:11.045000Z",
            "updated_at": "2025-02-18T14:50:16.659000Z",
            "id": "67b49e289d00a26dc20ee3fa"
        },
        ...
    ]
    "pagination": {
>>> "total": 4,
    "per_page": 10,
    "current_page": 1,
    "last_page": 1
},
"total_price": 0
}
```

3. Count the number of orders that were placed in GBP? <br>
Answer: ```http://localhost:8000/api/orders?currency=gbp``` You can utilise the ```currency``` filter to filter for a specific currency like GBP or USD, etc.
The response will contain the "total" number of orders matching, as shown abbove.

4. Count the number of orders that were shipped to Essex? <br>
Answer: ```http://localhost:8000/api/orders?shipping_county=essex``` You can utilise the ```shipping_county``` filter to filter for orders shipped to a specific county. The response will contain the "total" number of orders matching, as shown above.

5. Sum the cost of orders that were placed in GBP and were £100 or more? <br>
Answer: ```http://localhost:8000/api/orders?currency=gbp&price_min=100``` You can utilise a combination of the ```currency``` and ```price_min``` filters to retrieve orders that were >= £100 and placed in GBP. The response will contain the "total" number of orders, as above.

6. Sum the cost of orders that were placed in GBP? <br>
Answer: ```http://localhost:8000/api/orders?currency=gbp``` Using the ```currency``` filter, you can retrieve all orders placed in that currency and look at the "total_price" value present in the response.

7. Sum the cost of orders that were placed in GBP and were shipped to Essex? <br>
Answer: ```http://localhost:8000/api/orders?shipping_county=essex&currency=gbp``` Using the ```shipping_county``` and ```currency``` filters, you can retrieve all orders that were placed in GBP and shipped to Essex. The "total_price" will show the sum of all the orders returned.