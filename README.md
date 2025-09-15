# 🎮 Sistema de Tarefas Gamificado — Projeto de Software

Repositório contendo meu **Projeto Final da disciplina de Projeto de Software**.  
Módulo funcional desenvolvido em **Laravel 12 + PHP 8.2 + PostgreSQL + Bootstrap**.

---

## 🚀 Objetivo
Demonstrar a aplicação prática de fundamentos de engenharia de software:
- **Interface intuitiva (UI/UX)** com protótipo e implementação
- **Modelagem UML e ER**
- Aplicação de **princípios SOLID**
- Implementação de **padrões de projeto (Factory, Observer, Singleton)**
- Login e **CRUD de tarefas** persistidos em PostgreSQL

---

## 🖼️ Protótipo
O protótipo serviu de guia para a interface, com **login, dashboard com progresso e CRUD de tarefas**.  
https://www.figma.com/proto/d66aYwAsxwUVJz3cLbCFut/Smart-Home-App--Community-?node-id=4-117&p=f&t=07DijlzNb3g33IEV-1&scaling=min-zoom&content-scaling=fixed&page-id=0%3A1&starting-point-node-id=134%3A8&show-proto-sidebar=1

---

## 📊 Modelagem
- **Diagrama de Classes:** `assents/Diagram de Classes.png`  
- **Diagrama ER:** `assents/ER.png`

---

## 🔐 Funcionalidades
- Autenticação (login/registro/logout)
- Dashboard com **nível** e **experiência (XP)**
- CRUD de Tarefas:
  - Criar
  - Listar
  - Editar
  - Excluir
- Barra de progresso e contadores por status (`pending`, `in_progress`, `completed`)

---

## 🧩 Arquitetura & Padrões

### 🔹 Princípios SOLID
- **SRP** — Services isolam a regra de negócio (`TaskService`, `GameService`)  
- **DIP** — `TaskService` depende de `TaskFactoryInterface` (não da classe concreta)  
- **ISP/OCP** — `TaskFactoryInterface` permite extender sem modificar consumidores  

### 🔹 Design Patterns
- **Factory** — `app/Factories/TaskFactory.php` cria `Task` com lógica de pontos  
- **Observer** — `app/Observers/TaskObserver.php` soma XP e checa conquistas ao concluir tarefas  
- **Singleton** — `GameService` registrado como singleton em `AppServiceProvider`  

---

## ⚙️ Como rodar localmente

**Pré-requisitos:** PHP 8.2+, Composer, PostgreSQL

```bash
# Clonar o repositório
git clone https://github.com/eduap10/Projeto_Software_Trab_Final.git
cd Projeto_Software_Trab_Final

# Instalar dependências
composer install

# Copiar .env e configurar banco (PostgreSQL)
cp .env.example .env
# edite DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Gerar key
php artisan key:generate

# Migrar tabelas
php artisan migrate

# Subir servidor
php artisan serve
