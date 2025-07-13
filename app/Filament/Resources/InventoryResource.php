<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Estoque';
    protected static ?string $modelLabel = 'Movimentação de Estoque';
    protected static ?string $pluralModelLabel = 'Movimentações de Estoque';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vehicle_id')
                    ->label('Veículo')
                    ->options(Vehicle::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\DateTimePicker::make('entry_date')
                    ->label('Data de Entrada')
                    ->required(),

                Forms\Components\DateTimePicker::make('exit_date')
                    ->label('Data de Saída'),

                Forms\Components\Select::make('entry_type')
                    ->label('Tipo de Entrada')
                    ->options([
                        'compra' => 'Compra',
                        'troca' => 'Troca',
                        'outro' => 'Outro',
                    ])
                    ->required(),

                Forms\Components\Select::make('exit_type')
                    ->label('Tipo de Saída')
                    ->options([
                        'venda' => 'Venda',
                        'baixa' => 'Baixa',
                        'outro' => 'Outro',
                    ]),

                Forms\Components\TextInput::make('total_cost')
                    ->label('Custo Total')
                    ->prefix('R$')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Veículo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Data de Entrada')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make('exit_date')
                    ->label('Data de Saída')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_type')
                    ->label('Tipo de Entrada'),

                Tables\Columns\TextColumn::make('exit_type')
                    ->label('Tipo de Saída'),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Custo Total')
                    ->money('BRL'),
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
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'view' => Pages\ViewInventory::route('/{record}'),
            //'create' => Pages\CreateInventory::route('/create'),
            //'edit' => Pages\EditInventory::route('/{record}/edit'),

        ];
    }
}
