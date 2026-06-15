# API REST — Precifique v1

Base URL: `https://seu-dominio.com/api/v1`

## Autenticação

### Obter token

```http
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "loja@exemplo.com",
  "password": "sua-senha",
  "device_name": "integração-erp",
  "abilities": ["dashboard:read", "products:read", "sales:write"]
}
```

Se `abilities` for omitido, o token recebe apenas leitura (`dashboard:read`, `products:read`, `sales:read`).

Documentação web: `/docs/api` · Gestão de tokens: **Minha conta** no app.

Resposta `200`:

```json
{
  "token": "plain-text-token-salve-agora",
  "token_type": "Bearer",
  "tenant": { "id": 1, "name": "Minha Loja", "email": "loja@exemplo.com" }
}
```

Use o header `Authorization: Bearer {token}` nas requisições seguintes.

**Pré-requisitos:** a conta deve ter aceito os termos LGPD, concluído o perfil e o onboarding no painel web.

### Listar tokens

```http
GET /api/v1/auth/tokens
Authorization: Bearer {token}
```

Requer ability `tokens:read`.

### Revogar token

```http
DELETE /api/v1/auth/tokens/{id}
Authorization: Bearer {token}
```

Requer ability `tokens:write`. Revoga o token pelo ID (não pelo valor em texto).

## Endpoints

### Resumo do dashboard

```http
GET /api/v1/dashboard/summary
Authorization: Bearer {token}
```

Requer ability `dashboard:read`.

Resposta:

```json
{
  "month_revenue": 1500.0,
  "sales_count": 12,
  "goal_amount": 2000.0,
  "goal_progress": 75.0,
  "products_count": 8
}
```

### Produtos

```http
GET /api/v1/products
GET /api/v1/products/{id}
Authorization: Bearer {token}
```

Requer ability `products:read`. Lista paginada com nome, preço, estoque e status.

```http
PATCH /api/v1/products/{id}/stock
Authorization: Bearer {token}
Content-Type: application/json

{ "stock_quantity": 10, "min_stock_alert": 3 }
```

Requer ability `products:write`.

### Vendas

```http
GET /api/v1/sales
GET /api/v1/sales/{id}
Authorization: Bearer {token}
```

Requer ability `sales:read`. Filtro opcional: `?payment_method=pix`.

```http
POST /api/v1/sales
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2,
  "unit_price": 25.90,
  "payment_method": "pix"
}
```

Requer ability `sales:write`.

## Abilities

| Ability | Descrição |
|---------|-----------|
| `dashboard:read` | Ler resumo do dashboard |
| `products:read` | Listar e ver produtos |
| `products:write` | Atualizar estoque |
| `sales:read` | Listar e ver vendas |
| `sales:write` | Registrar vendas |
| `tokens:read` | Listar tokens da conta |
| `tokens:write` | Revogar tokens |
| `*` | Acesso total (tokens legados) |

## Limites

- 60 requisições/minuto por token autenticado
- Login/token: rate limit compartilhado com `/entrar`
