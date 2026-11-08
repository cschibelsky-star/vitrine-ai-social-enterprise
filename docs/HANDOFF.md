# HANDOFF — Diretor de Reels IA V1

- Escopo: prévia editorial offline multi-tenant no painel do cliente e análise de três hooks iniciais.
- Fonte canônica: `cschibelsky-star/vitrine-ai-social-enterprise`, branch `feature/social-reel-director-offline-v1`.
- Mudanças propostas: `ReelDirectorService`, página Filament ReelDirector, view Blade, registro no ClientPanelProvider, smoke offline, CI e contrato.
- Estado: branch/PR candidato; não publicado, não integrado aos motores de vídeo nem a aprovações VIA.
- Proteções: sem novas migrations, sem chamadas externas, sem alteração no publicador, sem custo de API, tenant scoped, sempre `human_approval_required=true`.
- Testes locais: lint PHP e 9 verificações offline em `tests/reel_director_smoke.php` (executados na preparação). Confirmar novamente em CI e HML.
- Pendências: homologação Filament real com login por cliente, testes com o componente visual, calibração dos ganchos e decisão de evoluir para as 26 fórmulas; nenhuma homologação operacional presumida.
- Critério para avanço: QA e evidências do gate HML; aprovação humana e autorização específica para merge/deploy.


## Continuação (08/11/2026)
- Conferido PR #19, CI inicial `Reel Director Offline Smoke` **success** em https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/actions/runs/42532844511 (commit `abb6667`).
- Corrigida atribuição indevida da fórmula #16; catálogo upstream de 26 fórmulas e licença MIT incorporados ao Git.
- View Blade ajustada para resolver URL da página via classe Filament explicitamente.
- Adicionado teste Feature de bloqueio de visitantes e seleção de projeto de outro tenant; **não executado** até disponibilizar banco Laravel/CI integrado.
- Workflow deve ser reavaliado depois dos novos commits; CI anterior não valida este incremento.
- Pendente: teste Feature Laravel, funcionamento visual em HML isolada e validação multi-tenant operacional. Nenhum deploy/merge/migration.

## Gate CI integrado (08/11/2026) — comprovado

- Suite isolada Laravel/Filament/SQLite em memória, sem uso de banco da VPS nem chamadas externas:
  - https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/actions/runs/42534775243
  - Resultado: **2 tests passed (10 assertions)**, sem avisos do PHPUnit.
  - Rota `GET /app/diretor-reels` registrada; `php artisan view:cache` aprovado.
  - Anônimo bloqueado; cliente A não vê dados de cliente B; status dos projetos permanece `draft`.
- Workflow de regressão completo:
  - https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/actions/runs/42534775263
  - Jobs `smoke` e `filament-integration` concluídos em **success**.
- Causa e correção dos avisos iniciais: inexistência de `.env` no runner; workflows agora copiam `.env.example` para o ambiente temporário de CI. O teste também desabilita Vite. Flag `--display-warnings --fail-on-warning` aplicada.
- Limite de evidência: testado no runner GitHub, não na VPS HML; não existe validação visual do layout, fluxo real de navegador ou integração com supervisão VIA.
- Gate para merge/deploy continua bloqueado até HML isolada, inspeção visual e autorização específica. Não reconciliar o checkout da VPS (main atrás de origin/main) no escopo deste PR.
