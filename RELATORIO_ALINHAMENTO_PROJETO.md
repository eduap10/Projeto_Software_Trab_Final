
# Relatório de Alinhamento — Projeto **Vortex - Gaming Task** (Laravel + PHP + Bootstrap + PostgreSQL)

> Objetivo: Validar e garantir que o módulo entregue esteja **alinhado ao enunciado** (UI/UX, UML/ER, Login, CRUD, SOLID, Design Patterns, Arquitetura MVC em Laravel e PostgreSQL), além de apontar **ajustes mínimos** para cumprir integralmente os requisitos.

---

## 1) Visão Geral do Módulo

- **Stack**: Laravel 12 (PHP 8.2), Bootstrap 5 (via CDN), PostgreSQL.
- **Arquitetura**: MVC do Laravel.
- **Módulo**: Gestão de tarefas gamificada (login obrigatório + CRUD de tarefas; progressão de nível e pontos prevista).
- **Pastas-chave**:
  - `app/Http/Controllers/` → Controllers MVC (ex.: `TaskController.php`, `Auth/*`, `DashboardController.php`)
  - `app/Models/` → Modelos Eloquent (`Task`, `User`, `Achievement`, `UserAchievement`)
  - `app/Services/` → Serviços (SRP/DIP): `TaskService.php`, `AchievementService.php`, `GameService.php`
  - `app/Factories/TaskFactory.php` → Factory **custom** para criar `Task` (precisa ser **conectada** ao fluxo — ver Seção 6)
  - `app/Observers/TaskObserver.php` → Observer **custom** para reagir a eventos de `Task` (precisa ser **registrado** — ver Seção 6)
  - `resources/views/` → Telas Blade com **Bootstrap** (ex.: `layouts/app.blade.php`, `tasks/*.blade.php`, `auth/*.blade.php`, `welcome.blade.php`)
  - `routes/web.php` → Rotas com **auth + rotas protegidas** e **CRUD de tarefas**
  - `database/migrations/` → Tabelas `users`, `tasks`, `achievements`, `user_achievements` etc.
  - `assents/ER.png` e `assents/Diagram de Classes.png` → **UML** (Classes) e **Diagrama ER**

---

## 2) Como rodar (Passo a passo)

1. **Pré-requisitos**: PHP 8.2+, Composer, PostgreSQL 14+.
2. Clonar/extrair o projeto e entrar no diretório raíz (onde há o `composer.json`).
3. Copiar `.env.example` para `.env` (já existe `.env` de exemplo no pacote).
4. Ajustar credenciais no `.env` (o projeto já vem com PostgreSQL ativado):
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=task_gamified_db
   DB_USERNAME=postgres
   DB_PASSWORD=masterkey
   ```
5. Instalar dependências (se necessário): `composer install`
6. Gerar key (se necessário): `php artisan key:generate`
7. Migrar banco: `php artisan migrate`
8. (Opcional) Popular usuário de teste: editar `database/seeders/DatabaseSeeder.php` e rodar `php artisan db:seed`
9. Subir o servidor: `php artisan serve` → acessar `http://localhost:8000`

> **Observação**: As telas usam **Bootstrap via CDN**; portanto, não é obrigatório rodar build de assets para testar.

---

## 3) Requisitos Obrigatórios — **Validação**

### 3.1 UI/UX (Ergonomia)
- **Presente**: Layout com Bootstrap; formulários com labels; barra de progresso; feedback por flash messages.
- **Sugestões rápidas** (não obrigatórias, mas melhoram usabilidade/acessibilidade):
  - Incluir `aria-live="polite"` na área de alerts de sucesso/erro para leitores de tela.
  - Garantir `aria-label` em ícones e botões com ícones.
  - Exibir mensagens de validação `@error` logo abaixo dos inputs.
  - Nos modais/ações rápidas, focar input inicial ao abrir (para fluxo ágil).

### 3.2 Modelagem UML/ER
- **Presente**: `assents/Diagram de Classes.png` (Classes) e `assents/ER.png` (ER).  
  Recomenda-se **linkar**/referenciar essas imagens no relatório (Seção 7) para evidência formal.

### 3.3 Login + CRUD
- **Login/Registro/Logout**: `app/Http/Controllers/Auth/*`, `routes/web.php` com rotas e middleware `auth`; views em `resources/views/auth`.
- **CRUD de Tarefas**: `TaskController` com `index/create/store/show/edit/update/destroy` + views correspondentes.

