# LFG — Looking for Group

A matchmaking app for gamers to find teammates by game, skill level, region, timezone, and voice comm preference — built as a portfolio project to practice Laravel, Vue 3, and TypeScript.

## Tech Stack

- **Backend:** PHP, Laravel, MySQL, REST APIs
- **Frontend:** Vue 3, TypeScript, Tailwind CSS, Inertia.js
- **Real-time:** Pusher/Soketi + Laravel Echo (planned)
- **Payments:** Stripe (planned)
- **Game data:** IGDB API (via Twitch OAuth)
- **Testing:** Pest
- **Local dev:** Docker + Laravel Sail (MySQL)

## Features

- **Browse open lobbies** — filter and view posts from other players looking for a group
- **Create a post** — search for a game via a live IGDB-backed combobox (with automatic fallback to a custom game entry if it's not on IGDB), set skill/rank, region, timezone, availability, voice comm preference, party size, and join mode (auto-accept or manual review)
- **Request to join** — send a join request to an open lobby, with guards against joining your own post, duplicate requests, and closed lobbies
- **Accept / decline requests** — hosts manage incoming requests from their own posts; accepting automatically fills the lobby when party size is reached
- **Toast notifications** — flash messages for success/error states, driven by Inertia's request lifecycle

## Setup

### Requirements
- PHP 8.2+
- Composer
- Node.js + npm
- Docker Desktop (with WSL2 integration, if on Windows)

### Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Database (via Sail)

```bash
composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up -d
php artisan migrate --seed
```

### IGDB API credentials

Register an app at [dev.twitch.tv/console/apps](https://dev.twitch.tv/console/apps) (Confidential client type), then add to `.env`:

```bash
IGDB_CLIENT_ID=your_client_id
IGDB_CLIENT_SECRET=your_client_secret
```

### Run the app

```bash
composer run dev
```

This starts the PHP server, queue listener, log tailing, and Vite dev server together. Visit `http://127.0.0.1:8000`.

## Testing

```bash
php artisan test
```

## Roadmap

- [ ] Single post view (roster management, leave/remove members)
- [ ] Post-session rating prompt (win/loss + teammate feedback)
- [ ] Reputation and reporting system
- [ ] Real-time chat between matched players
- [ ] Stripe premium tier (priority posts, unlimited listings)
- [ ] Visual styling pass to match the neon/dark design direction
