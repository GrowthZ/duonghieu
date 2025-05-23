<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the tickets
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tickets;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //standard view
        return view('pages/tickets/components/create/wrapper', compact('page', 'categories', 'tags', 'clients_projects', 'fields','canned_categories','canned_recently_used'))->render();
    }

}
