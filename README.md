# TODO Application

Nowoczesna aplikacja konsolowa do zarządzania zadaniami (TODO) zbudowana w **Symfony 7.3** jako praktyczna implementacja **Architektury Heksagonalnej** (Ports and Adapters) z elementami **CQRS**.

## O projekcie

Projekt został stworzony jako przykład czystej architektury w PHP 8.2+, demonstrując:
- ✅ Separację warstw (Domain, Application, Infrastructure, Presentation)
- ✅ Wzorce projektowe (Repository, Command/Handler, DTO)
- ✅ Dependency Injection i Inversion of Control
- ✅ SOLID principles
- ✅ Immutability i type safety (readonly classes)

**Przypadki użycia:**
- Tworzenie nowych zadań z tytułem i opisem
- Przeglądanie listy zadań w czytelnej tabeli
- Oznaczanie zadań jako ukończone z timestampem
- Usuwanie zadań z potwierdzeniem

## Wymagania

- PHP 8.2 lub wyższy
- Composer
- Rozszerzenie PDO SQLite

## Instalacja

1. Sklonuj repozytorium:
```bash
git clone <repository-url>
cd 2025_09_30_sf_hex_todo
```

2. Zainstaluj zależności:
```bash
composer install
```

3. Utwórz bazę danych:
```bash
php bin/console doctrine:database:create
```

4. Uruchom migracje:
```bash
php bin/console doctrine:migrations:migrate
```

## Użycie

Aplikacja posiada własną konsolę dostępną przez `bin/todo`:

### Wyświetlenie dostępnych komend

```bash
php bin/todo
```

### Tworzenie zadania

```bash
php bin/todo app:create-task
```

Komenda poprosi interaktywnie o podanie tytułu i opisu zadania.

### Wyświetlanie wszystkich zadań

```bash
php bin/todo app:get-tasks
```

Zadania wyświetlane są w formie tabeli z kolorowym statusem (✓ Completed / ○ Pending).

### Oznaczanie zadania jako ukończone

```bash
php bin/todo app:complete-task <task-id>
```

Przykład:
```bash
php bin/todo app:complete-task 67920c1ae4a971.23456789
```

### Usuwanie zadania

```bash
php bin/todo app:delete-task <task-id>
```

Komenda pyta o potwierdzenie przed usunięciem zadania.

Przykład:
```bash
php bin/todo app:delete-task 67920c1ae4a971.23456789
```

## Architektura

Projekt implementuje **Architekturę Heksagonalną** (Ports and Adapters), która izoluje logikę biznesową od szczegółów technicznych.

### Struktura warstw

```
src/
├── Domain/              # 🎯 Jądro aplikacji - logika biznesowa
│   ├── Model/           # Encje domenowe (Task z regułami biznesowymi)
│   └── Port/            # Interfejsy (porty) definiujące kontrakty
│
├── Application/         # 🎬 Przypadki użycia (Use Cases)
│   └── UseCase/         # Grupowane po feature (Screaming Architecture)
│       ├── CreateTask/      # Tworzenie zadania
│       ├── CompleteTask/    # Oznaczanie jako ukończone
│       ├── DeleteTask/      # Usuwanie zadania
│       └── GetAllTasks/     # Pobieranie listy zadań
│
├── Infrastructure/      # ⚙️ Implementacje techniczne (adaptery)
│   └── Persistence/     # Repozytoria Doctrine ORM + SQLite
│
└── Presentation/        # 🖥️ Interfejsy użytkownika
    └── Cli/             # Własna konsola Symfony
```

### Przepływ danych (Dependency Rule)

```
┌─────────────┐
│ Presentation│  (CLI Commands)
└──────┬──────┘
       │ wywołuje
       ▼
┌─────────────┐
│ Application │  (Handlers)
└──────┬──────┘
       │ używa
       ▼
┌─────────────┐
│   Domain    │  (Ports - interfejsy)
└──────┬──────┘
       │ implementuje
       ▼
┌─────────────┐
│Infrastructure│ (Adaptery - Doctrine)
└─────────────┘
```

**Kierunek zależności:** Presentation → Application → Domain ← Infrastructure

