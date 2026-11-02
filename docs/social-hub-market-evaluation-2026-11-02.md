# Vitrine Social Hub — avaliação do radar de 02/11/2026

## Escopo e estado real

Avaliação das sete prioridades autorizadas por EXECUTAR após o resumo de mercado.
Fonte: código e descrições atuais dos PRs Social #9/#12/#15/#16 e Marketing #102.
Nenhum recurso planejado é declarado disponível. Não foram executados merge,
migration, deploy, ativação de flags, posts externos ou chamadas pagas.

## Matriz de avaliação

| Prioridade | Evidência atual | Resultado e próxima integração |
|---|---|---|
| Capacidades por conta | Social #9 usa lista de formatos do receptor; Marketing #102 deriva lista de IDs de conta + configuração, sem comprovar permissões/eligibilidade atual | Adicionada política pura de preflight com identidade exata, prazo de validade e formatos locais. Receptor ainda precisa emitir snapshot confiável; executor ainda não usa esta classe. |
| Estado por destino | ContentProject tem canal único; #9 possui publication_attempts; #12 adiciona social_publication_operations; #16 reserva/inspeciona ambos sem enviar | Preservar o canal/peça atual. Campanha com vários destinos ainda exige vínculo explícito com conta, estados independentes e agregação, sem reciclar sucesso como pendente. |
| Tentativas/limites | #9 bloqueia reenvio incerto; #102 usa ledger e retoma contêiner conhecido | Não adicionar retry automático ao POST. Falta política persistida de next_attempt_at/retry_after para leituras e limites. Concorrência real e reconciliação integrada continuam gates. |
| Aprovações unificadas | #9 protege arquivo e copy por fingerprint; telas existentes operam no contexto do cliente | Fila transversal por clientes, permissões de revisor e revogação durante revisão ainda precisam de contrato e testes. Aprovação simples deve permanecer padrão. |
| Integrações externas | #102 contém conexão/desconexão e revogação Meta por proprietário | Inventário geral de apps, escopos e trilha de revogação ainda precisa de modelo. Separar permissão de rascunho, aprovação e envio. |
| Instruções de marca por IA | Nenhum mecanismo novo foi implementado neste escopo | Tratar como sugestão no Marketing IA, com versão, amostra histórica e aprovação. Mudança de instrução não deve alterar arquivo/copy já aprovado. |
| Construir/comprar | Publicador próprio já possui trabalho e contratos candidatos | Nenhuma compra ou adoção do Mixpost. Comparar custo total e licença comercial antes de considerar alternativa; não introduzir um segundo motor enquanto ledgers atuais são reconciliados. |

## Entrega: AccountPublicationCapabilityPolicy

Política sem banco, HTTP, jobs ou mutação. Recebe:
- destination: project_id, client_id inteiro, account_id e channel;
- snapshot confiável do receptor com a mesma identidade, contract_version=2,
  connected=true, durable_idempotency=true, checked_at/expires_at em Unix segundos
  e capabilities como lista de strings;
- content: placement, type, mime_type e options opcional.

Validade máxima proposta de 300 segundos. A janela é decisão interna conservadora,
não um limite oficial da Meta. A política não autentica o snapshot: o integrador deve
obtê-lo server-side pelo receptor autorizado; nunca aceitar snapshot da interface.
ALLOWED indica apenas compatibilidade de formato, não autorização para publicar.

Lista local conservadora: Instagram Feed JPEG, Stories JPEG/MP4, Reels MP4;
Facebook Feed JPEG/PNG. Carrossel, Facebook Stories/Reels, primeiro comentário e
menções estruturadas permanecem bloqueados porque os adaptadores atuais não os
implementam. Nenhuma capacidade é presumida pela presença do token/ID.

## Sequência de integração

1. Concluir o contrato canônico de reserva/reconciliação dos PRs #9/#12/#16.
2. Emitir o snapshot por conta no receptor do Marketing #102 e validar posse,
   permissões, elegibilidade e revogação server-side.
3. Vincular a conta escolhida à versão aprovada e ao hash/identidade da tentativa;
   trocar a conta deve invalidar a aprovação.
4. Invocar esta política antes de reservar/enviar; revalidar no receptor no envio.
5. Exercitar com HTTP simulado, contas revogadas, troca de conta e concorrência
   em HML isolada antes de qualquer autorização de tráfego externo.

## Ambiente

O Super Centro confirmou o projeto canônico
vitrine-social-publishing-v2-validation, em branch do PR #9, com Compose dedicado,
sem domínio público cadastrado. A existência do registro não comprova health atual
ou homologação Social → Marketing. Não criar outro ambiente por conveniência.

## Validação

Nova suíte unitária cobre identidade, token lógico revogado, frescor, contrato legado,
MIME, formatos não implementados e opções desconhecidas. Workflow isolado mantém
as três flags de publicação/lookup desativadas e executa também as suítes anteriores.
O resultado de CI deve ser registrado no PR; não equivale a homologação operacional.
