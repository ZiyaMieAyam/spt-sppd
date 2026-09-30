<?php

namespace Tests\Unit;

use Tests\TestCase;

class EnvExampleLocaleTest extends TestCase
{
    public function test_env_example_memakai_locale_valid(): void
    {
        $values = [];
        foreach (file(base_path('.env.example'), FILE_IGNORE_NEW_LINES) as $baris) {
            if (str_starts_with($baris, 'APP_LOCALE=')) {
                $values['locale'] = substr($baris, strlen('APP_LOCALE='));
            }
            if (str_starts_with($baris, 'APP_FALLBACK_LOCALE=')) {
                $values['fallback'] = substr($baris, strlen('APP_FALLBACK_LOCALE='));
            }
        }

        $this->assertSame('id', $values['locale'] ?? null);
        $this->assertSame('id', $values['fallback'] ?? null);
    }
}