Domain **nie zależy** od żadnej warstwy (czysta logika biznesowa)!

### Zastosowane wzorce projektowe

#### 1. **Hexagonal Architecture (Ports and Adapters)**
- **Porty** (`TaskRepositoryInterface`) - definiują kontrakty
- **Adaptery** (`DoctrineTaskRepository`) - implementują porty
- Izolacja logiki biznesowej od frameworka

#### 2. **CQRS Light (Command Query Responsibility Segregation)**
- **Commands** - operacje zmieniające stan:
  - `UseCase/CreateTask/CreateTaskCommand` - tworzenie zadania
  - `UseCase/CompleteTask/CompleteTaskCommand` - oznaczanie jako ukończone
  - `UseCase/DeleteTask/DeleteTaskCommand` - usuwanie zadania
- **Queries** - operacje tylko do odczytu:
  - `UseCase/GetAllTasks/GetAllTasksQuery` - pobieranie listy zadań

Każdy Use Case jest grupowany w osobnym folderze z Command/Query + Handler.

#### 3. **Command/Handler Pattern**
```php
// Application/UseCase/CreateTask/CreateTaskCommand.php
namespace App\Application\UseCase\CreateTask;

final readonly class CreateTaskCommand {
    public function __construct(
        public string $title,
        public string $description,
    ) {}
}

// Application/UseCase/CreateTask/CreateTaskHandler.php
namespace App\Application\UseCase\CreateTask;

final readonly class CreateTaskHandler {
    public function handle(CreateTaskCommand $command): Task {
        $task = new Task($command->title, $command->description);
        $this->repository->save($task);
        return $task;
    }
}
```

#### 4. **Repository Pattern**
```php
// Port (interfejs w Domain)
interface TaskRepositoryInterface {
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function delete(Task $task): void;
}

// Adapter (implementacja w Infrastructure)
final readonly class DoctrineTaskRepository implements TaskRepositoryInterface {
    // Implementacja przez Doctrine ORM
}
```

#### 5. **DTO (Data Transfer Object)**
- Immutable DTOs (`final readonly`)
- Public properties zamiast getterów (prostota)
- Type safety przez PHP 8.2+ features

#### 6. **Dependency Injection**
- Constructor injection wszędzie
- Symfony Container zarządza zależnościami
- Konfiguracja przez `services.yaml`

### Kluczowe zasady

✅ **Single Responsibility Principle** - każda klasa ma jedną odpowiedzialność
✅ **Open/Closed Principle** - rozszerzalność przez interfejsy
✅ **Liskov Substitution** - zamienność implementacji portów
✅ **Interface Segregation** - minimalne interfejsy (tylko potrzebne metody)
✅ **Dependency Inversion** - zależność od abstrakcji, nie konkretnych klas

### Cechy techniczne

- **PHP 8.2+** - readonly classes, constructor property promotion
- **Symfony 7.3** - framework jako infrastruktura
- **Doctrine ORM 3.x** - mapowanie atrybutami w encjach
- **SQLite** - baza danych w jednym pliku
- **Immutability** - wszystkie klasy `readonly`
- **Type Safety** - strict types wszędzie

## Baza danych

Aplikacja używa **SQLite** jako lekką, plikową bazę danych - idealna do developmentu i małych projektów.

**Lokalizacja:** `var/data_dev.db`

**Schema zadania:**
| Kolumna | Typ | Opis |
|---------|-----|------|
| `id` | string | Unikalny identyfikator (uniqid) |
| `title` | string | Tytuł zadania (wymagane) |
| `description` | text | Opis zadania |
| `created_at` | datetime | Data utworzenia (automatyczna) |
| `completed_at` | datetime nullable | Data ukończenia (null = nieukończone) |

## Testowanie

Możesz przetestować aplikację:

```bash
# 1. Utwórz zadanie
php bin/todo app:create-task
# Wpisz: "Nauczyć się Hexagonal Architecture"
# Opis: "Studiowanie wzorców projektowych"

# 2. Zobacz listę
php bin/todo app:get-tasks

# 3. Oznacz jako ukończone (skopiuj ID z listy)
php bin/todo app:complete-task <task-id>

# 4. Zobacz ponownie listę (status zmieniony na ✓)
php bin/todo app:get-tasks

# 5. Usuń zadanie
php bin/todo app:delete-task <task-id>
```

