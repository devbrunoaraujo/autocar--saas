<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Vehicle;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Estoque';

    protected static ?string $modelLabel = 'Estoque';

    protected static ?string $pluralModelLabel = 'Estoque';

    protected static ?string $navigationGroup = 'Estoque';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Veículo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Entrada')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Veículo')
                    ->options(Vehicle::all()->pluck('full_name', 'id')),

                Tables\Filters\SelectFilter::make('entry_type')
                    ->label('Tipo de Entrada')
                    ->options([
                        'compra' => 'Compra',
                        'troca' => 'Troca',
                        'outro' => 'Outro',
                    ]),

                Tables\Filters\SelectFilter::make('exit_type')
                    ->label('Tipo de Saída')
                    ->options([
                        'venda' => 'Venda',
                        'baixa' => 'Baixa',
                        'outro' => 'Outro',
                    ]),

                Tables\Filters\Filter::make('sem_saida')
                    ->label('Sem Data de Saída')
                    ->query(fn($query) => $query->whereNull('exit_date')),
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
                Action::make('inventory_exit')
                    ->label('Registrar Saída')
                    ->icon('heroicon-o-arrow-right')
                    ->form([
                        Select::make('origin')
                            ->label('Tipo de Saída')
                            ->options([
                                'venda' => 'Venda',
                                'baixa' => 'Baixa',
                                'outro' => 'Outro',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Preço de Compra')
                            ->prefix('R$')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        // Register the exit movement
                        InventoryMovement::create([
                            'user_id' => auth()->id(),
                            'vehicle_id' => $record->vehicle_id,
                            'movement_type' => 'saída',
                            'origin' => $data['origin'],
                            'movement_date' => now(),
                            'purchase_price' => $data['purchase_price' ?? 0],
                            'description' => "Veículo {$record->vehicle->full_name} retirado do estoque.",
                        ]);
                        Inventory::find($record->id)?->delete();
                        Notification::make()
                            ->title('Saída registrada com sucesso!')
                            ->success()
                            ->send();
                    })->requiresConfirmation(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            //'view' => Pages\ViewInventory::route('/{record}'),
            //'create' => Pages\CreateInventory::route('/create'),
           // 'edit' => Pages\EditInventory::route('/{record}/edit'),

        ];
    }

    public static function canCreate(): bool
    {
        return false; // Disable creation of Inventory directly from the resource
    }
}
