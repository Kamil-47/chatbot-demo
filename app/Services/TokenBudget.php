<?php

namespace App\Services;

use App\Exceptions\BudgetExceededException;
use Illuminate\Support\Facades\Cache;

class TokenBudget
{
    // Twardy cap globalny — ~$7.50 worst case dla gpt-4o-mini output.
    // Poniżej hard limitu $10 w OpenAI dashboardzie (który jest jedyną gwarancją).
    private const GLOBAL_TOKEN_HARD_CAP = 20_000_000;

    // Dzienny limit na jeden adres IP — obrona przed pojedynczym botem/klientem.
    private const PER_IP_DAILY_LIMIT = 100_000;

    private const GLOBAL_KEY = 'openai:tokens:global';

    public function assertWithinBudget(string $ip): void
    {
        $store = Cache::store('database');

        if ((int) $store->get(self::GLOBAL_KEY, 0) >= self::GLOBAL_TOKEN_HARD_CAP) {
            throw new BudgetExceededException('global', 'Global token budget exceeded');
        }

        if ((int) $store->get($this->ipKey($ip), 0) >= self::PER_IP_DAILY_LIMIT) {
            throw new BudgetExceededException('ip', 'Per-IP daily token limit exceeded');
        }
    }

    public function record(string $ip, int $tokens): void
    {
        if ($tokens <= 0) {
            return;
        }

        $store = Cache::store('database');

        // Globalny licznik bez TTL — persystentny do końca życia projektu.
        $store->increment(self::GLOBAL_KEY, $tokens);

        // Per-IP: Cache::increment nie ustawia TTL, więc robimy get+put z endOfDay.
        $ipKey = $this->ipKey($ip);
        $current = (int) $store->get($ipKey, 0);
        $store->put($ipKey, $current + $tokens, now()->endOfDay());
    }

    private function ipKey(string $ip): string
    {
        return "openai:tokens:ip:{$ip}:" . now()->format('Y-m-d');
    }
}