## Dla developerów

### Dodawanie nowego przypadku użycia

1. **Stwórz folder** w `Application/UseCase/NowyFeature/`
2. **Stwórz Command/Query DTO** np. `NowyFeatureCommand.php`
3. **Stwórz Handler** np. `NowyFeatureHandler.php`
4. **Stwórz CLI Command** w `Presentation/Cli/Command`
5. **Zarejestruj komendę** w `bin/todo` i `config/services.yaml`

Przykład struktury:
```
Application/UseCase/NowyFeature/
├── NowyFeatureCommand.php  (DTO)
└── NowyFeatureHandler.php  (Use Case)
```

Zobacz implementację `DeleteTask` jako wzorzec.

### Rozszerzanie o API REST

Dzięki architekturze heksagonalnej możesz łatwo dodać HTTP API:

```php
// Presentation/Http/Controller/TaskController.php
use App\Task\Application\UseCase\CreateTask\CreateTaskCommand;

class TaskController {
    public function create(Request $request) {
        $command = new CreateTaskCommand(
            $request->get('title'),
            $request->get('description')
        );
        $task = $this->handler->handle($command);
        return new JsonResponse($task);
    }
}
```

**Ta sama logika** (Handler) działa dla CLI i HTTP! 🎉

## Szczegółowe wyjaśnienia kluczowych koncepcji

### 1. Separacja warstw - Dlaczego i jak?

#### Domain - Jądro biznesowe (nie zależy od niczego!)

```php
// src/Domain/Model/Task.php
class Task {
    private ?DateTimeImmutable $completedAt = null;

    public function complete(): void {
        if ($this->completedAt !== null) {
            throw new DomainException('Task is already completed');
        }
        $this->completedAt = new DateTimeImmutable();
    }
}
```

**Reguła biznesowa**: "Nie można oznaczyć zadania jako ukończone dwa razy"
- ✅ Czysty PHP bez zależności zewnętrznych
- ✅ Można przenieść do innego frameworka bez zmian
- ✅ Łatwe do przetestowania (unit testy)

#### Application - Orkiestracja (koordynuje przepływ)

```php
// src/Application/UseCase/CompleteTask/CompleteTaskHandler.php
final readonly class CompleteTaskHandler {
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function handle(CompleteTaskCommand $command): Task {
        $task = $this->taskRepository->findById($command->taskId);
        if ($task === null) {
            throw new InvalidArgumentException('Task not found');
        }
        $task->complete(); // ← Wywołuje regułę biznesową z Domain
        $this->taskRepository->save($task);
        return $task;
    }
}
```

**Odpowiedzialność**: Pobierz → Wykonaj logikę → Zapisz
- ✅ Nie wie JAK dane są zapisywane (SQLite? PostgreSQL? Redis?)
- ✅ Zależy tylko od abstrakcji (`TaskRepositoryInterface`)
- ✅ Łatwe do testowania (mock repozytorium)

#### Infrastructure - Szczegóły techniczne (wymienialny adapter)

```php
// src/Infrastructure/Persistence/Doctrine/DoctrineTaskRepository.php
final readonly class DoctrineTaskRepository implements TaskRepositoryInterface {
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Task $task): void {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }
}
```

**Możesz stworzyć alternatywną implementację**:
```php
// src/Infrastructure/Persistence/Redis/RedisTaskRepository.php
final readonly class RedisTaskRepository implements TaskRepositoryInterface {
    public function save(Task $task): void {
        $this->redis->set("task:{$task->getId()}", serialize($task));
    }
}
```

Zmiana w `services.yaml` i gotowe - **zero zmian w logice biznesowej**!

#### Presentation - Interfejs użytkownika (punkt wejścia)

```php
// src/Presentation/Cli/Command/CompleteTaskCommand.php
protected function execute(InputInterface $input, OutputInterface $output): int {
    $command = new CompleteTaskApplicationCommand($taskId);
    $task = $this->handler->handle($command);
    $io->success(sprintf('Task "%s" marked as completed!', $task->getTitle()));
    return Command::SUCCESS;
}
```

