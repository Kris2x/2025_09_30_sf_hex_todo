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

### Tworzenie zadania

```bash
php bin/console app:create-task
```

Komenda poprosi o podanie tytułu i opisu zadania.

### Wyświetlanie wszystkich zadań

```bash
php bin/console app:get-tasks
```

## Architektura

Projekt wykorzystuje **Architekturę Heksagonalną** (Ports and Adapters):

```
src/
├── Domain/           # Logika biznesowa
│   ├── Model/        # Encje domenowe
│   └── Port/         # Interfejsy (porty)
├── Application/      # Przypadki użycia
├── Infrastructure/   # Implementacje techniczne
│   └── Persistence/  # Repozytoria Doctrine
└── Presentation/     # Interfejsy użytkownika
    └── Cli/          # Komendy konsolowe
```

## Baza danych

Aplikacja używa SQLite. Plik bazy danych znajduje się w `var/data_dev.db`.

## Licencja

Proprietary