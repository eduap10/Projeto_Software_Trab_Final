# 🎮 Sistema de Tarefas Gamificado — Projeto de Software

Repositório contendo meu **Projeto Final da disciplina de Projeto de Software**.  
Módulo funcional desenvolvido em **Laravel 12 + PHP 8.2 + PostgreSQL + Bootstrap**.

---

## 🚀 Objetivo
Este projeto teve como objetivo **desenvolver um módulo funcional de sistema realista**, aplicando os conceitos de **engenharia de software** estudados ao longo da disciplina.  
O foco foi demonstrar a aplicação prática de **UI/UX, UML/ER, princípios SOLID e padrões de projeto** em uma solução coesa.

---

## 🖼️ Protótipo
O protótipo inicial guiou a implementação da interface, contendo tela de **login**, **dashboard com progresso** e **CRUD de tarefas**.  
👉 [Acesse o protótipo no Figma](https://www.figma.com/proto/d66aYwAsxwUVJz3cLbCFut/Smart-Home-App--Community-?node-id=134-8&p=f&t=5ru7EXsgqixZ405l-1&scaling=min-zoom&content-scaling=fixed&page-id=0%3A1&starting-point-node-id=134%3A8&show-proto-sidebar=1)

---

## 📊 Modelagem

### 🔹 Diagrama de Classes
Representa entidades como **User**, **Task** e **Achievement**, destacando relacionamentos e responsabilidades.  
📌 Arquivo: `assents/Diagram de Classes.png`

### 🔹 Diagrama ER
Modelagem da base de dados PostgreSQL, estruturando tabelas e relacionamentos.  
📌 Arquivo: `assents/ER.png`

---

## 🔐 Funcionalidades Implementadas
- **Autenticação**: login, registro e logout de usuários.
- **Dashboard**:
  - Barra de progresso do usuário.
  - Contadores de tarefas por status.
  - Exibição de nível e pontos de experiência (XP).
- **CRUD de Tarefas**:
  - Criar, listar, editar e excluir tarefas.
  - Status disponíveis: `pending`, `in_progress`, `completed`.
- **Gamificação**:
  - Usuários ganham XP ao concluir tarefas.
  - Sistema de conquistas desbloqueado via Observer.

---

## 🧩 Arquitetura & Padrões

### 🔹 Princípios SOLID
- **SRP (Responsabilidade Única)** — Classes de serviço isolam a regra de negócio (`TaskService`, `GameService`).
- **DIP (Inversão de Dependência)** — `TaskService` depende da abstração `TaskFactoryInterface`, não da implementação concreta.
- **ISP/OCP (Segregação de Interfaces & Aberto/Fechado)** — A `TaskFactoryInterface` permite evolução sem modificar consumidores.

### 🔹 Design Patterns
- **Factory**  
  Implementado em `app/Factories/TaskFactory.php`, responsável por criar `Task` com lógica de pontos.  
- **Observer**  
  Em `app/Observers/TaskObserver.php`, monitora mudanças no status das tarefas e concede XP/conquistas ao usuário.  
- **Singleton**  
  `GameService` é registrado como singleton em `AppServiceProvider`, garantindo apenas uma instância em toda a aplicação.

---

## 🎥 Vídeo de Apresentação
Assista à apresentação completa do sistema**.  
👉 [📺 YouTube - Projeto Final](https://youtu.be/m90zL2kGAJI)

---

## 📂 Estrutura do Projeto
  - app/Contracts → Interfaces (ex.: TaskFactoryInterface)

  - app/Factories → Implementações de Factory Pattern

  - app/Observers → Observadores de eventos (Observer Pattern)

  - app/Services → Regras de negócio (TaskService, GameService)

  - database/migrations → Estrutura do banco (PostgreSQL)

  - resources/views → Templates Blade (UI em Bootstrap)

---

## ⚙️ Como Rodar o Projeto

### 🔧 Pré-requisitos
- PHP 8.2+
- Composer
- PostgreSQL

---

## ✅ Checklist de Entrega
**📌 O que o professor/banca vai avaliar:

- Protótipo de interface → link Figma incluso

- Modelagem UML e ER → arquivos em assents/

- Aplicação de pelo menos 3 princípios SOLID

- Implementação de 2 padrões de projeto (Factory, Observer, Singleton)

- Arquitetura organizada em MVC com Laravel

- Funcionalidade mínima: login + CRUD de tarefas

- Vídeo de apresentação (≤ 5 minutos) → link incluso no README

- Código publicado no GitHub público

---

### ▶️ Passos de instalação
```bash
# Clonar o repositório
git clone https://github.com/eduap10/Projeto_Software_Trab_Final.git
cd Projeto_Software_Trab_Final

# Instalar dependências
composer install

# Configurar .env
cp .env.example .env
# edite DB_DATABASE, DB_USERNAME, DB_PASSWORD para suas credenciais

# Gerar key
php artisan key:generate

# Rodar migrations
php artisan migrate

# Subir servidor local
php artisan serve

---

##👤 Autor
**Eduardo (eduap10)
**Projeto Final — Disciplina de Projeto de Software (2025)
