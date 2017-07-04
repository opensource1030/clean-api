<?php

namespace WA\Jobs;

class EmailQueue extends Job
{
    protected $values;

    /**
     * EmailQueue constructor.
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        \Log::debug("EmailQueue@handle - values: " .print_r($this->values, true));

        $resAdmin = \Illuminate\Support\Facades\Mail::send(
                        $this->values['view_name'],
                        $this->values['data'],
                        function ($message) {
                            $message->subject($this->values['subject']);
                            $message->from($this->values['from'], 'Wireless Analytics');
                            $message->to($this->values['to']);
                        } // CALLBACK
                    );

        \Log::debug("EmailQueue@handle - Email has been sent.");
    }
}
