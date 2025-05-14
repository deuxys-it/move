# Sistema Morya - Versão com Banco de Dados

Sistema de gerenciamento de fornecedores, certidões e envio de e-mails.

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache/Nginx)

## Instalação

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITORIO]
cd move_db
```

2. Instale as dependências via Composer:
```bash
composer install
```

3. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Importe o arquivo `src/database.sql`
   - Configure as credenciais do banco no arquivo `.env`

4. Configure o arquivo `.env`:
```env
DB_HOST=localhost
DB_NAME=nome_do_banco
DB_USER=usuario
DB_PASS=senha
```

5. Configure as permissões:
```bash
chmod 755 -R public/
chmod 777 -R public/assinaturas/
```

6. Configure o servidor web:
   - Apache: Configure o DocumentRoot para apontar para a pasta `public/`
   - Nginx: Configure o root para apontar para a pasta `public/`

## Estrutura de Diretórios

```
move_db/
├── public/             # Arquivos públicos
│   ├── assinaturas/    # Armazenamento de fotos
│   ├── index.php       # Página de login
│   └── ...
├── src/                # Código fonte
│   ├── db.php          # Conexão com banco
│   └── database.sql    # Estrutura do banco
├── vendor/             # Dependências (gerado pelo Composer)
├── .env                # Configurações (criar baseado no .env.example)
└── composer.json       # Configuração do Composer
```

## Configuração de E-mail

1. Configure o SMTP nas configurações do usuário
2. Use as credenciais do seu servidor de e-mail

## Segurança

- Mantenha o `.env` fora do acesso público
- Configure corretamente as permissões dos diretórios
- Use HTTPS em produção
- Mantenha o PHP e as dependências atualizadas

## Backup

1. Banco de dados:
```bash
mysqldump -u usuario -p nome_do_banco > backup.sql
```

2. Arquivos:
- Faça backup da pasta `public/assinaturas/`
- Faça backup do arquivo `.env`

## Atualização

1. Faça backup do banco e arquivos
2. Atualize o código:
```bash
git pull
composer update
```
3. Execute as migrações do banco se houver
4. Verifique as configurações no `.env`

## Suporte

Em caso de problemas:
1. Verifique os logs do PHP
2. Verifique os logs do servidor web
3. Verifique as permissões dos diretórios
4. Verifique a conexão com o banco de dados 