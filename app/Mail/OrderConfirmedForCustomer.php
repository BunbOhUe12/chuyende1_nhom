<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedForCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['customer.user', 'items.motorcycle']);
    }

    public function envelope(): Envelope
    {
        $name = config('app.name', 'Cửa hàng');

        return new Envelope(
            subject: 'Đơn hàng '.$this->order->order_number.' đã được xác nhận — '.$name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.confirmed',
        );
    }
}
