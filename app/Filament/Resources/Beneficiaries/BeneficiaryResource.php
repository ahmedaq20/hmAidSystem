<?php

namespace App\Filament\Resources\Beneficiaries;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use App\Exports\BeneficiariesExport;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Form;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\Beneficiaries\Pages\EditBeneficiary;
use App\Filament\Resources\Beneficiaries\Pages\CreateBeneficiary;
use App\Filament\Resources\Beneficiaries\Pages\ListBeneficiaries;
use App\Filament\Resources\Beneficiaries\Schemas\BeneficiaryForm;
use App\Filament\Resources\Beneficiaries\Tables\BeneficiariesTable;


class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // public static function form(Schema $schema): Schema
    // {
    //     return BeneficiaryForm::configure($schema);
    // }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        TextInput::make('national_id')
                            ->label('رقم الهوية')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(9),
                            
                        TextInput::make('full_name')
                            ->label('الاسم الرباعي')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('phone_number')
                            ->label('رقم الهاتف')
                            ->required()
                            ->tel(),
                    ])->columns(3),
                    
                Section::make('المعلومات الأسرية')
                    ->schema([
                        TextInput::make('family_members')
                            ->label('عدد أفراد الأسرة')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                            
                        Textarea::make('address')
                            ->label('مكان السكن')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('الإحصائيات')
                    ->schema([
                        TextInput::make('martyrs_count')
                            ->label('عدد الشهداء')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                            
                        TextInput::make('injured_count')
                            ->label('عدد الجرحى')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                            
                        TextInput::make('disabled_count')
                            ->label('عدد ذوي الإعاقة')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])->columns(3),
                    
                Section::make('الحالة والإدارة')
                    ->schema([
                        Select::make('status')
                            ->label('حالة الطلب')
                            ->options([
                                'new' => '🆕 جديد',
                                'pending' => '🕒 قيد المراجعة',
                                'approved' => '✅ معتمد',
                            ])
                            ->required(),
                            
                        Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->columnSpanFull(),
                    ]),
            ]);
    }


    // public static function table(Table $table): Table
    // {
    //     return BeneficiariesTable::configure($table);
    // }

     public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('national_id')
                    ->label('رقم الهوية')
                    ->searchable(),
                    
                TextColumn::make('full_name')
                    ->label('الاسم')
                    ->searchable(),
                    
                TextColumn::make('phone_number')
                    ->label('الهاتف')
                    ->searchable(),
                    
                TextColumn::make('family_members')
                    ->label('أفراد الأسرة')
                    ->sortable(),
                    
                TextColumn::make('martyrs_count')
                    ->label('الشهداء')
                    ->sortable(),
                    
                TextColumn::make('injured_count')
                    ->label('الجرحى')
                    ->sortable(),
                    
                TextColumn::make('disabled_count')
                    ->label('ذوي الإعاقة')
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => '🆕 جديد',
                        'pending' => '🕒 قيد المراجعة',
                        'approved' => '✅ معتمد',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'new' => '🆕 جديد',
                        'pending' => '🕒 قيد المراجعة',
                        'approved' => '✅ معتمد',
                    ]),
                    
                Filter::make('has_martyrs')
                    ->label('يحتوي على شهداء')
                    ->query(fn (Builder $query): Builder => $query->where('martyrs_count', '>', 0)),
                    
                Filter::make('has_injured')
                    ->label('يحتوي على جرحى')
                    ->query(fn (Builder $query): Builder => $query->where('injured_count', '>', 0)),
                    
                Filter::make('has_disabled')
                    ->label('يحتوي على ذوي إعاقة')
                    ->query(fn (Builder $query): Builder => $query->where('disabled_count', '>', 0)),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                Action::make('approve')
                    ->label('اعتماد')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function (Beneficiary $record) {
                        $record->update(['status' => 'approved']);
                    })
                    ->visible(fn (Beneficiary $record) => $record->status !== 'approved'),
                    
                Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->action(function (Beneficiary $record) {
                        $record->update(['status' => 'pending']);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('تصدير إلى Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                echo (new BeneficiariesExport($records))->stream();
                            }, 'beneficiaries-' . date('Y-m-d') . '.xlsx');
                        }),
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
            'index' => ListBeneficiaries::route('/'),
            'create' => CreateBeneficiary::route('/create'),
            'edit' => EditBeneficiary::route('/{record}/edit'),
        ];
    }
}