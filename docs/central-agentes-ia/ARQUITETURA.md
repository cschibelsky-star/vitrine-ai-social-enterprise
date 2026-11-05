# Central de Agentes IA — protótipo HML

Fonte: transcrição fornecida pelo usuário em 05/11/2026, vídeo de 1m54s.

## Oito camadas
1. Dados: exportação diária e tendências em janela de 7 dias; conectores externos inicialmente mocks.
2. Análise: desempenho relativo à mediana do próprio criador; sinal se razão > 3.
3. Juízo: avaliação cega de estrutura narrativa, sem views nem identidade do perfil.
4. Conselho: papéis ousado, cético, árbitro e estrategista; ata e veto/aprovação humana.
5. Roteiro: autor, revisor independente e verificador de números nas fontes.
6. Memória: correções versionadas, aprovadas e consultadas antes de tarefas.
7. Produção: interface para mídia/Remotion, sem acionar serviços pagos.
8. Publicação: aprovação humana explícita; integração futura com Social Hub.

## Evidências verificadas no código
- app/Services/AI/AiContentService.php: integração Centro IA, geração e status pending_approval; fallback fake-content-engine-v1 deve ser sinalizado como simulado.
- app/Services/Publishing/MetaPublisherService.php: bloqueia publish se status não é ready/scheduled e limita canal a Instagram/Facebook.

## Limites da referência
O vídeo afirma 84 programas, agentes de 5 fornecedores, estudo de 43 perfis, 15 hipóteses, 65 famílias de ganho e 51 regras. Não revela implementações ou listas completas. Nenhuma dessas estruturas será inventada.
Não confundir sinal >3 com previsão comprovada de viralização. Mediana zero ou ausente produz análise inconclusiva.

## Contrato mínimo de workflow
DRAFT -> COLLECTED -> ANALYZED -> JUDGED -> COUNCIL_REVIEW -> HUMAN_APPROVED -> SCRIPT_REVIEW -> PRODUCTION_REVIEW -> PUBLICATION_APPROVED -> PUBLISHED.
VETOED e FAILED são estados terminais; reabertura exige nova revisão.
Toda transição exige ator, data, origem e evidência. Publicação exige autorização explícita específica.

## Plano de integração
1. Motor offline de razão versus mediana com testes.
2. Armazenamento auditável de sinais e avaliações cegas.
3. Conselho com mocks e ata verificável.
4. Aprovações humanas em HML.
5. Adaptadores Centro IA, Marketing IA e Social Hub, somente após contratos e testes.
6. HML isolada, sem custos de API e sem publicação real até autorização específica.

## Segurança
Sem segredos no repositório, sem auto-publicação, sem chamadas pagas, sem migrations ou deploy de produção.
