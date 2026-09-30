<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'User',
        };
    }

    /**
     * @return array<string, string> value => label, untuk opsi Select/Filter.
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * Normalisasi state string/enum (Filament bisa memberi keduanya)
     * menjadi enum. Melempar ValueError untuk nilai tak dikenal,
     * sama seperti cast Eloquent.
     */
    public static function coerce(string|self $role): self
    {
        return $role instanceof self ? $role : self::from($role);
    }
}
