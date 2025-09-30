# TODO Application

Aplikacja konsolowa do zarządzania zadaniami (TODO) zbudowana w Symfony 7.3 z wykorzystaniem Architektury Heksagonalnej.

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

## Architektura

Projekt wykorzystuje **Architekturę Heksagonalną** (Ports and Adapters) z elementami **CQRS**:

```
src/
├── Domain/              # Logika biznesowa
│   ├── Model/           # Encje domenowe (Task)
│   └── Port/            # Interfejsy (TaskRepositoryInterface)
├── Application/         # Przypadki użycia
│   ├── Command/         # DTOs dla operacji zapisu
│   ├── Query/           # DTOs dla operacji odczytu
│   └── Handler/         # Handlery use cases
├── Infrastructure/      # Implementacje techniczne
│   └── Persistence/     # Repozytoria Doctrine
└── Presentation/        # Interfejsy użytkownika
    └── Cli/             # Własna konsola + komendy
```

**Przepływ danych:**
```
CLI Command → Application Handler → Domain Port → Infrastructure Repository
```

**Cechy:**
- Separacja warstw (Domain, Application, Infrastructure, Presentation)
- Dependency Injection przez Symfony Container
- Command/Query Separation (CQRS Light)
- Własna aplikacja konsolowa (`TodoApplication`)

## Baza danych

Aplikacja używa SQLite. Plik bazy danych znajduje się w `var/data_dev.db`.

## Licencja

Proprietary