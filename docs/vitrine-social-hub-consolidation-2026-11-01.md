# Vitrine Social Hub — plano de integração dos publicadores (01/11/2026)

> Status: proposta para homologação; **nenhuma integração, migration, publicação Meta ou deploy está autorizada por este documento**.
> Nome oficial: **Vitrine Social Hub — by Vitrine IA Pro**.

## Fontes verificadas

- Social [PR #9](https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/pull/9): publicador v2, `publication_attempts`, arquivo aprovado/fingerprint, estados e reconciliação via receptor.
- Social [PR #12](https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/pull/12): tabela `social_publication_operations` com unicidade cliente/projeto/canal/versão e `idempotency_key`.
- Marketing [PR #102](https://github.com/cschibelsky-star/vitrine-ai-pro/pull/102): receptor nativo v2, ledger em filesystem persistente com flock, estados de submissão e lookup sem efeitos colaterais.
- [Issue #11](https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/issues/11) e [issue #13](https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/issues/13).

## Divergências verificadas e decisões propostas

1. **Dois ledgers no Social.** O PR #9 usa `publication_attempts` com restrição por `content_project_id`, equivalente a uma única tentativa por peça. O PR #12 introduz `social_publication_operations` com identidade cliente/projeto/canal/versão. Não criar duas filas independentes: definir uma entidade canônica de operação e uma relação explícita com a tentativa de transporte Meta. Antes de migrar, comparar cardinalidade e a semântica de `content_version` e `schedule_generation`; nunca reinterpretar status antigos automaticamente.
2. **Contrato de idempotência entre produtos.** Preservar `contract_version=2` e definir identidade estável por produto+tenant+conteúdo+versão+canal+placement, vinculada a um fingerprint da mídia/copy aprovadas. Rejeitar alteração de payload com a mesma chave; rejeitar chaves diferentes para a mesma operação já submetida. Confirmar compatibilidade exata com o cálculo atual dos PRs antes de ativar.
3. **Marketing continua dono do transporte Meta.** O receptor do PR #102 tem ledger próprio e guarda IDs de container/externos. O Social Hub mantém a intenção, autorização e visibilidade da operação; o Marketing registra tentativa de transporte. Ambos devem reconciliar pela mesma chave e escopo de tenant. Resposta `UNKNOWN` jamais deve disparar novo POST automaticamente.
4. **Concorrência e durabilidade.** O PR #102 usa `flock` e armazenamento local persistente: válido apenas se os workers compartilham o mesmo filesystem/host. Para múltiplas instâncias, migrar o ledger de transporte a banco/lock compartilhados antes de habilitar escalabilidade.
5. **Compatibilidade de formatos.** Instagram Feed requer JPEG revisado, Stories imagem/vídeo e Reels MP4 conforme capacidades e elegibilidade da conta; Facebook Feed imagem inicialmente. Facebook Stories/Reels permanecem bloqueados. Nunca reclassificar automaticamente o placement.
6. **Arquivo final/URL.** A aprovação protege hash e metadados da mídia; verificar que a URL HTTPS entregue ao Graph aponta exatamente para bytes aprovados e permaneça acessível/imutável até a confirmação. A galeria Marketing não converte PNG automaticamente e exige revisão explícita da versão JPEG.
7. **Agendamento e mudanças de aprovação.** Jobs vencidos ou cancelados não podem enviar; geração do agendamento deve ser validada. Uma submissão já iniciada impede alteração destrutiva até reconciliação.
8. **Isolamento.** Autorizar conexão, `client_id`, `project_id`, `content_project_id` e escopo do token interno em ambos os lados. Não inferir posse de tenant de identificadores enviados pelo cliente.
9. **Estados canônicos propostos.** `draft/review/approved/scheduled/queued/submitting/processing/published/unknown/blocked/cancelled`. Elaborar mapeamento explícito com os estados legados; `unknown` demanda verificação, não retry cego.

## Sequência de implementação

- [ ] Mapear modelos, migrations, serviços, jobs e rotas dos PRs #9 e #12 em uma matriz de impacto e determinar uma fonte canônica de identidade.
- [ ] Escrever testes de compatibilidade com identidade duplicada e concorrência entre os dois modelos; não aplicar migrations até validar política de dados.
- [ ] Integrar o contrato v2 do Social com o receptor Marketing do PR #102 mantendo publicação desativada por padrão.
- [ ] Validar `UNKNOWN`, timeout antes/depois do POST, falha de consulta, reentrância, alteração de copy, cancelamento e mudança de canal.
- [ ] Testar integração de ponta a ponta em HML isolada (banco, filas, worker, scheduler, storage, HTTPS, contas de teste, callbacks) e avaliar dependências Meta.
- [ ] Validar Cockpit, observabilidade/alertas VIA e documentação operacional antes de qualquer aprovação de produção.

## Gate de homologação

**Não fazer merge/deploy ou executar migrations na produção enquanto** não existirem evidências de: (a) ausência de envio duplicado sob timeout/reexecução, (b) conformidade de tenant e autorização por canal/formato, (c) ambiente HML isolado e migrações reversíveis, (d) reconciliação sem reenvio incerto, (e) aprovação formal no Cockpit.

A documentação pode ser integrada independentemente do código; este PR **não modifica runtime**.
