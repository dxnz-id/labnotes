<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                TextInput::make('event')
                    ->label('Event Name')
                    ->live(onBlur: true)
                    ->columnSpan(3)
                    ->required()
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                        // Update the upload directory when event name changes
                        static::updateUploadDirectory($set, $get);
                    }),
                DatePicker::make('date')
                    ->label('Date')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->columnSpan(3)
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
                    ->live(onBlur: true)
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
                    ->live(onBlur: true)
                    ->columnSpan(6)
                    ->nullable()
                    ->options(function (Get $get) {
                        $participants = $get('participants') ?? [];
                        $options = [];

                        foreach ($participants as $index => $participant) {
                            if (isset($participant['name'])) {
                                $options[$index] = $participant['name'];
                            }
                        }

                        return $options;
                    }),
                FileUpload::make('photo')
                    ->label('Image Documentation')
                    ->image()
                    ->multiple()
                    ->previewable(false)
                    ->minSize(256)
                    ->maxSize(20480)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->nullable()
                    ->directory(function (Get $get) {
                        $date = $get('date');
                        $event = $get('event');

                        if (empty($date) || empty($event)) {
                            return 'events/temp-uploads';
                        }

                        $formattedDate = date('Y-m-d', strtotime($date));
                        $sanitizedEvent = Str::slug($event);

                        return "events/{$formattedDate}-{$sanitizedEvent}/images";
                    }),
                FileUpload::make('video')
                    ->label('Video Documentation')
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
                    ->previewable(false)
                    ->minSize(512)
                    ->maxSize(1048576)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->nullable()
                    ->directory(function (Get $get) {
                        $date = $get('date');
                        $event = $get('event');

                        if (empty($date) || empty($event)) {
                            return 'events/temp-uploads';
                        }

                        $formattedDate = date('Y-m-d', strtotime($date));
                        $sanitizedEvent = Str::slug($event);

                        return "events/{$formattedDate}-{$sanitizedEvent}/videos";
                    }),
                FileUpload::make('document')
                    ->label('Documents')
                    ->acceptedFileTypes([
                        // Microsoft Office
                        'application/msword',                                                  // Word (.doc)
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (.docx)
                        'application/vnd.ms-excel',                                            // Excel (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',   // Excel (.xlsx)
                        'application/vnd.ms-powerpoint',                                       // PowerPoint (.ppt)
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PowerPoint (.pptx)

                        // PDF
                        'application/pdf',                                                     // PDF documents

                        // Open Document Format
                        'application/vnd.oasis.opendocument.text',                             // OpenDocument Text (.odt)
                        'application/vnd.oasis.opendocument.spreadsheet',                      // OpenDocument Spreadsheet (.ods)
                        'application/vnd.oasis.opendocument.presentation',                     // OpenDocument Presentation (.odp)
                    ])
                    ->multiple()
                    ->previewable(false)
                    ->minSize(256)
                    ->maxSize(20480)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->nullable()
                    ->directory(function (Get $get) {
                        $date = $get('date');
                        $event = $get('event');

                        if (empty($date) || empty($event)) {
                            return 'events/temp-uploads';
                        }

                        $formattedDate = date('Y-m-d', strtotime($date));
                        $sanitizedEvent = Str::slug($event);

                        return "events/{$formattedDate}-{$sanitizedEvent}/documents";
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
