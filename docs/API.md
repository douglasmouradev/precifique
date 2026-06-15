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
  "device_name": "integração-erp"
}
```

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

## Abilities

| Ability | Descrição |
|---------|-----------|
| `dashboard:read` | Ler resumo do dashboard |
| `tokens:read` | Listar tokens da conta |
| `tokens:write` | Revogar tokens |
| `*` | Acesso total (tokens legados) |

## Limites

- 60 requisições/minuto por token autenticado
- Login/token: rate limit compartilhado com `/entrar`

## Roadmap

Endpoints planejados: produtos, vendas, exportação. A API v1 está em evolução — consulte o repositório antes de integrações críticas.
