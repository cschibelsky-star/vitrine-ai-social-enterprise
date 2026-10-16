# Publicação própria — auditoria e implementação de 12/10/2026

Estado: implementação candidata em branch, publicação externa desativada por padrão. Não houve publicação real, geração de mídia nem consumo de créditos de IA. Merge, instalação de migration e deploy na VPS não realizados.

## Evidências operacionais

- GitHub main auditado: b4e17c52c852486aadbe5bb5bdd7a54e286cd045.
- V5 project_status: árvore limpa, main atrás de origin/main por 2 commits.
- Super project_inspect: /srv/projects/vitrine-ai-social-enterprise/repository; compose.studio.yml; nenhum domínio HML/produção registrado. Esse registry não comprova isolamento seguro.
- V5 project_docker_status: studio_app, studio_web, studio_worker e studio_scheduler ativos. Worker ativo não comprova processamento/publicação funcional.
- Super runtime_binding_list: zero bindings registrados para Social. Isso não prova ausência das configurações legadas services.marketing_engine.
- Fonte atual: Social envia conteúdo para endpoints internos /api/internal/marketing/publisher/meta no Marketing; mídia image/video possui configuração própria. Não foi localizado o receptor Meta no main do repositório vitrine-ai-pro. O comportamento do receptor permanece não comprovado.

## Falhas corrigidas no candidato

Publicador anterior gerava PNG durante a publicação, podendo alterar a arte aprovada e consumir créditos. Não havia ledger transacional ou snapshot da aprovação; timeouts poderiam permitir duplicação. type=image não distinguia Feed/Stories/Reels. Agendamento não possuía busca periódica de registros vencidos e interpretava o horário sem timezone explícito.

Agora Social possui upload e prévia por cliente, cópia final imutável, validação MIME/dimensões, conversão PNG→JPEG para Instagram ANTES da aprovação e inspeção MP4 com ffprobe. Aprovação guarda usuário/data/fingerprint do arquivo + copy + cliente + canal + formato. Alteração exige nova aprovação. Publicação nunca invoca geração de IA. Scheduler usa banco como fonte e dispatch atrasado como otimização. Datas são recebidas em America/Sao_Paulo e persistidas em UTC.

Ledger UNIQUE(content_project_id) e UNIQUE(idempotency_key) + lock da linha antes do envio. Uma peça permite uma única tentativa externa; republicação intencional exige novo projeto e aprovação. Respostas incertas/PROCESSING não viram published. Não há retry cego de POST. Reconciliação consulta ledger do receptor, nunca reenvia. Isso privilegia prevenção de duplicações; erros posteriores ao envio requerem reconciliação operacional.

## Contratos Meta e limites

Fontes oficiais consultadas em 12/10/2026:
- https://developers.facebook.com/documentation/instagram-platform/content-publishing
- https://developers.facebook.com/documentation/video-api/page-stories-api
- https://www.postman.com/meta/instagram/documentation/6yqw8pt/instagram-api
- https://www.postman.com/meta/facebook/documentation/r56bjfd/facebook-api
- https://github.com/fbsamples/Facebook-Reels-Publishing-API-Postman-Collection

Algumas páginas diretas retornaram 429; detalhes finais devem ser verificados contra a versão Graph autorizada no aplicativo antes da ativação.

| Canal / formato | Receptor precisa implementar | Candidato Social |
|---|---|---|
| Instagram Feed imagem | /{ig-user}/media com image_url JPEG → /media_publish | contrato feed/image |
| Instagram Stories | /media media_type=STORIES + image_url ou video_url → processamento → /media_publish | contrato stories/image ou video, condicionado à elegibilidade da conta |
| Instagram Reels | /media media_type=REELS + video_url → status_code FINISHED → /media_publish | contrato reels/video |
| Facebook Feed imagem | /{page}/photos | contrato feed/image |
| Facebook Stories | /photos published=false → /photo_stories; vídeo possui upload + /video_stories | contrato stories/image ou video |
| Facebook Reels | /video_reels upload_phase=start → upload → finish; confirmar resultado | contrato reels/video |

Perfil MP4 conservador desta versão: 9:16, ≥540×960, 4–60 segundos, 23–60fps, H264/HEVC e áudio AAC quando presente. Não é uma afirmação do limite universal atual da Meta. Vídeo Feed, carrossel, stickers/música interativa e perfis pessoais Facebook não são atendidos nesta versão.

