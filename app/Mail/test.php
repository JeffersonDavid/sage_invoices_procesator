<?php

use App\Mail\Notificator;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


var_dump(Mail::to('jeffersondvid@hotmail.com')->send(new Notificator()));
