<?php
// app/Filament/Resources/Beneficiaries/BeneficiaryResource.php

namespace App\Filament\Resources\Beneficiaries;

use BackedEnum;
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
use Illuminate\Support\Facades\Log;
use App\Exports\BeneficiariesExport;
use App\Imports\BeneficiariesImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationLabel = 'المستفيدين';

    protected static ?string $modelLabel = 'مستفيد';

    protected static ?string $pluralModelLabel = 'المستفيدين';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('national_id')
                            ->label('رقم الهوية')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(9)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('full_name')
                            ->label('الاسم الرباعي')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('رقم الهاتف')
                            ->required()
                            ->columnSpanFull()
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

                        Forms\Components\Textarea::make('notes')
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

                Action::make('reject')
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
            ])
            ->headerActions([
                // زر الاستيراد
                // في BeneficiaryResource - تحديث زر الاستيراد
                Action::make('import')
                    ->label('استيراد من Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->modalHeading('استيراد بيانات المستفيدين')
                    ->action(function (array $data) {
                        try {
                            Log::info('Starting import process...');

                            $import = new BeneficiariesImport;

                            Excel::import($import, $data['file']);

                            $importedCount = $import->getImportedCount();
                            $errors = $import->getErrors();

                            Log::info("Import completed. Imported: {$importedCount}, Errors: " . count($errors));

                            if (!empty($errors)) {
                                $errorMessage = "تم استيراد {$importedCount} سجل.<br><br><strong>الأخطاء:</strong><br>" . implode('<br>', array_slice($errors, 0, 5));
                                if (count($errors) > 5) {
                                    $errorMessage .= "<br>...و " . (count($errors) - 5) . " خطأ آخر";
                                }

                                Notification::make()
                                    ->title('تم الاستيراد مع بعض الأخطاء')
                                    ->body($errorMessage)
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('تم الاستيراد بنجاح')
                                    ->body("تم استيراد {$importedCount} سجل بنجاح")
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Import error: ' . $e->getMessage());

                            Notification::make()
                                ->title('خطأ في الاستيراد')
                                ->body('حدث خطأ أثناء الاستيراد: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->form([
                        FileUpload::make('file')
                            ->label('ملف Excel')
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv'
                            ])
                            ->maxSize(10240)
                            ->directory('imports')
                            ->helperText('
                <strong>ملاحظة:</strong> يجب أن تكون الأعمدة مرتبة بالترتيب المذكور أعلاه.
                <br>السطر الأول يمكن أن يحتوي على العناوين أو يبدأ بالبيانات مباشرة.
            ')
                            ->hint('يدعم: Excel (.xlsx, .xls) و CSV (.csv)')
                    ])
                    ->modalSubmitActionLabel('بدء الاستيراد')
                    ->modalCancelActionLabel('إلغاء')

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // \App\Filament\Widgets\BeneficiaryStats::class,
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