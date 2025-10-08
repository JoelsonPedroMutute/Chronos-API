<p align="center"><a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# Chronos API

Chronos API é uma solução backend para **gestão de assiduidade** e **controle de ponto eletrônico** de funcionários.  
O objetivo é oferecer uma API simples, segura e escalável para empresas de todos os portes — desde pequenas até grandes organizações.

---

## ✨ Funcionalidades Principais
- Registro de **entradas e saídas (punches)** de funcionários.
- Suporte a diferentes **papéis de usuário**: Super Admin, Admin, Manager e Employee.
- Configuração flexível por empresa, incluindo:
  - Regras de fechamento automático de turnos (*auto-close*).
  - Definição de limites e categorias de colaboradores.
- Suporte a **hora extra** com rastreabilidade clara.
- Integração preparada para **folha de pagamento** e relatórios.
- **Assinatura por funcionário**, garantindo escalabilidade e modelo de negócio sustentável.
- Registro de **event logs** para auditoria.

---

## ⚙️ Tecnologias Utilizadas
- **Backend:** Laravel 12.x  
- **Banco de Dados:** PostgreSQL 17  
- **Autenticação:** Laravel Sanctum  
- **Documentação:** Swagger (l5-swagger)  
- **Containerização:** Docker e Docker Compose  
- **Ambiente local (alternativo):** WAMP Server ou Laravel Herd  
- **Testes de Endpoints:** Postman / Insomnia  

---

## 📊 Diferenciais
- **Configurações específicas por empresa** (sem soluções genéricas que ignoram particularidades legais).  
- **Escalabilidade desde a V1** — preparada para evoluir sem reescrever tudo.  
- **API-first** — fácil integração com qualquer frontend ou sistema de RH.  
- **Customização de branding** (cores, logo, relatórios).  

---

## 📍 Público-Alvo
A Chronos API está inicialmente voltada para o mercado angolano, mas projetada para se adaptar a outros contextos legais e empresariais no futuro.

---

## 🚀 Requisitos

### 🔹 Para rodar com Docker
- Docker Desktop  
- Docker Compose  

### 🔹 Para instalação local
- PHP >= 8.3  
- Composer  
- PostgreSQL 17 (com extensões `pdo_pgsql`, `mbstring`)  
- Git  
- WAMP / Laravel Herd (opcional)  
- Arquivo `.env` configurado corretamente  

---

## 🛠️ Instalação e Execução

### 🔹 Usando Docker
1. Clone o repositório:
   ```bash
   git clone https://github.com/JoelsonPedroMutute/Chronos.git
   cd Chronos
    
## Configure o seu ambiente
cp .env.example .env
php artisan key:generate

## Suba os containers:
docker-compose up -

## Instale as dependências:
docker-compose exec app composer install

## Rode as migrations e seeders:
docker-compose exec app php artisan migrate --seed

## Gere a chave da aplicação:
docker-compose exec app php artisan key:generate

## Acesse a API
http://localhost:8000

## Documentação Swagger:
http://localhost:8000/api/documentation

    ```` Instalação Local (sem Docker)

Clone o repositório e copie o .env.

Configure conexão com PostgreSQL 17.

Instale dependências:

```bash 
composer install

## Rode as migrations e seeders
php artisan migrate --seed

## Gere a chave da aplicação:
php artisan key:generate

```inicie o servidor
php artisan serve

```A API esta disponivel em 
http://localhost:8000


🚧 **Status do Projeto:** Em desenvolvimento (V1 focada em autenticação(Sanctum), usuários, roles, funcionamiors, empresas e punches).
V2 (planejado): Relatórios, Integração com folha de pagamento, Alertas de atrasos, Dashboards.

## 🤝 Contribuindo:
Se você quiser contribuir para o projeto:

Faça um fork.

Crie uma branch (git checkout -b minha-feature).

Commit suas alterações (git commit -m 'Minha feature').

Push para o repositório (git push origin minha-feature).

Abra um Pull Request.