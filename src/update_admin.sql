-- Atualizar usuário administrador
UPDATE usuarios 
SET senha = '$2y$10$abcdefghijklmnopqrstuu' 
WHERE email = 'admin@admin.com';

-- Se o usuário não existir, criar
INSERT INTO usuarios (nome, email, senha, cargo, nivel_acesso_id)
SELECT 'Administrador', 'admin@admin.com', '$2y$10$abcdefghijklmnopqrstuu', 'Administrador', 1
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'admin@admin.com'); 