### 3.4 PostgreSQL
- **Presente**: `.env` já configurado com `DB_CONNECTION=pgsql`. `config/database.php` dá suporte nativo a Postgres.

---

## 4) Princípios **SOLID** — Evidência

1. **SRP (Single Responsibility Principle)**  
   - `app/Services/*` separam regras de negócio do controller/eloquent. Ex.: `TaskService`, `AchievementService`, `GameService`.
2. **DIP (Dependency Inversion Principle)**  
   - **Injeção de dependência** por construtor em controllers/serviços (ex.: `TaskController` recebe `TaskService`; `GameService` recebe `AchievementService`).
3. **(Recomendado) ISP/OCP**  
   - Para consolidar o terceiro princípio, **sugerimos** criar uma **interface** para a criação de tarefas (`TaskFactoryInterface`) e **injetá-la** (em vez de acoplar na classe concreta). Isso reforça **DIP** + **ISP** e facilita a extensão (**OCP**) ao trocar a fábrica sem alterar consumidores.

> A Seção 6 traz *patches* prontos para isso, com mínimo impacto no código.

---

## 5) **Design Patterns** — Evidência e Ajustes

1. **Factory** *(custom)* — `app/Factories/TaskFactory.php`  
   - **Ajuste necessário**: hoje a Factory existe, mas não é utilizada no fluxo. Conectaremos no `TaskService` (Seção 6).
2. **Observer** *(custom)* — `app/Observers/TaskObserver.php`  
   - **Ajuste necessário**: registrar o Observer e implementar `updated()` para premiar XP e conquistas quando a tarefa virar **completed**.
3. **Singleton** *(via Service Container)*  
   - O Laravel já usa *singletons* internamente (ex.: gerenciador de conexões do DB). Para evidenciar explicitamente no **seu código**, vamos registrar `GameService` como **singleton** (Seção 6) e justificar no relatório.

---

## 6) **Ajustes mínimos** (patches prontos)

> Aplique os *snippets* abaixo e você cumpre integralmente SOLID + Patterns.

### 6.1 Registrar Observer e consolidar gamificação
**Arquivo**: `app/Providers/AppServiceProvider.php`
```php
use App\Models\Task;
use App\Observers\TaskObserver;
use App\Services\AchievementService;
use App\Services\GameService;
use App\Contracts\TaskFactoryInterface;
use App\Factories\TaskFactory;

public function register(): void
{
    // Singleton explícito (Pattern Singleton)
    $this->app->singleton(GameService::class, function ($app) {
        return new GameService($app->make(AchievementService::class));
    });

    // DIP + ISP: interface → implementação (Factory Pattern)
    $this->app->bind(TaskFactoryInterface::class, TaskFactory::class);
}

public function boot(): void
{
    // Observer Pattern: reagir a eventos do modelo Task
    Task::observe(TaskObserver::class);
}
```

**Arquivo**: `app/Observers/TaskObserver.php`
```php
namespace App\Observers;

use App\Models\Task;
use App\Services\AchievementService;

class TaskObserver
{
    public function updated(Task $task): void
    {
        // Quando concluir, dar XP e verificar conquistas
        if ($task->wasChanged('status') && $task->status === 'completed') {
            $user = $task->user;
            if ($user) {
                $user->addExperience($task->points); // soma XP
                app(AchievementService::class)->checkTaskAchievements($user); // verifica conquistas
            }
        }
    }
}
```

### 6.2 Factory **de fato no fluxo** + DIP/ISP

**Criar** a interface: `app/Contracts/TaskFactoryInterface.php`
```php
<?php
namespace App\Contracts;

use App\Models\Task;

interface TaskFactoryInterface
{
    public function create(array $data, int $userId): Task;
}
```

**Alterar** a factory concreta para **implementar** a interface: `app/Factories/TaskFactory.php`
```php
use App\Contracts\TaskFactoryInterface;

class TaskFactory implements TaskFactoryInterface
{
    // conteúdo atual permanece (método create etc.)
}
```

**Injetar a interface** no serviço e **usar a Factory**: `app/Services/TaskService.php`
```php
use App\Contracts\TaskFactoryInterface;
use App\Models\Task;

class TaskService
{
    public function __construct(private TaskFactoryInterface $factory) {}

    public function getUserTasks(int $userId)
    {
        return Task::where('user_id', $userId)->get();
    }

    public function createForUser(array $data, int $userId): Task
    {
        // Factory Pattern aplicado de fato
        return $this->factory->create($data, $userId);
    }

    public function complete(Task $task): Task
    {
        $task->status = 'completed';
        $task->save();
        return $task;
    }
}
```