**Odpowiedzialność**: Zbierz input → Wywołaj handler → Sformatuj output
- ✅ Nie wie o bazie danych
- ✅ Można dodać HTTP API z tą samą logiką

**Przepływ w praktyce**:
```
1. CLI Command otrzymuje input użytkownika (ID zadania)
2. Tworzy CreateTaskCommand (DTO) z danymi
3. Wywołuje CreateTaskHandler z Application
4. Handler pobiera Task z repozytorium (przez interfejs)
5. Wywołuje task.complete() - reguła biznesowa
6. Zapisuje przez repozytorium
7. CLI formatuje odpowiedź i wyświetla sukces
```

---

### 2. Wzorce projektowe w akcji

#### Repository Pattern - Ukrycie źródła danych

**Problem**: Handler nie powinien wiedzieć, skąd pochodzą dane.

```php
// ❌ Źle - bezpośrednie użycie Doctrine
class CompleteTaskHandler {
    public function __construct(private EntityManagerInterface $em) {}

    public function handle($command) {
        $task = $this->em->getRepository(Task::class)->find($command->taskId);
        // Handler wie o Doctrine!
    }
}

// ✅ Dobrze - przez interfejs
interface TaskRepositoryInterface {
    public function findById(string $id): ?Task;
    public function save(Task $task): void;
}

class CompleteTaskHandler {
    public function __construct(private TaskRepositoryInterface $repo) {}

    public function handle($command) {
        $task = $this->repo->findById($command->taskId);
        // Handler nie wie, co kryje się za interfejsem!
    }
}
```

**Korzyść**: Możesz podmienić implementację bez zmiany handlera:
- Doctrine ORM → PDO
- SQLite → PostgreSQL → MongoDB
- Prawdziwa baza → Mock w testach

#### Command/Handler Pattern - Type-safe komunikacja

**Problem**: Jak przekazać dane do use case w bezpieczny sposób?

```php
// ❌ Array - brak bezpieczeństwa typów
$handler->handle(['title' => 'Foo', 'description' => 'Bar']);
// Co jeśli pomylę klucz? "tittle"? IDE nie pomoże!

// ✅ Command DTO - pełna kontrola typów
final readonly class CreateTaskCommand {
    public function __construct(
        public string $title,
        public string $description,
    ) {}
}

$command = new CreateTaskCommand('Foo', 'Bar');
$handler->handle($command);
// IDE podpowiada pola, PHP weryfikuje typy!
```

**Korzyść**:
- Autocompletition w IDE
- Błędy wykrywane przed uruchomieniem
- Samodokumentujący kod

#### DTO (Data Transfer Object) - Immutable dane

```php
// ❌ Mutowalny obiekt
class CreateTaskCommand {
    public string $title;
}

$command = new CreateTaskCommand();
$command->title = 'Foo';
someFunction($command); // Może zmienić title!
echo $command->title; // Co teraz zawiera?

// ✅ Readonly - niemutowalny
final readonly class CreateTaskCommand {
    public function __construct(
        public string $title,
        public string $description,
    ) {}
}

$command = new CreateTaskCommand('Foo', 'Bar');
$command->title = 'Changed'; // BŁĄD KOMPILACJI!
```

**Korzyść**: Brak niespodzianek, wartości stałe przez cały cykl życia.

---

### 3. Dependency Injection & Inversion of Control

#### Problem: Tight coupling (ścisłe powiązanie)

```php
// ❌ Handler tworzy zależność
class CompleteTaskHandler {
    private DoctrineTaskRepository $repo;

    public function __construct() {
        $this->repo = new DoctrineTaskRepository();
        // Handler WIE o Doctrine!
        // Nie można podmienić implementacji
        // Trudne testowanie
    }
}
```

#### Rozwiązanie: Dependency Injection

```php
// ✅ Wstrzykiwanie przez konstruktor
final readonly class CompleteTaskHandler {
    public function __construct(
        private TaskRepositoryInterface $repo // ← Wstrzyknięte!
    ) {}
}
```

