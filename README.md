# Sistema Morya (versão MySQL)

Esta é uma nova versão do sistema, baseada no código original, mas utilizando banco de dados MySQL para persistência de dados (usuários, fornecedores, certidões, histórico de envios, etc).

## Estrutura
- `public/` — arquivos públicos e páginas do sistema
- `src/` — classes e lógica de backend

## Como usar
1. Configure o banco de dados MySQL usando o script em `src/database.sql`.
2. Ajuste as credenciais de conexão no arquivo de configuração.
3. Acesse normalmente pelo navegador.

## Observação
Os arquivos e layout são baseados no sistema original, mas toda a lógica de dados foi adaptada para MySQL. 