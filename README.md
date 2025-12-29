# 🛡️ Sentri - SentriChannel Support Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/PostgreSQL-15-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/Redis-7.x-DC382D?style=for-the-badge&logo=redis&logoColor=white" alt="Redis">
</p>

> Profesjonalna platforma helpdesk z obsługą zgłoszeń w czasie rzeczywistym, systemem uprawnień i nowoczesnym interfejsem Dark Mode.

---

## ✨ Funkcjonalności

### 🎫 System Zgłoszeń (Tickets)
- Tworzenie, przeglądanie i zarządzanie zgłoszeniami
- Statusy: **Open** → **Pending** → **Closed**
- Priorytety: Low, Medium, High, Urgent
- System tagów do kategoryzacji
- Przypisywanie agentów do zgłoszeń

### 💬 Real-time Chat
- Komunikacja w czasie rzeczywistym przez **Laravel Reverb** (WebSockets)
- Wsparcie dla załączników (zdjęcia, dokumenty)
- Notatki wewnętrzne dla agentów (niewidoczne dla klientów)
- Wskaźnik pisania (typing indicator)

### 🔐 Role-based Access Control (RBAC)
- **Administrator** - pełny dostęp, zarządzanie użytkownikami
- **Agent** - obsługa przypisanych zgłoszeń
- **Customer** - tworzenie i śledzenie własnych zgłoszeń

### 📊 Analytics Dashboard
- Statystyki zgłoszeń (otwarte, oczekujące, zamknięte)
- Średni czas odpowiedzi
- Podgląd aktywności w czasie rzeczywistym

---

## 🏗️ Architektura

```
┌─────────────────────────────────────────────────────────────────┐
│                         Frontend (Vue 3)                        │
├─────────────────────────────────────────────────────────────────┤
│  Pinia Store  │  Vue Router  │  Tailwind CSS  │  Laravel Echo  │
└───────────────┴──────────────┴────────────────┴────────────────┘
                                    │
                              REST API / WebSocket
                                    │
┌─────────────────────────────────────────────────────────────────┐
│                       Backend (Laravel 11)                      │
├─────────────────────────────────────────────────────────────────┤
│            Controllers → Services → Actions → Models            │
├─────────────────────────────────────────────────────────────────┤
│   API Resources  │  Form Requests  │  Policies  │  Events      │
└──────────────────┴─────────────────┴────────────┴──────────────┘
                                    │
┌─────────────────────────────────────────────────────────────────┐
│                          Database Layer                         │
├───────────────────────────┬─────────────────────────────────────┤
│       PostgreSQL          │              Redis                  │
│   (persistent storage)    │    (cache, sessions, queues)        │
└───────────────────────────┴─────────────────────────────────────┘
```

### Wzorce projektowe

| Wzorzec | Zastosowanie |
|---------|--------------|
| **Service Pattern** | `TicketService` - centralna logika biznesowa |
| **Action Classes** | `AssignAgentAction`, `UpdateTicketStatusAction` - pojedyncze odpowiedzialności |
| **API Resources** | `TicketResource`, `MessageResource` - transformacja danych |
| **Form Requests** | `StoreTicketRequest` - walidacja wejścia |
| **Policies** | `TicketPolicy` - autoryzacja oparta na rolach |
| **Events & Broadcasting** | `MessageSent`, `TicketStatusChanged` - real-time updates |

---

## 🚀 Instalacja

### Wymagania
- PHP 8.3+
- Composer 2.x
- Node.js 20+
- PostgreSQL 15+
- Redis 7+

### Szybki start z Docker (Laravel Sail)

```bash
# 1. Klonowanie repozytorium
git clone https://github.com/your-username/sentri.git
cd sentri

# 2. Instalacja zależności PHP (bez Sail)
composer install

# 3. Konfiguracja środowiska
cp .env.example .env
php artisan key:generate

# 4. Uruchomienie przez Sail
./vendor/bin/sail up -d

# 5. Migracje i seed
./vendor/bin/sail artisan migrate --seed

# 6. Instalacja zależności JS i build
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

# 7. Uruchomienie WebSocket server (Reverb)
./vendor/bin/sail artisan reverb:start
```

### Instalacja lokalna (bez Docker)

```bash
# 1. Instalacja zależności
composer install
npm install

# 2. Konfiguracja
cp .env.example .env
php artisan key:generate

# 3. Edytuj .env - skonfiguruj połączenie z PostgreSQL i Redis

# 4. Migracje
php artisan migrate --seed

# 5. Uruchomienie serwerów (w osobnych terminalach)
php artisan serve        # Backend: http://localhost:8000
npm run dev              # Frontend: Vite dev server
php artisan reverb:start # WebSocket server
```

