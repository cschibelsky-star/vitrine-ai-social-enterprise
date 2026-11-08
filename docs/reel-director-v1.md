# Diretor de Reels IA — primeiro incremento seguro

## Contrato e ownership

- Marketing IA / Centro IA continua responsável pela geração do texto-base e por decidir qual modelo utilizar. Esta entrega **não faz chamadas de IA**.
- Vitrine Social Mídia oferece uma prévia editorial **somente leitura**, a partir de projetos `format=reels` do cliente autenticado.
- `ReelDirectorService::preview(array $context)` recebe tema, legenda, CTA e palavras proibidas do Brand Kit e devolve três hooks, indicador heurístico, beats, tempo, alertas e `human_approval_required=true`.
- O usuário final mantém toda a decisão de aprovação. O serviço não altera `ContentProject`, `ContentGeneration`, ledgers, jobs, agendamento ou publicação.
- Pontuações são **heurísticas locais**, não a pontuação original do `hookscore.py`, não previsões de resultado e não garantem viralização.
- São utilizados apenas três modelos iniciais de hooks; o catálogo completo de 26 modelos do repositório original **não foi importado** nesta fase. Referência: https://github.com/Jakeschincariol/instagram-agent-skill (MIT).

## Limites

- A prévia recorta o texto-base em até 64 palavras e estima a duração a 150 palavras por minuto. Não é transcrição, nem roteiro final validado, nem análise real de retenção.
- Falta de briefing, CTA, expressões bloqueadas, ganchos/trechos longos e fatos não verificados geram pendências.
- Somente projetos pertencentes ao cliente logado e no formato `reels` ficam visíveis. A página não permite selecionar IDs de outros clientes nem alterar dados.
- Nenhum gatilho de Meta, Metricool, Flow ou HeyGen é conectado.

## QA / Gate HML

1. Executar `php -l` no serviço e na nova página.
2. Executar `php tests/reel_director_smoke.php` (offline).
3. Em HML isolada, abrir `/app/diretor-reels` com um usuário de cliente, confirmar seleção dos Reels e prévia; validar tentativas de `?project_id=` de outro tenant e login ausente.
4. Verificar ausência de mudança de status, geração de jobs, consumo de saldo, cobrança ou publicação externa.
5. Só depois de evidências de QA real avaliar UX, catálogo de 26 fórmulas, integração com aprovação VIA e eventual escopo do Marketing IA.

Não realizar merge/deploy/migrations sem autorização específica.


## Incremento: catálogo e segurança de autoria (08/11/2026)

- Catálogo integral de 26 fórmulas importado de `Jakeschincariol/instagram-agent-skill` para `resources/reels/instagram-hooks.json`, preservando licença original em `resources/reels/UPSTREAM-LICENSE.txt`.
- O catálogo é consultivo, sem preenchimento automático dos modelos que exigem métricas, perguntas literais, resultados ou provas externas.
- A pergunta automática foi relabelada como **variação editorial própria**, não como a fórmula original #16 Verbatim Question.
- A URL do item usa a classe Filament explícita, em lugar de `static::getUrl` dentro da view.
- Adicionado teste de autorização/isolamento em `tests/Feature/ReelDirectorAccessTest.php`, que exige execução com dependências Laravel e banco de teste.
- O pré-voo offline segue com três ganchos; não há execução de modelos pagos, gravação de mídia ou publicação.
