<?php

namespace App\Http\Controllers;

use App\Models\Arupian;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\Request;
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

        $searchQuery = $request->get('q');
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
            'q'          => $searchQuery ? $searchQuery : '',
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



}
