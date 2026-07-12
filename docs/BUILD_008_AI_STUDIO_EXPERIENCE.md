# BUILD 008 — AI Studio Experience

## Objetivo

Transformar o painel administrativo em uma experiência orientada à criação de conteúdo.

## Entregas

- Home com chamada principal para criação.
- Atalhos para Post, Carrossel, Reels e Story.
- Conteúdos recentes no dashboard.
- Wizard em quatro etapas:
  1. Cliente e Brand Kit.
  2. Rede social e formato.
  3. Objetivo.
  4. Tema.
- Brand Kits filtrados automaticamente pelo cliente.
- Geração automática após a criação do projeto.
- Continuidade para o editor já existente.
- Script único de implantação e validação.

## Instalação na VPS

```bash
cd /srv/apps/vitrine-ai-social-enterprise
git pull origin main
chmod +x scripts/deploy-build-008.sh
bash scripts/deploy-build-008.sh
```

## Homologação

1. Abrir `/admin`.
2. Confirmar o novo bloco "O que vamos criar hoje?".
3. Confirmar os quatro atalhos de formato.
4. Abrir "Criar conteúdo com IA".
5. Confirmar o Wizard em quatro etapas.
6. Selecionar um cliente e confirmar que aparecem apenas seus Brand Kits.
7. Criar um projeto.
8. Confirmar o redirecionamento para edição com conteúdo gerado.
9. Confirmar que o projeto aparece em "Continue de onde parou".

## Rollback

```bash
cd /srv/apps/vitrine-ai-social-enterprise
git log --oneline -10
git reset --hard <COMMIT_ANTERIOR>
docker compose -f compose.studio.yml up -d --build --force-recreate
```
