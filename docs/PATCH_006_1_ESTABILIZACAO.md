# Patch 006.1 — Estabilização HostGator

## Objetivo

Corrigir problemas de painel desconfigurado no ambiente HostGator e padronizar o refresh do projeto após cada atualização via GitHub.

## Quando usar

Sempre que fizer `git pull` ou quando o Filament abrir com layout quebrado, execute no terminal:

```bash
cd ~/vitrine-ai-social-enterprise
bash scripts/refresh-hostgator.sh
```

## O que o script faz

- Atualiza o código pelo GitHub.
- Instala dependências PHP otimizadas para produção.
- Limpa caches do Laravel e Filament.
- Executa migrations pendentes.
- Recria link de storage quando possível.
- Publica/atualiza assets do Filament.
- Reotimiza a aplicação.

## Teste esperado

Após executar, acessar:

```text
https://social.vitrineiapro.com.br/admin
```

O painel deve abrir com layout normal.
