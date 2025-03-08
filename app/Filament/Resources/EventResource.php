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

    /**
     * Updates the upload directories for all file upload fields when event name or date changes
     *
     * @param Set $set The setter function to update field states
     * @param Get $get The getter function to retrieve field states
     * @return void
     */
    
    protected static function getUploadDirectory(Get $get, string $folder): string
    {
        $date = $get('date');
        $event = $get('event');

        if (empty($date) || empty($event)) {
            return "events/temp-uploads/{$folder}";
        }

        $formattedDate = date('Y-m-d', strtotime($date));
        $sanitizedEvent = Str::slug($event);

        return "events/{$formattedDate}-{$sanitizedEvent}/{$folder}";
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                TextInput::make('event')
                    ->label('Event Name')
                    ->live(onBlur: true, debounce: 500)
                    ->columnSpan(3)
                    ->required(),
                DatePicker::make('date')
                    ->label('Date')
                    ->live(onBlur: true, debounce: 500)
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
                    ->minSize(64)
                    ->maxSize(32768)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->directory(fn (Get $get) => static::getUploadDirectory($get, 'images'))
                    ->nullable(),
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
                    ->maxSize(524288)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->directory(fn (Get $get) => static::getUploadDirectory($get, 'videos'))
                    ->nullable(),
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
                    ->minSize(64)
                    ->maxSize(65536)
                    ->visibility('private')
                    ->columnSpan(2)
                    ->directory(fn (Get $get) => static::getUploadDirectory($get, 'documents'))
                    ->nullable(),
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
