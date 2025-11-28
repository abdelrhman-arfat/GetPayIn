# Flash Sale Checkout — Tasks

## Task 1 — Product Endpoint

Create an API for a single product with limited stock and accurate availability.

- API: `GET /api/products/{id}`
- Returns basic product info and current available stock.
- Must remain fast and correct under high load.

## Task 2 — Create Hold

Create a temporary reservation of stock (~2 minutes).

- API: `POST /api/holds { "product_id": 1, "qty": 2 }`
- Check if enough stock is available.
- On success, return: `{ "hold_id": X, "expires_at": "timestamp" }`
- Hold immediately reduces availability for others.
- Expired holds must automatically release stock (via background job).

## Task 3 — Create Order

Convert a valid hold into a preliminary order.

- API: `POST /api/orders { "hold_id": X }`
- Conditions: hold must be valid and unexpired, each hold can be used only once.
- On success, order status should be `pending`.

## Task 4 — Payment Webhook (Idempotent & Out-of-Order Safe)

Update order status after payment in a safe way.

- API: `POST /api/payments/webhook { "order_id": X, "status": "success|failed", "idempotency_key": "unique-key" }`
- Requirements:
    - Handling the same webhook multiple times should not incorrectly change order state.
    - Must handle webhooks arriving before or after order creation.
    - On success → set order status to `paid`.
    - On failure → cancel order and release stock.
    - Use `idempotency_key` to guarantee safe deduplication.

## Task 5 — Additional Requirements

- No overselling under heavy concurrency.
- Expired holds must release stock reliably.
- Use caching to improve read performance without stale stock.
- Avoid N+1 queries in list endpoints.
- Structured logging for concurrency, retries, and webhook deduplication.
- All tasks are API only; no UI is required.
