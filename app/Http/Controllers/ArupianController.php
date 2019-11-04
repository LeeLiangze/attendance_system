<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateQRCode;
use App\Jobs\SendAttendeeInvite;
use App\Models\Arupian;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Validator;

class ArupianController extends MyBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showArupians(Request $request, $event_id = '')
    {
        $allowed_sorts = ['first_name', 'last_name', 'email', 'reference', 'created_at'];

        $searchQuery = $request->get('t');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';
        $event = Event::scope()->find($event_id);
        if ($searchQuery) {
            /*
             * Strip the hash from the start of the search term in case people search for
             * order references like '#EDGC67'
             */
            if ($searchQuery[0] === '#') {
                $searchQuery = str_replace('#', '', $searchQuery);
            }

            $arupians = Arupian::where(function ($query) use ($searchQuery) {
                    $query->where('reference', 'like', $searchQuery . '%')
                        ->orWhere('first_name', 'like', $searchQuery . '%')
                        ->orWhere('email', 'like', $searchQuery . '%')
                        ->orWhere('last_name', 'like', $searchQuery . '%');
                })
                ->orderBy($sort_by, $sort_order)
                ->paginate();
        } else {
            $arupians = Arupian::orderBy($sort_by, $sort_order)->paginate();
        }

        $data = [
            'arupians'   => $arupians,
            'event'      => $event,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
            't'          => $searchQuery ? $searchQuery : '',
        ];

        return view('ManageEvent.Arupian', $data);
    }

    /**
     * Show the form for creating a new arupian.
     *
     * @return \Illuminate\Http\Response
     */
    public function showEditArupian(Request $request, $event_id, $arupian_id)
    {
        $arupian = Arupian::findOrFail($arupian_id);
        $groups = Group::pluck('name', 'id');

        $data = [
            'arupian' => $arupian,
            'groups'  => $groups,
            'event'   => $event_id
        ];
        return view('ManageEvent.modals.EditArupian', $data);
    }

    /**
     * Updates an attendee
     *
     * @param Request $request
     * @param $event_id
     * @param $attendee_id
     * @return mixed
     */
    public function postEditArupian(Request $request, $event_id, $arupian_id)
    {
        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email',
            'gender'     => 'required',
            'group_id'      => 'required',
        ];

        $messages = [
            'first_name.required'   => 'First name must not null',
            'last_name.required' => 'Last name must not null',
            'email.required' => 'Email must not null'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $arupian = Arupian::findOrFail($arupian_id);
        $arupian->update($request->all());

        session()->flash('message',trans("Controllers.successfully_updated_arupian"));

        return response()->json([
            'status'      => 'success',
            'id'          => $arupian->id,
            'redirectUrl' => '',
        ]);
    }

    /**
     * Shows the 'Cancel arupian' modal
     *
     * @param Request $request
     * @param $arupian_id
     * @return View
     */
    public function showCancelArupian(Request $request, $event_id, $arupian_id)
    {
        $arupian = Arupian::findOrFail($arupian_id);

        $data = [
            'arupian' => $arupian,
            'event'   => $event_id
        ];

        return view('ManageEvent.Modals.CancelArupian', $data);
    }

    /**
     * Cancels an arupian
     *
     * @param Request $request
     * @param $arupian_id
     * @return mixed
     */
    public function postCancelArupian(Request $request, $event_id, $arupian_id)
    {
        $arupian = Arupian::findOrFail($arupian_id)->delete();

        if ($arupian) {
            return response()->json([
                'status'      => 'success',
                'id'          => $arupian_id,
                'redirectUrl' => '',
            ]);
        } else {
            session()->flash('message', 'Successfully cancelled arupian.');
        }

    }

    /**
     * Show the 'Invite arup' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function showCreateArupian(Request $request, $event_id)
    {
        $event = Event::scope()->find($event_id);
        $groups = Group::pluck('name', 'id');

        return view('ManageEvent.Modals.CreateArupian', [
            'event'   => $event,
            'groups'  => $groups,
        ]);
    }

    /**
     * Post the 'create arup' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function postCreateArupian(Request $request, $event_id)
    {
        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:arupians,email',
            'gender'     => 'required',
            'group_id'      => 'required',
        ];

        $messages = [
            'first_name.required'   => 'First name must not null',
            'last_name.required' => 'Last name must not null',
            'email.required' => 'Email must not null'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $arupian = new Arupian();
        $arupian->first_name = $request->get('first_name');
        $arupian->last_name = $request->get('last_name');
        $arupian->email = $request->get('email');
        $arupian->gender = $request->get('gender');
        $arupian->group_id = $request->get('group_id');
        $arupian->save();

        session()->flash('message','Successfully Create Arupian');

        return response()->json([
            'status'      => 'success',
            'redirectUrl' => '',
            'event_id'    => $event_id,
        ]);
    }

    /**
     * Show the 'send arupian' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function showSendArupian(Request $request, $event_id)
    {
        $event = Event::scope()->find($event_id);
        if ($event->tickets->count() === 0) {
            return '<script>showMessage("Send Arupians error. Please create ticket");</script>';
        }

        return view('ManageEvent.Modals.SendArupian', [
            'event'   => $event,
            'tickets' => $event->tickets()->pluck('title', 'id'),
        ]);
    }

    /**
     * Post the 'send arupian' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function postSendArupian(Request $request, $event_id)
    {
        $ticket_id = $request->get('ticket_id');
        $ticket_price = 0;
        $email_attendee = $request->get('email_ticket');
        $num_added = 0;
        $arupians = Arupian::all();
        foreach ($arupians as $arupian) {
            $num_added++;

            $arupian_first_name = $arupian['first_name'];
            $arupian_last_name = $arupian['last_name'];
            $arupian_email = $arupian['email'];
            $arupian_gender = $arupian['gender'];
            $arupian_group_id = $arupian['group_id'];
            $arupian_reference = $arupian['reference'];
            $arupian_private_reference = $arupian['private_reference'];

            error_log($ticket_id . ' ' . $ticket_price . ' ' . $email_attendee);

            /**
             * Create the order
             */
            $order = new Order();
            $order->first_name = $arupian_first_name;
            $order->last_name = $arupian_last_name;
            $order->email = $arupian_email;
            $order->gender = $arupian_gender;
            $order->group_id = $arupian_group_id;
            $order->order_reference = $arupian_reference;
            $order->order_status_id = 1;
            $order->amount = $ticket_price;
            $order->account_id = Auth::user()->account_id;
            $order->event_id = $event_id;
            $order->taxamt = 0;
            $order->is_payment_received = 1;
            $order->save();

            /**
             * Update qty sold
             */
            $ticket = Ticket::scope()->find($ticket_id);
            $ticket->increment('quantity_sold');
            $ticket->increment('sales_volume', $ticket_price);
            $ticket->event->increment('sales_volume', $ticket_price);

            /**
             * Insert order item
             */
            $orderItem = new OrderItem();
            $orderItem->title = $ticket->title;
            $orderItem->quantity = 1;
            $orderItem->order_id = $order->id;
            $orderItem->unit_price = $ticket_price;
            $orderItem->save();

            /**
             * Update the event stats
             */
            $event_stats = new EventStats();
            $event_stats->updateTicketsSoldCount($event_id, 1);
            $event_stats->updateTicketRevenue($ticket_id, $ticket_price);

            /**
             * Create the attendee
             */
            $attendee = new Attendee();
            $attendee->first_name = $arupian_first_name;
            $attendee->last_name = $arupian_last_name;
            $attendee->email = $arupian_email;
            $attendee->gender = $arupian_gender;
            $attendee->group_id = $arupian_group_id;
            $attendee->private_reference_number = $arupian_private_reference;
            $attendee->event_id = $event_id;
            $attendee->order_id = $order->id;
            $attendee->ticket_id = $ticket_id;
            $attendee->account_id = Auth::user()->account_id;
            $attendee->reference_index = 1;
            $attendee->save();

            if ($email_attendee == '1') {
                $this->dispatch(new SendAttendeeInvite($attendee));
            }
        }

        session()->flash('message', $num_added . ' Arupians Successfully Invited');

        return response()->json([
            'status'      => 'success',
            'id'          => $arupian->id,
            'redirectUrl' => route('showArupians', [
            'event_id' => $event_id,
            ]),
        ]);
    }

    public function downloadQRCode()
    {
//        File::deleteDirectory(public_path(config('attendize.event_pdf_qrcode_path')));
        $arupians = Arupian::all();
        foreach ($arupians as $arupian) {
            $reference = $arupian['private_reference'];
            $id = $arupian['id'];
            $group_name = Group::where('id', $arupian['group_id'])->first()->name;
            $staff_id = $arupian['staff_id'];
            $this->dispatch(new GenerateQRCode($reference, $id, $group_name, $staff_id));
        }
        $zip_file = public_path('user_content/qrcode.zip');
        $zip = new \ZipArchive();
        if($zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $zip->addFile(public_path(config('attendize.event_pdf_qrcode_path')));
            if ($zip->close()) {
                return response()->download($zip_file, basename($zip_file))->deleteFileAfterSend(true);
            } else {
                throw new Exception("could not close zip file: " . $zip->getStatusString());
            }
        }
        else {
            throw new Exception("zip file could not be created: " . $zip->getStatusString());
        }



    }

}
