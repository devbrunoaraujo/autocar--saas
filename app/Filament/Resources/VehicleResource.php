<?php

namespace App\Filament\Resources;

use App\Contracts\FipeServiceInterface;
use App\Contracts\ImageProcessorInterface;
use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Inventory;
use App\Models\Vehicle;
use App\Services\ImageUploadService;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
//use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resource do Filament para gerenciar veículos
 *
 * Este resource fornece uma interface para consultar e gerenciar
 * informações de veículos usando a API da FIPE
 */
class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Veículos';

    protected static ?string $modelLabel = 'Veículo';

    protected static ?string $pluralModelLabel = 'Veículos';

    protected static ?string $navigationGroup = 'Veículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Veículo')
                    ->description('Selecione as informações do veículo para consultar o preço FIPE')
                    ->schema([
                        // Select para tipo de veículo
                        Forms\Components\Select::make('vehicle_type')
                            ->label('Tipo de Veículo')
                            ->options([
                                'cars' => 'Carros',
                                'motorcycles' => 'Motos',
                                'trucks' => 'Caminhões',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Limpa os campos dependentes quando o tipo muda
                                $set('brand_id', null);
                                $set('model_id', null);
                                $set('year_id', null);
                                $set('brand_name', null);
                                $set('model_name', null);
                                $set('year_name', null);
                                $set('fuel', null);
                                $set('fuel_acronym', null);
                                $set('fipe_price', null);
                                $set('month_reference', null);
                            }),

                        // Select para marca
                        Forms\Components\Select::make('brand_id')
                            ->label('Marca')
                            ->searchable()
                            ->options(function (callable $get) {
                                $vehicleType = $get('vehicle_type');
                                if (!$vehicleType) {
                                    return [];
                                }

                                try {
                                    $fipeService = app(FipeServiceInterface::class);
                                    $brands = $fipeService->getBrands($vehicleType);

                                    return collect($brands)->pluck('name', 'id')->toArray();
                                } catch (\Exception $e) {
                                    Log::error('Erro ao carregar marcas', ['error' => $e->getMessage()]);
                                    return [];
                                }
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Limpa os campos dependentes quando a marca muda
                                $set('model_id', null);
                                $set('year_id', null);
                                $set('model_name', null);
                                $set('year_name', null);
                                $set('fuel', null);
                                $set('fuel_acronym', null);
                                $set('fipe_price', null);
                                $set('month_reference', null);

                                // Define o nome da marca
                                if ($state && $get('vehicle_type')) {
                                    try {
                                        $fipeService = app(FipeServiceInterface::class);
                                        $brands = $fipeService->getBrands($get('vehicle_type'));
                                        $brand = collect($brands)->firstWhere('id', $state);
                                        $set('brand_name', $brand['name'] ?? '');
                                    } catch (\Exception $e) {
                                        Log::error('Erro ao definir nome da marca', ['error' => $e->getMessage()]);
                                    }
                                }
                            }),

                        // Select para modelo
                        Forms\Components\Select::make('model_id')
                            ->label('Modelo')
                            ->searchable()
                            ->options(function (callable $get) {
                                $vehicleType = $get('vehicle_type');
                                $brandId = $get('brand_id');

                                if (!$vehicleType || !$brandId) {
                                    return [];
                                }

                                try {
                                    $fipeService = app(FipeServiceInterface::class);
                                    $models = $fipeService->getModels($vehicleType, $brandId);

                                    return collect($models)->pluck('name', 'id')->toArray();
                                } catch (\Exception $e) {
                                    Log::error('Erro ao carregar modelos', ['error' => $e->getMessage()]);
                                    return [];
                                }
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Limpa os campos dependentes quando o modelo muda
                                $set('year_id', null);
                                $set('year_name', null);
                                $set('fuel', null);
                                $set('fuel_acronym', null);
                                $set('fipe_price', null);
                                $set('month_reference', null);

                                // Define o nome do modelo
                                if ($state && $get('vehicle_type') && $get('brand_id')) {
                                    try {
                                        $fipeService = app(FipeServiceInterface::class);
                                        $models = $fipeService->getModels($get('vehicle_type'), $get('brand_id'));
                                        $model = collect($models)->firstWhere('id', $state);
                                        $set('model_name', $model['name'] ?? '');
                                    } catch (\Exception $e) {
                                        Log::error('Erro ao definir nome do modelo', ['error' => $e->getMessage()]);
                                    }
                                }
                            }),

                        // Select para ano
                        Forms\Components\Select::make('year_id')
                            ->label('Ano')
                            ->options(function (callable $get) {
                                $vehicleType = $get('vehicle_type');
                                $brandId = $get('brand_id');
                                $modelId = $get('model_id');

                                if (!$vehicleType || !$brandId || !$modelId) {
                                    return [];
                                }

                                try {
                                    $fipeService = app(FipeServiceInterface::class);
                                    $years = $fipeService->getYears($vehicleType, $brandId, $modelId);

                                    return collect($years)->pluck('name', 'id')->toArray();
                                } catch (\Exception $e) {
                                    Log::error('Erro ao carregar anos', ['error' => $e->getMessage()]);
                                    return [];
                                }
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Limpa os campos de informações detalhadas
                                $set('fuel', null);
                                $set('fuel_acronym', null);
                                $set('fipe_price', null);
                                $set('month_reference', null);

                                // Define o nome do ano e busca informações detalhadas
                                if ($state && $get('vehicle_type') && $get('brand_id') && $get('model_id')) {
                                    try {
                                        $fipeService = app(FipeServiceInterface::class);

                                        // Define o nome do ano
                                        $years = $fipeService->getYears($get('vehicle_type'), $get('brand_id'), $get('model_id'));
                                        $year = collect($years)->firstWhere('id', $state);
                                        $set('year_name', $year['name'] ?? '');

                                        // Busca informações detalhadas do veículo
                                        $vehicleInfo = $fipeService->getVehicleInfo(
                                            $get('vehicle_type'),
                                            $get('brand_id'),
                                            $get('model_id'),
                                            $state
                                        );

                                        if (!empty($vehicleInfo)) {
                                            $set('fuel', $vehicleInfo['fuel'] ?? '');
                                            $set('fuel_acronym', $vehicleInfo['fuel_acronym'] ?? '');
                                            $set('fipe_price', $vehicleInfo['price'] ?? '');
                                            $set('month_reference', $vehicleInfo['month_reference'] ?? '');
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Erro ao buscar informações do veículo', ['error' => $e->getMessage()]);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informações da FIPE')
                    ->description('Informações obtidas automaticamente da API da FIPE')
                    ->schema([
                        Forms\Components\TextInput::make('fuel')
                            ->label('Combustível')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('fipe_price')
                            ->label('Preço FIPE')
                            ->disabled()
                            ->dehydrated(false),

                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalhes do Veículo')
                    ->description('Informações complementares do veículo')
                    ->schema([
                        Forms\Components\TextInput::make('license_plate')
                            ->label('Placa')
                            ->mask('*******'),
                        Forms\Components\TextInput::make('color')
                            ->label('Cor'),
                        Forms\Components\Select::make('transmission')
                            ->label('Transmissão')
                            ->options([
                                'Manual' => 'Manual',
                                'Automático' => 'Automático',
                            ]),

                        Forms\Components\TextInput::make('mileage')
                            ->label('Quilometragem')
                            ->numeric(),
                        Forms\Components\TextInput::make('renavam')
                            ->label('RENAVAM')
                            ->numeric(),
                        Forms\Components\TextInput::make('crv')
                            ->label('CRV')
                            ->numeric(),
                        Forms\Components\TextInput::make('chassis_number')
                            ->label('Chassi'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status e Mídia')
                    ->description('Status e imagens do veículo')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destaque'),

                        FileUpload::make('thumbnail')
                            ->label('Thumb')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('vehicles/thumbnail')
                            ->preserveFilenames()
                            ->visibility('public')
                            ->imagePreviewHeight('150')
                            ->saveUploadedFileUsing(function ($file) {
                                return ImageUploadService::saveWithWebp(
                                    file: $file,
                                    dir: 'vehicles/thumbnail',
                                    width: 640,
                                    quality: 80,
                                    disk: 'public'
                                );
                            })
                            ->deleteUploadedFileUsing(function ($file) {
                                Storage::disk('public')->delete($file);
                            }),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Imagens do veículo')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('vehicles/gallery')
                            ->imagePreviewHeight('150')
                            ->maxSize(2048)
                            ->helperText('Você pode enviar múltiplas imagens do carro.')
                            ->reorderable()
                            ->preserveFilenames()
                            ->disk('public')
                            ->visibility('public')
                            ->saveUploadedFileUsing(function ($file) {
                                return ImageUploadService::saveWithWebp(
                                    file: $file,
                                    dir: 'vehicles/gallery',
                                    width: 1280,
                                    quality: 75,
                                    disk: 'public'
                                );
                            })
                            ->deleteUploadedFileUsing(function ($file) {
                                Storage::disk('public')->delete($file);
                            }),

                    ])
                    ->columns(2),
                Forms\Components\Section::make('Opcionais')
                    ->description('Selecione os opcionais do veículo')
                    ->schema([
                        Forms\Components\CheckboxList::make('optionals')
                            ->label('Opcionais')
                            ->columns(4)
                            ->relationship('optionals', 'name')
                            ->searchable()
                            ->bulktoggleable()
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Valores de Compra e Venda')
                    ->description('Preços de compra e venda do veículo')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Preço de Compra')
                            ->numeric()
                            ->prefix('R$'),
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Preço de Venda')
                            ->numeric()
                            ->prefix('R$'),
                    ])
                    ->columns(2),

                // Campos ocultos para armazenar os dados
                Forms\Components\Hidden::make('brand_name'),
                Forms\Components\Hidden::make('model_name'),
                Forms\Components\Hidden::make('year_name'),
                Forms\Components\Hidden::make('fuel'),
                Forms\Components\Hidden::make('fuel_acronym'),
                Forms\Components\Hidden::make('fipe_price'),
                Forms\Components\Hidden::make('month_reference'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Tipo')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'cars' => 'Carros',
                            'motorcycles' => 'Motos',
                            'trucks' => 'Caminhões',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'cars' => 'success',
                            'motorcycles' => 'warning',
                            'trucks' => 'danger',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('brand_name')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('year_name')
                    ->label('Ano')
                    ->sortable(),


                Tables\Columns\TextColumn::make('fipe_price')
                    ->label('Preço FIPE')
                    ->money('BRL'),

                Tables\Columns\TextColumn::make('month_reference')
                    ->label('Referência')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_type')
                    ->label('Tipo de Veículo')
                    ->options([
                        'cars' => 'Carros',
                        'motorcycles' => 'Motos',
                        'trucks' => 'Caminhões',
                    ]),

                Tables\Filters\Filter::make('brand_name')
                    ->form([
                        Forms\Components\TextInput::make('brand_name')
                            ->label('Marca'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['brand_name'], function ($query, $brand) {
                            return $query->where('brand_name', 'like', "%{$brand}%");
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('add_to_inventory')
                    ->label('Adicionar ao Estoque')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\Select::make('entry_type')
                            ->label('Tipo de Entrada')
                            ->options([
                                'compra' => 'Compra',
                                'troca' => 'Troca',
                                'transferencia' => 'Transferência',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        if ($record->inventories()->exists()) {
                            throw new \Exception('Este veículo já está no estoque!');
                        }
                        Inventory::create([
                            'vehicle_id' => $record->id,
                            'entry_date' => now(),
                            'entry_type' => $data['entry_type'],
                            'total_cost' => $record->purchase_price,
                        ]);
                        Notification::make()
                            ->title('Veículo adicionado ao estoque com sucesso!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn($record) => $record->inventories()->count() === 0)

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            //'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
