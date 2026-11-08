# HANDOFF — Diretor de Reels IA V1

- Escopo: prévia editorial offline multi-tenant no painel do cliente e análise de três hooks iniciais.
- Fonte canônica: `cschibelsky-star/vitrine-ai-social-enterprise`, branch `feature/social-reel-director-offline-v1`.
- Mudanças propostas: `ReelDirectorService`, página Filament ReelDirector, view Blade, registro no ClientPanelProvider, smoke offline, CI e contrato.
- Estado: branch/PR candidato; não publicado, não integrado aos motores de vídeo nem a aprovações VIA.
- Proteções: sem novas migrations, sem chamadas externas, sem alteração no publicador, sem custo de API, tenant scoped, sempre `human_approval_required=true`.
- Testes locais: lint PHP e 9 verificações offline em `tests/reel_director_smoke.php` (executados na preparação). Confirmar novamente em CI e HML.
- Pendências: homologação Filament real com login por cliente, testes com o componente visual, calibração dos ganchos e decisão de evoluir para as 26 fórmulas; nenhuma homologação operacional presumida.
- Critério para avanço: QA e evidências do gate HML; aprovação humana e autorização específica para merge/deploy.