**Symfony robi to automatycznie** dzięki `services.yaml`:
```yaml
App\Domain\Port\TaskRepositoryInterface:
    class: App\Infrastructure\Persistence\Doctrine\DoctrineTaskRepository
```

**Korzyści**:
- Handler nie wie, co dostaje (Doctrine? Redis? Mock?)
- Łatwa podmiana implementacji
- Testy jednostkowe z mock'ami

#### Inversion of Control - Odwrócenie zależności

**Tradycyjnie**: Moduł wysokopoziomowy zależy od niskopoziomowego
```
Handler (wysokopoziomowy)
   ↓ zależy od
DoctrineRepository (niskopoziomowy)
```

**IoC**: Oba zależą od abstrakcji
```
Handler → TaskRepositoryInterface ← DoctrineRepository
        (oba zależą od interfejsu)
```

**Dlaczego to ważne?**
```php
// Możesz mieć wiele implementacji tego samego interfejsu
class DoctrineTaskRepository implements TaskRepositoryInterface {}
class RedisTaskRepository implements TaskRepositoryInterface {}
class InMemoryTaskRepository implements TaskRepositoryInterface {} // dla testów

// Handler działa z każdą z nich!
```

---

### 4. SOLID Principles - Praktyczne przykłady

#### S - Single Responsibility (Jedna odpowiedzialność)

```php
// ❌ Klasa robi za dużo
class TaskManager {
    public function createTask() { /* ... */ }
    public function completeTask() { /* ... */ }
    public function deleteTask() { /* ... */ }
    public function sendEmailNotification() { /* ... */ }
    public function saveToDatabase() { /* ... */ }
    public function exportToPdf() { /* ... */ }
}

// ✅ Każda klasa jedna rzecz
class CreateTaskHandler { /* tylko tworzenie */ }
class CompleteTaskHandler { /* tylko oznaczanie */ }
class DeleteTaskHandler { /* tylko usuwanie */ }
class EmailNotificationService { /* tylko emaile */ }
class TaskRepositoryInterface { /* tylko zapis/odczyt */ }
```

**Korzyść**: Łatwiejsze utrzymanie - zmiana w email'ach nie wpływa na tworzenie zadań.

#### O - Open/Closed (Otwarty na rozszerzenie, zamknięty na modyfikację)

```php
// Istniejący kod - NIE ZMIENIAMY
interface TaskRepositoryInterface {
    public function save(Task $task): void;
}

class DoctrineTaskRepository implements TaskRepositoryInterface {
    public function save(Task $task): void { /* Doctrine */ }
}

// Dodajemy nową funkcjonalność - NIE MODYFIKUJEMY STAREGO
class ElasticsearchTaskRepository implements TaskRepositoryInterface {
    public function save(Task $task): void { /* Elasticsearch */ }
}
```

**Korzyść**: Zero ryzyka zepsucia działającego kodu przy dodawaniu nowych funkcji.

#### L - Liskov Substitution (Zamienność)

```php
// Handler oczekuje interfejsu
public function __construct(private TaskRepositoryInterface $repo) {}

// Możesz podać DOWOLNĄ implementację - zachowanie pozostaje poprawne
$handler = new CompleteTaskHandler(new DoctrineTaskRepository());
$handler = new CompleteTaskHandler(new RedisTaskRepository());
$handler = new CompleteTaskHandler(new InMemoryTaskRepository());
```

**Korzyść**: Testy z mock'ami, różne środowiska (dev/prod) z różnymi implementacjami.

#### I - Interface Segregation (Małe interfejsy)

```php
// ❌ Fat interface - wymusza implementację niepotrzebnych metod
interface TaskRepositoryInterface {
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function exportToPdf(): string;
    public function sendToSlack(): void;
    public function syncWithJira(): void;
}

// ✅ Lean interface - tylko co naprawdę potrzebne
interface TaskRepositoryInterface {
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function delete(Task $task): void;
}

// Eksport i integracje w osobnych interfejsach
interface TaskExporterInterface {
    public function exportToPdf(): string;
}
```

**Korzyść**: Nie musisz implementować metod, których nie używasz.