**Usar o serviço no controller** (já está): `TaskController` chama `TaskService` por DI.  
Se quiser criar tarefa passando pela Factory via Service:
```php
// Dentro de store():
$task = $this->taskService->createForUser($validated, Auth::id());
```

### 6.3 **Status**: corrigir divergência Enum × Validação/View
Atualmente:
- **Migration** de `tasks` permite: `['pending','in_progress','completed']`
- **Controller + View** aceitam também `canceled` e `paused`

Escolha **uma** das abordagens:

**Opção A — Manter 3 status (mais simples)**  
- **Ajustar validação** em `TaskController@store`/`@update` para `in:pending,in_progress,completed`
- **Remover opções** `canceled` e `paused` do `<select>` nas views

**Opção B — Expandir a coluna para 5 status**  
> Em Postgres, para alterar `enum` via `change()` é comum instalar `doctrine/dbal` em dev:
```
composer require doctrine/dbal --dev
```
Criar migration de alteração:
```php
Schema::table('tasks', function (Blueprint $table) {
    $table->enum('status', ['pending','in_progress','completed','canceled','paused'])
          ->default('pending')
          ->change();
});
```
Reaplicar migration (em dev, preferível recriar tabela).

> **Recomendação**: Opção A é imediata e atende ao escopo. Se quiser os 5 status, siga a Opção B.

---

## 7) **Relatório/Explicação** (para anexar ao trabalho)

### 7.1 Por que **SOLID** aqui?
- **SRP**: Serviços isolam regras de negócio; controllers apenas orquestram requisições/respostas.
- **DIP**: Construtores recebem **abstrações** (interfaces/classes injetáveis), desacoplando uso da implementação concreta.
- **ISP/OCP** (via Factory Interface): Consumidores dependem de uma **interface** simples (`TaskFactoryInterface`); trocar a fábrica (ex.: outra política de pontos) não afeta quem usa.

### 7.2 Por que estes **Design Patterns**?
- **Factory**: centraliza e padroniza a criação de `Task` (cálculo de `points` por prioridade, defaults), reduzindo duplicação e erros.
- **Observer**: reação automática a eventos do domínio (ex.: ao concluir uma tarefa, creditar XP e checar conquistas). Mantém o código **coeso** e **desacoplado**.
- **Singleton**: registrar `GameService` como singleton garante **instância única** (útil para caches internos/estatísticas) e também **demonstra** o padrão no seu código, além dos singletons nativos do framework (ex.: gerenciador de DB).

### 7.3 Telas, Usabilidade e Acessibilidade
- Telas **simples e claras** (Bootstrap). Feedback visual por progress bars e alerts.
- Propostas: mensagens `@error`, `aria-live` em flash, foco inicial no primeiro campo do modal, tooltips breves nos ícones.

### 7.4 UML e ER
- Anexos inclusos: `assents/Diagram de Classes.png` e `assents/ER.png`.  
  **Dica**: cite-os no PDF/relatório final e explique os relacionamentos e cardinalidades em 1–2 parágrafos.

---

## 8) Checklist final de conformidade

- [x] **Login** funcional + rotas protegidas.
- [x] **CRUD** de tarefas completo.
- [x] **PostgreSQL** configurado no `.env`.
- [x] **UI/UX** com Bootstrap; usabilidade básica válida.
- [x] **UML/ER** presentes no repositório.
- [x] **SOLID**: SRP + DIP **presentes**; **ISP/OCP** implementados via interface de Factory (**aplicar patch 6.2**).
- [x] **Design Patterns**: Factory (após conectar), Observer (após registrar + updated), Singleton (registrar `GameService`).
- [x] **Divergência de Status**: resolvida (Opção A **ou** B).

---

## 9) Próximos Passos (opcional)
- Exibir **ranking** e **conquistas** na dashboard consumindo `GameService`/`AchievementService`.
- Testes de unidade básicos (ex.: criação de tarefa via Factory; observer incrementando XP).
- Documentar no README os comandos principais e anexar este relatório.

---

> **Conclusão**: Com os **patches curtos** da Seção 6, o projeto cumpre **integralmente** os itens do enunciado (SOLID + 2 Patterns + arquitetura e requisitos). A estrutura está limpa e didática, pronta para apresentação e avaliação.
