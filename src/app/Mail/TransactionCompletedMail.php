<?php

namespace App\Mail;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $item;
    public $user;
    public $buyer;

    public function __construct(Item $item, User $seller, User $buyer)
    {
        $this->item = $item;
        $this->user = $seller;
        $this->buyer = $buyer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('取引が完了しました')
            ->view('emails.transaction_completed');
    }
}
