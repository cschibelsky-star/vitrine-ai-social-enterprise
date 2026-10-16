# Validação operacional privada do publicador Social

## Atualização operacional — 16/10/2026, 09h16 BRT

Estado PARCIAL: bloqueio inicial de build/inicialização superado para o projeto canônico
vitrine-social-publishing-v2-validation. Esta atualização substitui o estado anterior de VPS
“preparado, sem ativação”; as seções históricas abaixo registram a primeira etapa.

Foi encontrado um projeto de validação já cadastrado nos dois conectores, com Compose autorizado.
Sua árvore limpa estava em 8a3e447. Atualizações feitas por fast-forward até
7bdb7c0a22b95e6380fd1c1a6950e9fe36c57ccb, referência atual do PR Social #9.
As cópias social-publisher-v2-hml e marketing-publisher-v2-hml criadas anteriormente não receberam
serviços; não são ambientes adicionais homologados e não devem ser usadas como nova referência.

Operações confirmadas via Super Centro Operacional:
- Build da imagem; a primeira chamada expirou, mas inspeção posterior confirmou a imagem criada.
- Inicialização de validation_secret_init, validation_db, validation_redis e validation_app.
- Credenciais locais dedicadas criadas no volume de validação sem revelar valores.
- Estágio Docker validation opt-in com dependências de testes e SQLite; estágio final padrão production
  deriva do runtime original e continua sem dependências de desenvolvimento.
- Startup exclusivo do Compose de validação executa os testes antes do PHP-FPM.
- Rede interna, sem portas publicadas; storage, banco e secrets em volumes próprios.
- MariaDB healthy; Redis e PHP-FPM ativos. Duas rotas de mídia listadas via Laravel.

Evidência no container validation_app (PHP 8.3.33, PHPUnit 12.5.30):
OK (25 tests, 172 assertions), em 1,762s.

Escopo efetivamente validado:
- Suíte Social PublishingSafetyTest: 23 testes/158 assertions, com SQLite em memória,
  Storage fake e HTTP simulado, executada no container da VPS.
- Worker real por subprocesso queue:work redis em fila sintética única no Redis validation_redis:
  falha controlada na tentativa 1, conclusão na tentativa 2, fila vazia e nenhuma terceira execução.
- Executor real schedule:run com agenda sintética isolada: evento devido executado e evento vetado ignorado.
- Filas e marcadores sintéticos removidos no finally. Nenhum envio a redes sociais.

A suíte não prova consumo de ContentProject por workers persistentes, transações no MariaDB,
concorrência de dois publicadores, reconciliação entre hosts, scheduler em daemon nem
tráfego Social -> receptor Marketing pela rede. Os testes de contratos continuam usando HTTP fake.
A prova do scheduler substitui sua agenda no container de teste, não executa a agenda de clientes.
QUEUE_CONNECTION do aplicativo de validação permanece sync; o teste seleciona Redis explicitamente.

CI do commit 7bdb7c0:
- Publishing safety SUCCESS: https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/actions/runs/38899234210
- Product Contract SUCCESS: https://github.com/cschibelsky-star/vitrine-ai-social-enterprise/actions/runs/38899234514

Pendentes: receptor Marketing isolado em rede; fila persistente de publicações reais simuladas;
calendários e permissões ponta a ponta; mídia HTTPS/Range; Cockpit; consentimentos/gates Meta.
Sem merge, migração de banco persistente, publicação real, alteração de produção ou uso do PR #10.
O banco MariaDB de validação foi inicializado pelo container, sem executar migrations de aplicação.

