<?php

namespace App\Filament\Resources\Beneficiaries;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse; // Needed for export
use App\Exports\BeneficiariesExport;
use BackedEnum;
use Maatwebsite\Excel\Facades\Excel;


class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

// protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'المستفيدين';

    protected static ?string $modelLabel = 'مستفيد';

    protected static ?string $pluralModelLabel = 'المستفيدين';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';


    public static function form(Schema $schema): Schema
    {
        return $schema // Use the $schema variable
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('national_id')
                            ->label('رقم الهوية')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(9),

                        Forms\Components\TextInput::make('full_name')
                            ->label('الاسم الرباعي')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('رقم الهاتف')
                            ->required()
                            ->tel(),
                    ])->columns(3),

                Section::make('المعلومات الأسرية')
                    ->schema([
                        Forms\Components\TextInput::make('family_members')
                            ->label('عدد أفراد الأسرة')
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\Textarea::make('address')
                            ->label('مكان السكن')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('الإحصائيات')
                    ->schema([
                        Forms\Components\TextInput::make('martyrs_count')
                            ->label('عدد الشهداء')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('injured_count')
                            ->label('عدد الجرحى')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('disabled_count')
                            ->label('عدد ذوي الإعاقة')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])->columns(3),

                Section::make('الحالة والإدارة')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('حالة الطلب')
                            ->options([
                                'new' => '🆕 جديد',
                                'pending' => '🕒 قيد المراجعة',
                                'approved' => '✅ معتمد',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('national_id')
                    ->label('رقم الهوية')
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('الاسم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('family_members')
                    ->label('أفراد الأسرة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('martyrs_count')
                    ->label('الشهداء')
                    ->sortable(),

                Tables\Columns\TextColumn::make('injured_count')
                    ->label('الجرحى')
                    ->sortable(),

                Tables\Columns\TextColumn::make('disabled_count')
                    ->label('ذوي الإعاقة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'new' => '🆕 جديد',
                        'pending' => '🕒 قيد المراجعة',
                        'approved' => '✅ معتمد',
                    }),

                Tables\Columns\TextColumn::make('created_at')
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
    ->query(fn(EloquentBuilder $query): EloquentBuilder => $query->where('martyrs_count', '>', 0)),

Filter::make('has_injured')
    ->label('يحتوي على جرحى')
    ->query(fn(EloquentBuilder $query): EloquentBuilder => $query->where('injured_count', '>', 0)),

Filter::make('has_disabled')
    ->label('يحتوي على ذوي إعاقة')
    ->query(fn(EloquentBuilder $query): EloquentBuilder => $query->where('disabled_count', '>', 0)),

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
                    ->visible(fn(Beneficiary $record) => $record->status !== 'approved'),

                Action::make('reject') // Note: this sets status to 'pending', not a rejected state.
                    ->label('رفض')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->action(function (Beneficiary $record) {
                        $record->update(['status' => 'pending']);
                    })
                    ->visible(fn(Beneficiary $record) => $record->status === 'approved'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('تصدير إلى Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(new BeneficiariesExport($records), 'beneficiaries-' . date('Y-m-d') . '.xlsx');
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
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}