# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {BEARER_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Obtenez votre token via <code>POST /api/v1/auth/login</code>. Passez-le dans le header : <code>Authorization: Bearer {token}</code>
