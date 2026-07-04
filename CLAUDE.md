# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 12 / PHP 8.2 app for managing a private tutoring ("korepetycje") business: students, scheduled lessons, and monthly payments, run by an admin. It has an admin panel (Blade views) plus an AI chat assistant (React widget + OpenAI function calling) that lets the admin manage the same data conversationally.

Most code comments, log messages, and user-facing strings are written in Polish — match that when editing existing files.

## Commands

```bash
composer setup   # install deps, copy .env, generate key, migrate, npm install+build
composer dev      # runs server + queue:listen + pail (log viewer) + vite concurrently — normal dev loop
composer test     # config:clear then `php artisan test`
php artisan test --filter=TestName   # run a single test
php artisan test tests/Feature/ExampleTest.php  # run a single test file

npm run dev       # vite dev server only (if not using `composer dev`)
npm run build     # production frontend build
```

There's no configured linter beyond `.styleci.yml` (StyleCI, runs on the hosted service, not locally).

## Architecture

**Two separate frontends share one Laravel backend:**
- Admin panel: server-rendered Blade views under `resources/views/{student,lesson,payment,prompt}`, backed by standard resource controllers in `app/Http/Controllers`. Protected by `auth` + `isAdmin` middleware (`app/Http/Middleware/IsAdmin.php`, aliased in `bootstrap/app.php`). Non-admin logged-in users only see `user.dashboard`.
- Chat widget: a React app (`resources/js/components/ChatBot.jsx`, `ChatBubble.jsx`, `ChatWindow.jsx`) mounted via Vite/`app.jsx`, injected into Blade pages. It talks to a single stateless endpoint, `POST /api/chat` (`routes/api.php` → `ChatBotController@chat`), passing the full `conversationHistory` back and forth on every request (no server-side session state for the chat).

**Chatbot / OpenAI function-calling flow** (the core non-obvious piece of this codebase):
1. `ChatBotController::chat()` posts the conversation to OpenAI's `gpt-4o-mini` chat completions endpoint, along with a system prompt and a list of callable "tools".
2. The system prompt is *not* hardcoded — it's loaded from the DB (`Prompt` model, single row) and editable by the admin at `/prompt/edit`. If you change the assistant's behavior/instructions, that's the place, not the controller.
3. The tool/function schemas OpenAI is allowed to call are declared in `app/Services/functions.php` (plain array, OpenAI tool-calling format: name, description, JSON-schema parameters).
4. When OpenAI responds with `tool_calls`, `ChatBotController::executeFunction()` dispatches by function name to a matching method on `App\Services\ChatBotService`, which does the actual Eloquent read/write (students, lessons, payments) and returns a `['success' => bool, ...]` array. That result is fed back to OpenAI as a `role: tool` message, and the loop repeats until OpenAI returns a plain text response.
5. **Adding a new chatbot capability requires three changes kept in sync**: a new entry in `functions.php` (schema OpenAI sees), a new method on `ChatBotService` (implementation), and a new `case` in `ChatBotController::executeFunction()` (dispatch).

**Domain model** (`app/Models`): `Student` has many `Lesson` and `Payment`. `Student.schedule` is a JSON-cast array of `{weekday: "HH:MM"}` recurring lesson slots. `LessonController::generate()` is a batch job (triggered from the admin UI, `POST /lesson/generate`) that expands each student's `schedule` into concrete `Lesson` rows for a given month and creates/updates the matching monthly `Payment` (amount = lesson_count × `price_per_lesson`). Editing a single lesson's status (`LessonController::update`) recalculates that student's payment for the month via `updatePaymentForLesson()` — keep these two in sync if you touch either.

Status vocabularies are English (migrated from Polish in `2026_03_18_000000_update_statuses_to_english.php`): `Lesson.status` ∈ `planned|canceled|completed`; `Payment.status` ∈ `waiting|paid`.

**Config**: OpenAI key is read from `OPENAI_API_KEY` env var via `config('services.openai.key')` (`config/services.php`). Model, temperature, max_tokens are hardcoded in `ChatBotController::callOpenAI()`.