#### D - Dependency Inversion (Zależność od abstrakcji)

```php
// ❌ Zależność od konkretnej klasy
class CompleteTaskHandler {
    public function __construct(
        private DoctrineTaskRepository $repo // ← konkretna klasa
    ) {}
}

// ✅ Zależność od interfejsu
class CompleteTaskHandler {
    public function __construct(
        private TaskRepositoryInterface $repo // ← abstrakcja
    ) {}
}
```

**Korzyść**: Moduł wysokopoziomowy (Handler) nie zależy od niskopoziomowego (Doctrine).

---

### 5. Immutability & Type Safety

#### Immutability - Niemutowalność

```php
// ❌ Mutowalny obiekt
class User {
    public string $name;
}

$user = new User();
$user->name = 'John';
processUser($user);
echo $user->name; // Co teraz? Kto zmienił?

// ✅ Readonly - niezmienny
final readonly class User {
    public function __construct(
        public string $name,
    ) {}
}

$user = new User('John');
$user->name = 'Jane'; // BŁĄD KOMPILACJI!
```

**Korzyści**:
- Thread-safe (bezpieczne w wielowątkowości)
- Brak skutków ubocznych
- Łatwiejsze debugowanie

#### Type Safety - Bezpieczeństwo typów

```php
// ❌ Bez typów - błędy w runtime
function handle($command) {
    $title = $command->title ?? 'default'; // Może nie istnieć
    return doSomething($title);
}

handle(['title' => 'Foo']); // Array zamiast obiektu - BOOM w runtime!

// ✅ Z typami - błędy w kompilacji
function handle(CreateTaskCommand $command): Task {
    $title = $command->title; // ZAWSZE string, zawsze istnieje
    return new Task($title, $command->description);
}

handle(['title' => 'Foo']); // BŁĄD KOMPILACJI - oczekuje CreateTaskCommand!
```

**strict_types**:
```php
declare(strict_types=1);

function foo(int $x): void {}

foo(5);     // ✅ OK
foo("5");   // ❌ BŁĄD - nie konwertuje automatycznie string→int
```

**Korzyści**:
- IDE podpowiada typy i błędy przed uruchomieniem
- Self-documenting code
- Mniej bugów w produkcji

---

### Podsumowanie - Jak wszystko współgra?

```
1. USER wpisuje: php bin/todo app:complete-task 123

2. PRESENTATION (CLI Command):
   - Zbiera input (taskId = "123")
   - Tworzy CompleteTaskCommand("123") ← DTO (immutable)

3. APPLICATION (Handler):
   - Otrzymuje Command (type-safe!)
   - Wywołuje $repo->findById("123") ← Dependency Injection
   - Interfejs, nie wie że to Doctrine!

4. INFRASTRUCTURE (Repository):
   - DoctrineTaskRepository implementuje interfejs
   - Pobiera z SQLite przez Doctrine ORM

5. DOMAIN (Entity):
   - task->complete() ← Reguła biznesowa
   - Sprawdza czy już ukończone
   - Ustawia completedAt

6. INFRASTRUCTURE (Repository):
   - save(task) zapisuje do SQLite

7. APPLICATION (Handler):
   - Zwraca Task do CLI

8. PRESENTATION (CLI Command):
   - Wyświetla: "Task marked as completed!"
```

**Magiczne jest to**, że możesz:
- Zmienić SQLite→PostgreSQL (tylko adapter)
- Dodać HTTP API (nowy Presentation)
- Zmienić Doctrine→PDO (tylko adapter)
- Testować z mock'ami (DI + interfejsy)

**Bez zmiany logiki biznesowej w Domain i Application!**

## Uczenie się

Ten projekt jest doskonałym źródłem nauki:
- 📚 **Hexagonal Architecture** w praktyce
- 🎯 **SOLID principles** w akcji
- 🔄 **CQRS** w uproszczonej formie
- 🧩 **Design Patterns** (Repository, Command/Handler, DTO)
- 🏗️ **Clean Architecture** - separacja warstw
- 💉 **Dependency Injection** przez konstruktor

## Licencja

MIT License. See `LICENSE` file for details.