---

## 📁 Struktura Projektu

```
sentri/
├── app/
│   ├── Actions/                 # Single-responsibility actions
│   │   ├── Message/
│   │   │   └── SendMessageAction.php
│   │   └── Ticket/
│   │       ├── AssignAgentAction.php
│   │       └── UpdateTicketStatusAction.php
│   ├── DTOs/                    # Data Transfer Objects
│   │   └── TicketData.php
│   ├── Enums/                   # PHP 8.1+ Enums
│   │   ├── TicketPriority.php
│   │   ├── TicketStatus.php
│   │   └── UserRole.php
│   ├── Events/                  # Broadcast events
│   │   ├── MessageSent.php
│   │   ├── TicketAssigned.php
│   │   ├── TicketCreated.php
│   │   └── TicketStatusChanged.php
│   ├── Http/
│   │   ├── Controllers/Api/     # API Controllers
│   │   ├── Requests/            # Form Requests (validation)
│   │   └── Resources/           # API Resources (transformation)
│   ├── Models/                  # Eloquent Models
│   ├── Policies/                # Authorization Policies
│   └── Services/                # Business Logic Services
│       └── TicketService.php
├── database/
│   └── migrations/              # Database schema
├── resources/
│   ├── css/
│   │   └── app.css              # Tailwind CSS
│   ├── js/
│   │   ├── components/          # Vue Components
│   │   │   ├── ChatWindow.vue
│   │   │   └── Sidebar.vue
│   │   ├── pages/               # Vue Pages
│   │   │   ├── Dashboard.vue
│   │   │   ├── Login.vue
│   │   │   └── Register.vue
│   │   ├── services/            # API & Echo services
│   │   ├── stores/              # Pinia Stores
│   │   │   ├── useAuthStore.js
│   │   │   └── useTicketStore.js
│   │   └── app.js               # Main entry
│   └── views/
│       └── app.blade.php        # SPA template
└── routes/
    ├── api.php                  # API routes
    ├── channels.php             # Broadcast channels
    └── web.php                  # Web routes (SPA catch-all)
```

---

## 🔌 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Logowanie użytkownika |
| POST | `/api/register` | Rejestracja nowego konta |
| POST | `/api/logout` | Wylogowanie |
| GET | `/api/user` | Pobranie zalogowanego użytkownika |

### Tickets
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tickets` | Lista zgłoszeń (z filtrowaniem) |
| POST | `/api/tickets` | Utworzenie nowego zgłoszenia |
| GET | `/api/tickets/{id}` | Szczegóły zgłoszenia |
| PATCH | `/api/tickets/{id}/status` | Zmiana statusu |
| POST | `/api/tickets/{id}/assign` | Przypisanie agenta |
| GET | `/api/tickets/statistics` | Statystyki zgłoszeń |

### Messages
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tickets/{id}/messages` | Lista wiadomości w zgłoszeniu |
| POST | `/api/tickets/{id}/messages` | Wysłanie wiadomości |
| POST | `/api/messages/{id}/read` | Oznaczenie jako przeczytane |

---

## 🧪 Testowanie

```bash
# Uruchomienie testów jednostkowych
./vendor/bin/sail artisan test

# Z pokryciem kodu
./vendor/bin/sail artisan test --coverage
```

---

## 🎨 Stack Techniczny

### Backend
- **Laravel 11** - Framework PHP
- **Laravel Sanctum** - API Authentication
- **Laravel Reverb** - WebSocket Broadcasting
- **PostgreSQL** - Baza danych
- **Redis** - Cache, Sessions, Queues

### Frontend
- **Vue 3** - Framework JavaScript (Composition API)
- **Pinia** - State Management
- **Vue Router** - Routing
- **Tailwind CSS** - Styling
- **Vite** - Build Tool
- **Laravel Echo** - WebSocket Client

---

## 📄 Licencja

MIT License - zobacz plik [LICENSE](LICENSE) po szczegóły.

---

## 👨‍💻 Autor

Projekt stworzony z myślą o rekruterach i jako przykład profesjonalnego podejścia do architektury aplikacji webowych.

**Zasady SOLID zastosowane w projekcie:**
- **S**ingle Responsibility - Action Classes
- **O**pen/Closed - Service abstraction
- **L**iskov Substitution - Interface segregation
- **I**nterface Segregation - Slim controllers
- **D**ependency Inversion - Constructor injection

---

<p align="center">
  <strong>🛡️ Sentri</strong> - Built with ❤️ and clean code
</p>
