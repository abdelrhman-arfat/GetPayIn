# GetPayIn

Base URLs: localhost:8000/api

# Authentication

- HTTP Authentication, scheme: bearer

# Auth

## POST Register in app

POST /auth/register

> Body Parameters

```json
{
    "name": "abdo",
    "email": "abdo@gmail.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Params

| Name | Location | Type   | Required | Description |
| ---- | -------- | ------ | -------- | ----------- |
| body | body     | object | no       | none        |

> Response Examples

> 200 Response

```json
{}
```

### Responses

| HTTP Status Code | Meaning                                                 | Description | Data schema |
| ---------------- | ------------------------------------------------------- | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1) | none        | Inline      |

### Responses Data Schema

## POST Login

POST /auth/login

> Body Parameters

```json
{
    "email": "abdo@gmail.com",
    "password": "password"
}
```

### Params

| Name | Location | Type   | Required | Description |
| ---- | -------- | ------ | -------- | ----------- |
| body | body     | object | no       | none        |

> Response Examples

> 200 Response

```json
{
    "message": "Success",
    "status": true,
    "data": {
        "user": {
            "id": 1,
            "name": "abdo",
            "email": "abdo@gmail.com",
            "created_at": "2025-12-01T20:57:27.000000Z",
            "updated_at": "2025-12-01T20:57:27.000000Z"
        },
        "token": "2|gI9EWXVBK4CK54FgkTjEEtnrZgwtW0BM9aoURUML2e387301"
    },
    "errors": null
}
```

### Responses

| HTTP Status Code | Meaning                                                 | Description | Data schema |
| ---------------- | ------------------------------------------------------- | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1) | none        | Inline      |

### Responses Data Schema

HTTP Status Code **200**

| Name           | Type    | Required | Restrictions | Title | description |
| -------------- | ------- | -------- | ------------ | ----- | ----------- |
| » message      | string  | true     | none         |       | none        |
| » status       | boolean | true     | none         |       | none        |
| » data         | object  | true     | none         |       | none        |
| »» user        | object  | true     | none         |       | none        |
| »»» id         | integer | true     | none         |       | none        |
| »»» name       | string  | true     | none         |       | none        |
| »»» email      | string  | true     | none         |       | none        |
| »»» created_at | string  | true     | none         |       | none        |
| »»» updated_at | string  | true     | none         |       | none        |
| »» token       | string  | true     | none         |       | none        |
| » errors       | null    | true     | none         |       | none        |

# Products

## GET Get product data

GET /products/1

> Response Examples

> 200 Response

```json
{
    "message": "Success",
    "status": true,
    "data": {
        "id": 1,
        "name": "Nia Schmeler MD",
        "price": 26.03,
        "stock": 8,
        "created_at": "2025-11-29T15:04:37.000000Z",
        "updated_at": "2025-11-29T15:04:37.000000Z"
    },
    "errors": null
}
```

### Responses

| HTTP Status Code | Meaning                                                 | Description | Data schema |
| ---------------- | ------------------------------------------------------- | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1) | none        | Inline      |

### Responses Data Schema

HTTP Status Code **200**

| Name          | Type    | Required | Restrictions | Title | description |
| ------------- | ------- | -------- | ------------ | ----- | ----------- |
| » message     | string  | true     | none         |       | none        |
| » status      | boolean | true     | none         |       | none        |
| » data        | object  | true     | none         |       | none        |
| »» id         | integer | true     | none         |       | none        |
| »» name       | string  | true     | none         |       | none        |
| »» price      | number  | true     | none         |       | none        |
| »» stock      | integer | true     | none         |       | none        |
| »» created_at | string  | true     | none         |       | none        |
| »» updated_at | string  | true     | none         |       | none        |
| » errors      | null    | true     | none         |       | none        |

# Holds

## POST Store Hold

POST /holds

> Body Parameters

```json
{
    "product_id": 1,
    "qty": 9
}
```

### Params

| Name | Location | Type   | Required | Description |
| ---- | -------- | ------ | -------- | ----------- |
| body | body     | object | yes      | none        |

> Response Examples

> 200 Response

```json
{
    "message": "your hold has been created",
    "status": true,
    "data": {
        "hold_id": 3,
        "expires_at": null
    },
    "errors": null
}
```

> 422 Response

```json
{
    "message": "Product quantity is not enough",
    "status": false,
    "data": null,
    "errors": "Product quantity is not enough"
}
```

### Responses

| HTTP Status Code | Meaning                                                                  | Description | Data schema |
| ---------------- | ------------------------------------------------------------------------ | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)                  | none        | Inline      |
| 422              | [Unprocessable Entity](https://tools.ietf.org/html/rfc2518#section-10.3) | none        | Inline      |

### Responses Data Schema

HTTP Status Code **422**

| Name      | Type    | Required | Restrictions | Title | description |
| --------- | ------- | -------- | ------------ | ----- | ----------- |
| » message | string  | true     | none         |       | none        |
| » status  | boolean | true     | none         |       | none        |
| » data    | null    | true     | none         |       | none        |
| » errors  | string  | true     | none         |       | none        |

# orders

## POST create order

POST /orders

> Body Parameters

```json
{
    "hold_id": 8
}
```

### Params

| Name | Location | Type   | Required | Description |
| ---- | -------- | ------ | -------- | ----------- |
| body | body     | object | yes      | none        |

> Response Examples

> 200 Response

```json
{
    "message": "your order has been booked",
    "status": true,
    "data": {
        "hold_id": 8,
        "status": "pending",
        "amount": 165.3,
        "updated_at": "2025-12-01T21:13:28.000000Z",
        "created_at": "2025-12-01T21:13:28.000000Z",
        "id": 3
    },
    "errors": null
}
```

### Responses

| HTTP Status Code | Meaning                                                 | Description | Data schema |
| ---------------- | ------------------------------------------------------- | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1) | none        | Inline      |

### Responses Data Schema

HTTP Status Code **200**

| Name          | Type    | Required | Restrictions | Title | description |
| ------------- | ------- | -------- | ------------ | ----- | ----------- |
| » message     | string  | true     | none         |       | none        |
| » status      | boolean | true     | none         |       | none        |
| » data        | object  | true     | none         |       | none        |
| »» hold_id    | integer | true     | none         |       | none        |
| »» status     | string  | true     | none         |       | none        |
| »» amount     | number  | true     | none         |       | none        |
| »» updated_at | string  | true     | none         |       | none        |
| »» created_at | string  | true     | none         |       | none        |
| »» id         | integer | true     | none         |       | none        |
| » errors      | null    | true     | none         |       | none        |

# Payment webhook

## POST Webhook Request

POST /payments/webhook

> Body Parameters

```json
{
    "order_id": 3,
    "status": "success"
}
```

### Params

| Name            | Location | Type   | Required | Description    |
| --------------- | -------- | ------ | -------- | -------------- |
| idempotency_key | header   | string | yes      | header or body |
| body            | body     | object | yes      | none           |

> Response Examples

> 200 Response

```json
{
    "message": "Payment processed successfully.",
    "status": true,
    "data": {
        "order_id": 3,
        "order_status": "paid",
        "payment_status": "success",
        "payment_id": 1,
        "hold_released": false
    },
    "errors": null
}
```

> 422 Response

```json
{
    "message": "This order cannot be used. Current status: Paid.",
    "status": false,
    "data": null,
    "errors": "This order cannot be used. Current status: Paid."
}
```

### Responses

| HTTP Status Code | Meaning                                                                  | Description | Data schema |
| ---------------- | ------------------------------------------------------------------------ | ----------- | ----------- |
| 200              | [OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)                  | none        | Inline      |
| 422              | [Unprocessable Entity](https://tools.ietf.org/html/rfc2518#section-10.3) | none        | Inline      |

### Responses Data Schema

HTTP Status Code **200**

| Name              | Type    | Required | Restrictions | Title | description |
| ----------------- | ------- | -------- | ------------ | ----- | ----------- |
| » message         | string  | true     | none         |       | none        |
| » status          | boolean | true     | none         |       | none        |
| » data            | object  | true     | none         |       | none        |
| »» order_id       | integer | true     | none         |       | none        |
| »» order_status   | string  | true     | none         |       | none        |
| »» payment_status | string  | true     | none         |       | none        |
| »» payment_id     | integer | true     | none         |       | none        |
| »» hold_released  | boolean | true     | none         |       | none        |
| » errors          | null    | true     | none         |       | none        |

HTTP Status Code **422**

| Name      | Type    | Required | Restrictions | Title | description |
| --------- | ------- | -------- | ------------ | ----- | ----------- |
| » message | string  | true     | none         |       | none        |
| » status  | boolean | true     | none         |       | none        |
| » data    | null    | true     | none         |       | none        |
| » errors  | string  | true     | none         |       | none        |

# Data Schema
