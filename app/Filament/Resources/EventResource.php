<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Infolists\Components\Document;
use App\Models\Event;
use Filament\Infolists\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\text;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    /**
     * Updates the upload directories for all file upload fields when event name or date changes
     *
     * @param Set $set The setter function to update field states
     * @param Get $get The getter function to retrieve field states
     * @return void
     */

    protected static function getUploadDirectory(Get $get, string $folder): string
    {
        // $eventDate = (new \DateTime($get('date')))->format('Y-m-d');
        // $eventName = $get('event');

        // // Generate a unique ID based on event name and date
        // $parent = $eventDate . '-' . $eventName;

        // return "events/{$parent}/{$folder}";
        return "events/{$folder}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                TextInput::make('event')
                    ->label('Event Name')
                    ->columnSpan(3)
                    ->required(),
                DatePicker::make('date')
                    ->label('Date')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->format('Y-m-d')
                    ->default(today())
                    ->columnSpan(3)
                    ->live(onBlur: true, debounce: 500)
                    ->required(),
                Repeater::make('responsible_person')
                    ->label('Responsible Persons')
                    ->columns(3)
                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                    ->collapsible()
                    ->minItems(1)
                    ->columnSpan(6)
                    ->required()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(255),
                    ]),
                Repeater::make('participants')
                    ->label('Participants')
                    ->columns(3)
                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                    ->live(onBlur: true, debounce: 500)
                    ->collapsible()
                    ->minItems(1)
                    ->columnSpan(6)
                    ->required()
                    ->schema($participants = [
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(255),
                    ]),
                Select::make('speaker')
                    ->label('Speaker')
                    ->multiple()
                    ->live(onBlur: true, debounce: 500)
                    ->columnSpan(6)
                    ->nullable()
                    ->options(function (Get $get) {
                        $participants = $get('participants') ?? [];
                        $options = [];

                        foreach ($participants as $participant) {
                            if (isset($participant['name'])) {
                                // Store the name directly as both key and value
                                // This ensures we store the actual name in the database
                                $name = $participant['name'];
                                $options[$name] = $name;
                            }
                        }

                        return $options;
                    }),
                FileUpload::make('photo')
                    ->label('Image Documentation')
                    ->panelLayout('grid')
                    ->image()
                    ->multiple()
                    ->preserveFilenames(true)
                    ->previewable(true)
                    ->minSize(64)
                    ->maxSize(32768)
                    ->visibility('private')
                    ->columnSpan(['default' => 6, 'xl' => 2])
                    ->directory(fn(Get $get) => static::getUploadDirectory($get, 'images'))
                    ->maxParallelUploads(3)
                    ->nullable(),
                FileUpload::make('video')
                    ->label('Video Documentation')
                    ->panelLayout('grid')
                    ->acceptedFileTypes([
                        'video/mp4',
                        'video/mpeg',
                        'video/quicktime',
                        'video/x-msvideo',
                        'video/x-ms-wmv',
                        'video/webm',
                        'video/3gpp',
                        'video/3gpp2',
                        'video/x-flv',
                        'video/x-matroska',
                    ])
                    ->multiple()
                    ->preserveFilenames(true)
                    ->previewable(false)
                    ->minSize(512)
                    ->maxSize(524288)
                    ->visibility('private')
                    ->columnSpan(['default' => 6, 'xl' => 2])
                    ->directory(fn(Get $get) => static::getUploadDirectory($get, 'videos'))
                    ->maxParallelUploads(3)
                    ->nullable(),
                FileUpload::make('document')
                    ->label('Documents')
                    ->panelLayout('grid')
                    ->multiple()
                    ->preserveFilenames(true)
                    ->previewable(false)
                    ->minSize(64)
                    ->maxSize(65536)
                    ->visibility('private')
                    ->columnSpan(['default' => 6, 'xl' => 2])
                    ->directory(fn(Get $get) => static::getUploadDirectory($get, 'documents'))
                    ->maxParallelUploads(3)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('speaker')
                    ->label('Speakers')
                    ->badge()
                    ->color('primary')
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options(function () {
                        return Event::query()
                            ->selectRaw('YEAR(date) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->mapWithKeys(fn($year) => [$year => $year]);
                    }),
                SelectFilter::make('month')
                    ->label('Month')
                    ->options(function () {
                        return Event::query()
                            ->selectRaw('MONTHNAME(date) as month')
                            ->distinct()
                            ->orderBy('month', 'asc')
                            ->pluck('month', 'month')
                            ->mapWithKeys(fn($month) => [$month => $month]);
                    }),
            ])
            ->actions([

                ViewAction::make()->label(false)->icon(false)->slideOver(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Event Details')
                    ->columnSpan(2)
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('event')
                                    ->label('Event Name'),
                                TextEntry::make('date')
                                    ->label('Date')
                                    ->dateTime('d M Y'),
                                TextEntry::make('responsible_person')
                                    ->label('Responsible Persons')
                                    ->badge()
                                    ->color('info')
                                    ->state(function ($record) {
                                        if (!is_array($record->responsible_person)) {
                                            return [];
                                        }

                                        return collect($record->responsible_person)
                                            ->map(fn($person) => $person['name'] ?? null)
                                            ->filter()
                                            ->toArray();
                                    }),
                                TextEntry::make('speaker')
                                    ->label('Speakers')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('participants')
                                    ->label('Participants')
                                    ->badge()
                                    ->state(function ($record) {
                                        if (!is_array($record->participants)) {
                                            return [];
                                        }

                                        return collect($record->participants)
                                            ->map(fn($participant) => $participant['name'] ?? null)
                                            ->filter()
                                            ->toArray();
                                    }),
                                TextEntry::make('total_images')
                                    ->label('Total Images')
                                    ->badge()
                                    ->color('warning')
                                    ->state(function ($record) {
                                        return count($record->photo);
                                    }),
                                TextEntry::make('total_videos')
                                    ->label('Total Videos')
                                    ->badge()
                                    ->color('danger')
                                    ->state(function ($record) {
                                        return count($record->video);
                                    }),
                                TextEntry::make('total_documents')
                                    ->label('Total Documents')
                                    ->badge()
                                    ->color('primary')
                                    ->state(function ($record) {
                                        return count($record->document);
                                    }),
                            ]),
                        Tabs\Tab::make('Media')
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tabs\Tab::make('Images')
                                            ->schema([
                                                ImageEntry::make('photo')
                                                    ->label(false)
                                                    ->openUrlInNewTab()
                                                    ->visibility('private'),
                                            ]),
                                        Tabs\Tab::make('Videos')
                                            ->schema([
                                                RepeatableEntry::make('video')
                                                    ->label(false)
                                                    ->schema([
                                                        TextEntry::make('video')
                                                            ->label(false)
                                                            ->state(function ($record) {
                                                                return collect($record->video)
                                                                    ->map(fn($file) => basename($file))
                                                                    ->toArray();
                                                            }),
                                                        Actions::make([
                                                            Action::make('view')
                                                                ->label('View')
                                                                ->url(fn($record) => "/storage/" . (is_array($record->video) ? $record->video[0] : $record->video))
                                                                ->icon('heroicon-o-eye')
                                                                ->openUrlInNewTab(),
                                                            Action::make('download')
                                                                ->label('Download')
                                                                ->action(fn($record) => Storage::download("public/" . (is_array($record->video) ? $record->video[0] : $record->video)))
                                                                ->icon('heroicon-o-arrow-down')
                                                                ->openUrlInNewTab(false),
                                                        ]),
                                                    ])
                                            ]),
                                        Tabs\Tab::make('Documents')
                                            ->schema([
                                                Document::make('document')
                                                    ->label(false),
                                            ]),
                                    ]),
                            ]),
                    ]),

                // TextEntry::make('event')
                //     ->label('Event Name'),
                // TextEntry::make('date')
                //     ->label('Date')
                //     ->dateTime('d M Y'),
                // RepeatableEntry::make('responsible_person')
                //     ->label('Responsible Persons')
                //     ->schema([
                //         TextEntry::make('name')
                //             ->label('Name'),
                //         TextEntry::make('email')
                //             ->label('Email'),
                //         TextEntry::make('phone_number')
                //             ->label('Phone Number'),
                //     ]),
                // RepeatableEntry::make('participants')
                //     ->label('Participants')
                //     ->schema([
                //         TextEntry::make('name')
                //             ->label('Name'),
                //         TextEntry::make('email')
                //             ->label('Email'),
                //         TextEntry::make('phone_number')
                //             ->label('Phone Number'),
                //     ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            // 'create' => Pages\CreateEvent::route('/create'),
            // 'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
