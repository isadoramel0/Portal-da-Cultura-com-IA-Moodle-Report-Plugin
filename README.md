# 🎭 Portal da Cultura com IA — Moodle Report Plugin

Plugin de relatório para o Moodle que integra Google Gemini para exibir dados culturais em tabelas interativas, com sistema de cache nativo do Moodle.

---

## ✨ Funcionalidades

- 5 prompts culturais pré-preparados com um clique
- Integração com a API do Google Gemini (modelo `gemini-2.5-flash`)
- Resposta em JSON estruturado renderizada em tabela
- Indicação de erro caso a IA não consiga retornar os dados
- Sistema de cache nativo do Moodle (MUC) com duração de 1 hora, evitando chamadas desnecessárias à API
- Chave da API configurável pela área de administração do plugin
- Indicação visual se o resultado veio da IA ou do cache

---

## 🎨 Temas disponíveis

| Tema | Dados exibidos |
|---|---|
| 🎬 Filmes mais premiados no Oscar | Filme, ano, nº de Oscars, categoria principal |
| 📚 Livros mais vendidos da história | Livro, autor, ano de publicação, cópias vendidas |
| 🎵 Músicas mais tocadas no Spotify | Música, artista, ano de lançamento, reproduções |
| 💿 Álbuns mais vendidos da história | Álbum, artista, ano de lançamento, cópias vendidas |
| 🎨 Obras de arte mais famosas | Obra, artista, ano, museu ou localização |

---

## ⚙️ Tecnologias utilizadas

- **Moodle 4.1** — plataforma de ensino
- **PHP 8.1** — linguagem do backend
- **Google Gemini API** (`gemini-2.5-flash`) — IA generativa
- **MUC (Moodle Universal Cache)** — sistema de cache nativo
- **Apache2** — servidor web
- **MySQL** — banco de dados
- **Ubuntu (WSL)** — ambiente de desenvolvimento

---

## 🚀 Como instalar

1. Clone o repositório dentro da pasta de relatórios do Moodle:

```bash
git clone https://github.com/isadoramel0/moodle-iareport.git /var/www/html/moodle/report/iareport
```

2. Ajuste as permissões:

```bash
sudo chown -R www-data:www-data /var/www/html/moodle/report/iareport
```

3. Acesse o Moodle como administrador — o sistema detectará o novo plugin automaticamente e solicitará a instalação.

4. Após instalar, configure a chave da API em:

**Administração do site > Relatórios > 🎭 Portal da Cultura com IA**

---

## 🔑 Obtendo a chave da API do Gemini

Acesse [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey), faça login com sua conta Google e crie uma nova chave de API gratuitamente.

---

## 📁 Estrutura do plugin

```
report/iareport/
├── index.php                  # Página principal do relatório
├── settings.php               # Configurações do plugin (chave da API)
├── version.php                # Versão e metadados do plugin
├── .gitignore
├── db/
│   └── caches.php             # Definição do cache MUC
└── lang/
    ├── en/
    │   └── report_iareport.php
    └── pt_br/
        └── report_iareport.php
```

---
