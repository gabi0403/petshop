-- Limpa os usuários de teste antigos
DELETE FROM usuarios;

-- Insere os usuários com a criptografia gerada perfeitamente para a senha: 123456
INSERT INTO usuarios (nome, email, senha, telefone, tipo, cargo) VALUES
('Dr. Roberto Garcia', 'roberto@pethealth.com', '$2y$10$qRvyb7N87A1YvA0Kylv7eO1C9Xf0xshxR0Tz8q6eRGe3Iu6gBkW1G', '11999999991', 'equipe', 'Veterinário'),
('Camila Silva Tosadora', 'camila@pethealth.com', '$2y$10$qRvyb7N87A1YvA0Kylv7eO1C9Xf0xshxR0Tz8q6eRGe3Iu6gBkW1G', '11999999992', 'equipe', 'Tosador'),
('Carlos Tutor', 'cliente@gmail.com', '$2y$10$qRvyb7N87A1YvA0Kylv7eO1C9Xf0xshxR0Tz8q6eRGe3Iu6gBkW1G', '11988888888', 'cliente', 'Cliente');