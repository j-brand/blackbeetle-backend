<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class newPostComment extends Mailable
{
    use Queueable, SerializesModels;

    protected $story;
    protected $subscriber;
    protected $sub;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($story, $sub)
    {
        $this->story = $story;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.newPostComment')
            ->subject($this->sub)
            ->with([
                'greeting'      => 'Hallo ihr Reisenden,',
                'storyTitle'    => $this->story->title,
                'actionUrl'     => secure_url('blog/' . $this->story->slug),
                'introLines'    => ['Es gibt einen neuen Kommentar in der Geschichte <b>"' . $this->story->title . '"</b>.</br>', 'Sicherlich nur Gutes.'],
                'outroLines'    => ['Viel Spaß beim lesen.'],
            ]);
    }
}
