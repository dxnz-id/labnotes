<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEvents extends TableWidget
{
    protected int | string | array $columnSpan = '7';

    public function table(Table $table): Table
    {
        return $table
            ->query(Event::query()->latest()->limit(5)) // Tambahkan limit di sini
            ->columns([
                TextColumn::make('event')
                    ->label('Event Name'),
                TextColumn::make('date')
                    ->label('Date'),
                TextColumn::make('responsible_person')
                    ->label('Responsible Persons')
                    ->state(function ($record) {
                        if (!is_array($record->responsible_person)) {
                            return [];
                        }

                        return collect($record->responsible_person)
                            ->map(fn($person) => $person['name'] ?? null)
                            ->filter()
                            ->toArray();
                    })
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false) // Nonaktifkan pagination
            ->defaultSort('created_at', 'desc');
    }

    public static function getSort(): int
    {
        return 2;
    }
}