## Contrato interno v2 (proposto, receptor ainda pendente)

`status` deve responder connected=true, contract_version=2, durable_idempotency=true e capabilities contendo, por exemplo, instagram.reels.video. Capacidades só devem ser declaradas depois de validar permissões, conta e adaptador real. Sem isso, Social bloqueia o envio inclusive Feed legado.

`publish` recebe project_id (produto), client_id (tenant), content_project_id (peça), contract_version=2, channel, placement, type, mime_type, asset_url assinada HTTPS, asset_sha256, caption e idempotency_key. Receptor deve persistir chave antes de qualquer efeito, isolar tenant/produto/conta, guardar IDs de upload/container e nunca repetir media_publish/finish após resultado incerto. Meta não recebe automaticamente a chave interna como garantia de idempotência.

Resposta final: ok=true, status=PUBLISHED, external_id string não vazia. PROCESSING/PENDING permanecem pendentes.

`publication-status` é somente consulta, com os IDs e chave da tentativa; responde a mesma idempotency_key, status e external_id confirmado. Não pode gerar upload nem publicar. Chave divergente/404/timeout preserva bloqueio.

## Separação de responsabilidades

Marketing IA produz versões de arquivos. Social importa/revisa/aprova/agenda e mantém o ledger. Centro Operacional mantém identidade, acesso, ambiente, configuração e evidência. A rota legada do publicador é mantida como compatibilidade de transporte, sem mover billing/geração/credenciais Meta para Social nem para Cockpit. A extração futura do receptor para serviço de distribuição deve preservar esse contrato; não alterar o Core para implementar publicação.

## Materiais de teste

ZIP autenticado do Drive inspecionado: 1UwP2-CSOkSKk0YtLLrMwCs85j1EcKfMC. Os 30 arquivos foram baixados para análise temporária; o repositório recebe apenas manifesto SHA256/metadados, sem expor criativos.
20 PNGs: 10 de 1080×1080 e 10 de 1080×1920. Os 10 MP4s são 1080×1920, H264/AAC, duração 16,166667s e taxa média ≈24,06fps. Passam o perfil conservador. PNG exige cópia JPEG revisada para Instagram. Não usar link de visualização Drive como mídia pública Meta nem tornar a pasta pública.

## Configuração/autorização externa pendente

1. Identificar e versionar o receptor operacional Meta; implementar/validar contrato v2, adaptadores por formato, ledger durable e consulta.
2. Confirmar Meta App, login escolhido, versão Graph, tokens válidos e escopos. Com Facebook Login, validar instagram_basic, instagram_content_publish, pages_read_engagement e permissões de Página necessárias (pages_show_list para descoberta; pages_manage_posts para publicação). Instagram Login usa escopos instagram_business_* próprios. Não misturar os dois fluxos.
3. Validar conta profissional Instagram elegível para Stories, Página Facebook, tarefas CREATE_CONTENT e Advanced Access/App Review para clientes externos; autorização de cada cliente.
4. Provisionar HML isolada com banco, fila, cache, storage e APP_KEY próprios e cartão Cockpit; publication flag false. Registry atual não lista domínio HML.
5. Instalar migration apenas na HML isolada autorizada; instalar ffprobe/ffmpeg + GD; ajustar tamanho de upload PHP/Nginx/Livewire. Confirmar queue retry_after > timeout do job e timeout HTTP acumulado menor que worker.
6. Confirmar URL HTTPS que Meta consiga baixar, entrega de vídeo com Range e duração da URL assinada suficiente; APP_URL canônica e proxy trusted.
7. Após testes mockados e verificação das permissões, ativação externa exige etapa específica de configuração/autorização. Esta tarefa não autoriza posts reais.

## Validação

Workflow publishing-safety.yml usa runner isolado, SQLite em memória, Storage fake e Http::preventStrayRequests; nenhuma credencial real. Casos de Feed/Stories/Reels, opt-in desativado, alteração pós-aprovação, arquivo adulterado, receptor legado, erro/timeout, processamento assíncrono, schedule futuro/cancelado, UNIQUE contra duplicação, reconciliação, URL sem assinatura, importação PNG→JPEG e manifesto da campanha. CI isolada não equivale a homologação operacional na VPS.
