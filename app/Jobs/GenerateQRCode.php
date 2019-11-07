<?php

namespace App\Jobs;

use App\Models\Arupian;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use PDF;

class GenerateQRCode extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $reference;
    protected $id;
    protected $group_name;
    protected $staff_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reference, $id, $group_name, $staff_id)
    {
        Log::info("Generating qrcode: #" . $reference);
        $this->reference = $reference;
        $this->id = $id;
        $this->group_name = $group_name;
        $this->staff_id = $staff_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = explode(" ", $this->group_name, 2)[1] . "_" . $this->staff_id;
        $file_path = public_path(config('attendize.event_pdf_qrcode_path')) . '/' . $file_name;
        $file_with_ext = $file_path . ".pdf";

        if (file_exists($file_with_ext)) {
            Log::info("Use ticket from cache: " . $file_with_ext);
            return;
        }

        $arupian = Arupian::find($this->id);
        $data = [
            'arupian' => $arupian,
        ];
        try {
            PDF::setOutputMode('F'); // force to file
            PDF::html('Public.ViewEvent.Partials.PDFQRCode', $data, $file_path);
            Log::info("qrcode generated!");
        } catch(\Exception $e) {
            Log::error("Error generating qrcode. This can be due to permissions on vendor/nitmedia/wkhtml2pdf/src/Nitmedia/Wkhtml2pdf/lib. This folder requires write and execute permissions for the web user");
            Log::error("Error message. " . $e->getMessage());
            Log::error("Error stack trace" . $e->getTraceAsString());
            $this->fail($e);
        }

    }

    private function isAttendeeTicket()
    {
        return ($this->attendee_reference_index != null);
    }
}
