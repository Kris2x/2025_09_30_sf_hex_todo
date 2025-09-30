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
use App\Application\UseCase\CreateTask\CreateTaskCommand;
use App\Application\UseCase\CreateTask\CreateTaskHandler;

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

## Uczenie się

Ten projekt jest doskonałym źródłem nauki:
- 📚 **Hexagonal Architecture** w praktyce
- 🎯 **SOLID principles** w akcji
- 🔄 **CQRS** w uproszczonej formie
- 🧩 **Design Patterns** (Repository, Command/Handler, DTO)
- 🏗️ **Clean Architecture** - separacja warstw
- 💉 **Dependency Injection** przez konstruktor

## Licencja

Proprietary