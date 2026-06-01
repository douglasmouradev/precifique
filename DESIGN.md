# Guia visual — Precifique

## Cores

| Token | Hex | Uso |
|-------|-----|-----|
| `brand` | `#00C896` | CTAs, destaques, lucro |
| `brand-dark` | `#00A67D` | Hover, textos de ênfase |
| `ink` | `#0D0D0D` | Texto principal, botões primários escuros |
| `paper` | `#F8FAFC` | Fundo do app |

## Tipografia

- **Corpo:** Plus Jakarta Sans
- **Títulos:** Instrument Sans (`font-display`)

## Componentes Blade

- `x-ui.card` / `x-ui.card-hover` / `x-ui.card-premium` — superfícies
- `x-ui.setup-progress` — wizard de configuração da conta (sidebar do app)
- `x-ui.button` — variantes: `primary`, `secondary`, `outline`, `ghost`
- `x-ui.empty-state` — listas vazias
- `x-ui.confirm-delete` — exclusão com modal
- `x-ui.toast-container` — notificações (via `window.toast`)

## Landing vs App

- **Landing:** fundo escuro no hero, animações 3D (respeitar `prefers-reduced-motion`)
- **App:** sidebar `#0a0a0a`, conteúdo em `paper`, cards brancos

## Acessibilidade

- Contraste mínimo 4.5:1 em textos
- Foco visível em botões (`focus:ring`)
- `aria-label` em ícones de menu
