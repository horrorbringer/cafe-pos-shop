<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Domain\Notifications\Events\RefundVoided;
use App\Domain\Ordering\Actions\CancelOrderAction;
use App\Domain\Ordering\Actions\RefundOrderAction;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refund')
                ->label('Refund Order')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (Order $record): bool => in_array($record->status, ['paid', 'completed']))
                ->requiresConfirmation()
                ->modalHeading('Refund Order')
                ->modalDescription('This will mark the order as refunded. This action cannot be undone.')
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for refund')
                        ->required()
                        ->rows(3)
                        ->placeholder('Enter the reason for this refund...'),
                ])
                ->action(function (array $data, Order $record): void {
                    try {
                        $refundAction = app(RefundOrderAction::class);
                        $order = $refundAction->execute(
                            order: $record,
                            user: Auth::user(),
                            reason: $data['reason'],
                        );

                        event(new RefundVoided(
                            order: $order,
                            user: Auth::user(),
                            reason: $data['reason'],
                            type: 'refund',
                        ));

                        Notification::make()
                            ->title('Order refunded successfully')
                            ->success()
                            ->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Refund failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('cancel')
                ->label('Cancel Order')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn (Order $record): bool => ! in_array($record->status, ['cancelled', 'refunded']))
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->modalDescription('This will cancel the order.')
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for cancellation')
                        ->required()
                        ->rows(3)
                        ->placeholder('Enter the reason for cancellation...'),
                ])
                ->action(function (array $data, Order $record): void {
                    try {
                        $cancelAction = app(CancelOrderAction::class);
                        $order = $cancelAction->execute(
                            order: $record,
                            user: Auth::user(),
                            reason: $data['reason'],
                        );

                        Notification::make()
                            ->title('Order cancelled successfully')
                            ->success()
                            ->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Cancellation failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
