<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Estimate;
use App\Repositories\TaxRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class EstimateRepository {

    /**
     * The estimates repository instance.
     */
    protected $estimates;

    /**
     * Inject dependecies
     */
    public function __construct(Estimate $estimates, LineitemRepository $lineitemrepo, TaxRepository $taxrepo) {
        $this->estimates = $estimates;
        $this->lineitemrepo = $lineitemrepo;
        $this->taxrepo = $taxrepo;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object estimate collection
     */
    public function search($id = '', $data = []) {

        $estimates = $this->estimates->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //for public url's etc
        if (request('do_not_apply_filters')) {
            $data['apply_filters'] = false;
        }

        // all client fields
        $estimates->selectRaw('*');

        //joins
        $estimates->leftJoin('clients', 'clients.client_id', '=', 'estimates.bill_clientid');
        $estimates->leftJoin('users', 'users.id', '=', 'estimates.bill_creatorid');
        $estimates->leftJoin('categories', 'categories.category_id', '=', 'estimates.bill_categoryid');
        $estimates->leftJoin('projects', 'projects.project_id', '=', 'estimates.bill_projectid');
        $estimates->leftJoin('pinned', function ($join) {
            $join->on('pinned.pinnedresource_id', '=', 'estimates.bill_estimateid')
                ->where('pinned.pinnedresource_type', '=', 'estimate');
            if (auth()->check()) {
                $join->where('pinned.pinned_userid', auth()->id());
            }
        });

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $estimates->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'estimates.bill_estimateid')
                    ->where('reminders.reminderresource_type', '=', 'estimate')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        //default where
        $estimates->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_bill_estimateid')) {
            $estimates->where('bill_estimateid', request('filter_bill_estimateid'));
        }
        if (is_numeric($id)) {
            $estimates->where('bill_estimateid', $id);
        }

        //filter by client - used for counting on external pages
        if (isset($data['bill_projectid'])) {
            $estimates->where('bill_projectid', $data['bill_projectid']);
        }

        //[document templates vs normal estimates]
        if (request('filter_estimate_type') == 'document' || request('estimate_mode') == 'document' || request('generate_estimate_mode') == 'document') {
            $estimates->where('bill_estimate_type', 'document');
        } else {
            $estimates->where('bill_estimate_type', 'estimate');
        }

        //do not show items that not yet ready (i.e exclude items in the process of being cloned that have status 'invisible')
        $estimates->where('bill_visibility', 'visible');

        //apply filters
        if ($data['apply_filters']) {

            //filter clients
            if (request()->filled('filter_bill_clientid')) {
                $estimates->where('bill_clientid', request('filter_bill_clientid'));
            }

            //filter clients
            if (request()->filled('filter_bill_projectid')) {
                $estimates->where('bill_projectid', request('bill_projectid'));
            }

            //filter: value (min)
            if (request()->filled('filter_bill_subtotal_min')) {
                $estimates->where('bill_final_amount', '>=', request('filter_bill_subtotal_min'));
            }

            //filter: value (max)
            if (request()->filled('filter_bill_subtotal_max')) {
                $estimates->where('bill_final_amount', '<=', request('filter_bill_subtotal_max'));
            }

            //filter: estimate date (start)
            if (request()->filled('filter_bill_date_start')) {
                $estimates->where('bill_date', '>=', request('filter_bill_date_start'));
            }

            //filter: estimate date (end)
            if (request()->filled('filter_bill_date_end')) {
                $estimates->where('bill_date', '<=', request('filter_bill_date_end'));
            }

            //filter: estimate date (start)
            if (request()->filled('filter_bill_expiry_date_start')) {
                $estimates->whereDate('bill_expiry_date', '>=', request('filter_bill_expiry_date_start'));
            }

            //filter: estimate date (end)
            if (request()->filled('filter_bill_expiry_date_end')) {
                $estimates->whereDate('bill_expiry_date', '<=', request('filter_bill_expiry_date_end'));
            }

            //resource filtering
            if (request()->filled('estimateresource_type') && request()->filled('estimateresource_id')) {
                switch (request('estimateresource_type')) {
                case 'client':
                    $estimates->where('bill_clientid', request('estimateresource_id'));
                    break;
                case 'project':
                    $estimates->where('bill_projectid', request('estimateresource_id'));
                    break;
                }
            }

            //stats: - count
            if (isset($data['stats']) && (in_array($data['stats'], [
                'count-new',
                'count-accepted',
                'count-declined',
                'count-expired',
            ]))) {
                $estimates->where('bill_status', str_replace('count-', '', $data['stats']));
            }
            //stats: - sum
            if (isset($data['stats']) && (in_array($data['stats'], [
                'sum-new',
                'sum-accepted',
                'sum-declined',
                'sum-expired',
            ]))) {
                $estimates->where('bill_status', str_replace('sum-', '', $data['stats']));
            }

            //filter category
            if (is_array(request('filter_bill_categoryid')) && !empty(array_filter(request('filter_bill_categoryid')))) {
                $estimates->whereIn('bill_categoryid', request('filter_bill_categoryid'));
            }

            //filter status
            if (is_array(request('filter_bill_status')) && !empty(array_filter(request('filter_bill_status')))) {
                $estimates->whereIn('bill_status', request('filter_bill_status'));
            }

            //filter created by
            if (is_array(request('filter_bill_creatorid')) && !empty(array_filter(request('filter_bill_creatorid')))) {
                $estimates->whereIn('bill_creatorid', request('filter_bill_creatorid'));
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $estimates->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

            //filter - exlude draft invoices
            if (request('filter_estimate_exclude_status') == 'draft') {
                $estimates->whereNotIn('bill_status', ['draft']);
            }

            //search: various client columns and relationships (where first, then wherehas)
            if (request()->filled('search_query') || request()->filled('query')) {
                $estimates->where(function ($query) {
                    //clean for estimate id search
                    $bill_estimateid = str_replace(config('system.settings_estimates_prefix'), '', request('search_query'));
                    $bill_estimateid = preg_replace("/[^0-9.,]/", '', $bill_estimateid);
                    $bill_estimateid = ltrim($bill_estimateid, '0');
                    $query->Where('bill_estimateid', '=', $bill_estimateid);

                    $query->orWhere('bill_date', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    $query->orWhere('bill_expiry_date', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    $query->orWhere('first_name', 'LIKE', '%' . request('search_query') . '%');
                    if (is_numeric(request('search_query'))) {
                        $query->orWhere('bill_final_amount', '=', request('search_query'));
                    }
                    $query->orWhere('bill_status', '=', request('search_query'));
                    $query->orWhereHas('tags', function ($q) {
                        $q->where('tag_title', 'LIKE', '%' . request('search_query') . '%');
                    });
                    $query->orWhereHas('category', function ($q) {
                        $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                    });
                    $query->orWhereHas('client', function ($q) {
                        $q->where('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                    });
                });
            }
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('estimates', request('orderby'))) {
                $estimates->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $estimates->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('client_company_name', request('sortorder'));
                break;
            case 'created_by':
                $estimates->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('first_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $estimates->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                ->orderBy(config('settings.ordering_estimates.sort_by'), config('settings.ordering_estimates.sort_order'));
        }

        //eager load
        $estimates->with([
            'tags',
        ]);

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'sum-new',
            'sum-accepted',
            'sum-declined',
            'sum-expired',
        ]))) {
            return $estimates->get()->sum('bill_final_amount');
        }

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'count-new',
            'count-accepted',
            'count-declined',
            'count-expired',
        ]))) {
            return $estimates->count();
        }

        // Get the results and return them.
        if (isset($data['limit']) && is_numeric($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = config('system.settings_system_pagination_limits');
        }

        //we are not paginating (e.g. when doing exports)
        if (isset($data['no_pagination']) && $data['no_pagination'] === true) {
            return $estimates->get();
        }

        return $estimates->paginate($limit);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_clientid = request('bill_clientid');
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_categoryid = request('bill_categoryid');
        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_status = 'draft';
        $estimate->bill_uniqueid = str_unique();
        if (is_numeric(request('bill_projectid'))) {
            $estimate->bill_projectid = request('bill_projectid');
        }

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function createProposalEstimate($id) {

        if (!is_numeric($id)) {
            return;
        }

        //check that we do not already have an estimate
        if (\App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->exists()) {
            return;
        }

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_estimateid = -time();
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_date = now();
        $estimate->bill_status = 'draft';
        $estimate->bill_proposalid = $id;
        $estimate->bill_estimate_type = 'document';
        $estimate->bill_uniqueid = str_unique();

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function createContractEstimate($id) {

        if (!is_numeric($id)) {
            return;
        }

        //check that we do not already have an estimate
        if (\App\Models\Estimate::Where('bill_contractid', $id)->Where('bill_estimate_type', 'document')->exists()) {
            return;
        }

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_estimateid = -time();
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_date = now();
        $estimate->bill_status = 'draft';
        $estimate->bill_contractid = $id;
        $estimate->bill_estimate_type = 'document';
        $estimate->bill_uniqueid = str_unique();

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id estimate id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$estimate = $this->estimates->find($id)) {
            return false;
        }

        //general
        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_subtotal = request('bill_subtotal');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_categoryid = request('bill_categoryid');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_status = request('bill_status');

        //save
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to update record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate_id' => $id ?? '']);
            return false;
        }
    }

    /**
     * refresh an estimate
     * @param mixed $estimate can be an estimate id or an estimate object
     * @return mixed bool or id of record
     */
    public function refreshEstimate($estimate) {

        //get the estimate
        if (is_numeric($estimate)) {
            if (!$estimate = $this->search($estimate)) {
                return false;
            }
        }

        if (!$estimate instanceof \App\Models\Estimate) {
            Log::error("unable to load estimate record", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //change dates to carbon format
        $bill_date = \Carbon\Carbon::parse($estimate->bill_date);
        $bill_expiry_date = \Carbon\Carbon::parse($estimate->bill_expiry_date);

        //estimate status for none draft, accepted, declined estimates
        if (!in_array($estimate->bill_status, ['draft', 'accepted', 'declined', 'revised'])) {

            //estimate is expired
            if ($estimate->bill_status == 'new') {
                if ($bill_expiry_date->diffInDays(today(), false) > 0) {
                    $estimate->bill_status = 'expired';
                }
            }

            //expired but date updated
            if ($estimate->bill_status == 'expired') {
                if ($bill_expiry_date->diffInDays(today(), false) < 0) {
                    $estimate->bill_status = 'new';
                }
            }

        }

        //update estimate
        $estimate->save();
    }

    /**
     * update an estimate from he edit estimate page
     * @param int $id record id
     * @return null
     */
    public function updateEstimate($id) {

        //get the record
        if (!$estimate = $this->estimates->find($id)) {
            Log::error("unable to load estimate record", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate_id' => $id ?? '']);
            return false;
        }

        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_subtotal = request('bill_subtotal');
        $estimate->bill_amount_before_tax = request('bill_amount_before_tax');
        $estimate->bill_final_amount = request('bill_final_amount');
        $estimate->bill_tax_type = request('bill_tax_type');
        $estimate->bill_tax_total_percentage = request('bill_tax_total_percentage');
        $estimate->bill_tax_total_amount = request('bill_tax_total_amount');
        $estimate->bill_discount_type = request('bill_discount_type');
        $estimate->bill_discount_percentage = request('bill_discount_percentage');
        $estimate->bill_discount_amount = request('bill_discount_amount');
        $estimate->bill_adjustment_description = request('bill_adjustment_description');
        $estimate->bill_adjustment_amount = request('bill_adjustment_amount');

        //save
        $estimate->save();
    }

    /**
     * save each estimateline item
     * (1) get all existing line items and unlink them from estimates or timers
     * (2) delete all existing line items
     * (3) save each line item
     * @param int $bill_estimateid resource id
     * @return mixed null|bool
     */
    public function saveLineItems($bill_estimateid = '') {

        Log::info("saving estimate line items - started", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validation
        if (!is_numeric($bill_estimateid)) {
            Log::error("validation error - required information is missing", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get the estimate
        if (!$estimate = \App\Models\Estimate::Where('bill_estimateid', $bill_estimateid)->first()) {
            Log::error("validation error - the linked estimate could not be loaded", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete line items
        \App\Models\Lineitem::Where('lineitemresource_type', 'estimate')
            ->where('lineitemresource_id', $bill_estimateid)
            ->delete();

        //delete line taxes for this bill
        \App\Models\Tax::Where('taxresource_type', 'estimate')
            ->where('taxresource_id', $bill_estimateid)
            ->where('tax_type', 'inline')
            ->delete();

        //default position
        $position = 0;

        //loopthrough each posted line item (use description to start the loop)
        if (is_array(request('js_item_description'))) {
            foreach (request('js_item_description') as $key => $description) {

                //next position (simple increment)
                $position++;

                //skip invalid items
                if (request('js_item_description')[$key] == '' || request('js_item_unit')[$key] == '') {
                    Log::error("invalid estimate line item...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //skip invalid items
                if (!is_numeric(request('js_item_rate')[$key]) || !is_numeric(request('js_item_total')[$key])) {
                    Log::error("invalid estimate line item...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //save lineitem to database
                if (request('js_item_type')[$key] == 'plain') {

                    //validate
                    if (!is_numeric(request('js_item_quantity')[$key])) {
                        Log::error("invalid estimate line item (plain) ...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        continue;
                    }

                    $line = [
                        'lineitem_description' => request('js_item_description')[$key],
                        'lineitem_quantity' => request('js_item_quantity')[$key],
                        'lineitem_rate' => request('js_item_rate')[$key],
                        'lineitem_unit' => request('js_item_unit')[$key],
                        'lineitem_total' => request('js_item_total')[$key],
                        'lineitemresource_linked_type' => request('js_item_linked_type')[$key],
                        'lineitemresource_linked_id' => request('js_item_linked_id')[$key],
                        'lineitem_type' => request('js_item_type')[$key],
                        'lineitem_position' => $position,
                        'lineitemresource_type' => 'estimate',
                        'lineitemresource_id' => $bill_estimateid,
                        'lineitem_time_timers_list' => null,
                        'lineitem_time_hours' => null,
                        'lineitem_time_minutes' => null,
                        'lineitem_tax_status' => request('js_item_tax_status')[$key],
                        'lineitem_linked_product_id' => request('js_item_id')[$key],
                    ];
                    $lineitem_id = $this->lineitemrepo->create($line);

                    //save line item taxes
                    if ($estimate->bill_tax_type == 'inline') {
                        $this->taxrepo->saveLineTaxes('estimate', $bill_estimateid, $lineitem_id, request('js_item_tax')[$key]);
                    }

                }

                //save time item to database
                if (request('js_item_type')[$key] == 'time') {

                    //validate
                    if (!is_numeric(request('js_item_hours')[$key]) || !is_numeric(request('js_item_minutes')[$key])) {
                        Log::error("invalid estimate line item (time) ...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        continue;
                    }

                    $line = [
                        'lineitem_description' => request('js_item_description')[$key],
                        'lineitem_quantity' => null,
                        'lineitem_rate' => request('js_item_rate')[$key],
                        'lineitem_unit' => request('js_item_unit')[$key],
                        'lineitem_total' => request('js_item_total')[$key],
                        'lineitemresource_linked_type' => request('js_item_linked_type')[$key],
                        'lineitemresource_linked_id' => request('js_item_linked_id')[$key],
                        'lineitem_type' => request('js_item_type')[$key],
                        'lineitem_position' => $position,
                        'lineitemresource_type' => 'estimate',
                        'lineitemresource_id' => $bill_estimateid,
                        'lineitem_time_hours' => request('js_item_hours')[$key],
                        'lineitem_time_minutes' => request('js_item_minutes')[$key],
                        'lineitem_time_timers_list' => request('js_item_timers_list')[$key],

                    ];

                    //process inline tax
                    $lineitem_id = $this->lineitemrepo->create($line);

                    //save line item taxes
                    if ($estimate->bill_tax_type == 'inline') {
                        $this->taxrepo->saveLineTaxes('estimate', $bill_estimateid, $lineitem_id, request('js_item_tax')[$key]);
                    }
                }

                //save dimensions item to database
                if (request('js_item_type')[$key] == 'dimensions') {

                    //validate
                    if (!is_numeric(request('js_item_length')[$key]) || !is_numeric(request('js_item_width')[$key])) {
                        Log::error("invalid estimate line item (time) ...skipping it", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        continue;
                    }

                    /** -------------------------------------------------------------------------
                     * [area] this will be stored as the 'quantity' value in the database line item
                     *    note - the rounding used here must match what was used in bill.js
                     *           [tagged] - dimensions rounding
                     * -------------------------------------------------------------------------*/
                    $area = request('js_item_length')[$key] * request('js_item_width')[$key];
                    $area = round($area, 2);
                    $line = [
                        'lineitem_description' => request('js_item_description')[$key],
                        'lineitem_quantity' => request('js_item_quantity')[$key],
                        'lineitem_rate' => request('js_item_rate')[$key],
                        'lineitem_unit' => request('js_item_unit')[$key],
                        'lineitem_total' => request('js_item_total')[$key],
                        'lineitemresource_linked_type' => request('js_item_linked_type')[$key],
                        'lineitemresource_linked_id' => request('js_item_linked_id')[$key],
                        'lineitem_type' => request('js_item_type')[$key],
                        'lineitem_position' => $position,
                        'lineitemresource_type' => 'estimate',
                        'lineitemresource_id' => $bill_estimateid,
                        'lineitem_dimensions_length' => request('js_item_length')[$key],
                        'lineitem_dimensions_width' => request('js_item_width')[$key],
                        'lineitem_time_hours' => null,
                        'lineitem_time_minutes' => null,
                        'lineitem_time_timers_list' => null,
                        'lineitem_tax_status' => request('js_item_tax_status')[$key],
                        'lineitem_linked_product_id' => request('js_item_id')[$key],
                    ];
                    $lineitem_id = $this->lineitemrepo->create($line);

                    //save line item taxes
                    if ($estimate->bill_tax_type == 'inline') {
                        $this->taxrepo->saveLineTaxes('estimate', $bill_estimateid, $lineitem_id, request('js_item_tax')[$key]);
                    }
                }
            }
        }
    }

    /**
     * convert the estimate into an invoice
     * @param int $id estimate id
     * @return \Illuminate\Http\Response
     */
    public function convertEstimateToInvoice($id) {

        //get the  estimate
        $estimate = \App\Models\Estimate::Where('bill_estimateid', $id)->first();

        //get the line items
        $lines = \App\Models\Lineitem::Where('lineitemresource_type', 'estimate')->where('lineitemresource_id', $id)->get();

        //get the line items
        $taxes = \App\Models\Tax::Where('taxresource_type', 'estimate')->where('taxresource_id', $id)->get();

        //create an invocie
        $invoice = new \App\Models\Invoice();
        $invoice->bill_clientid = $estimate->bill_clientid;
        $invoice->bill_projectid = $estimate->bill_projectid;
        $invoice->bill_creatorid = 0; //you can update this in the function calling this method
        $invoice->bill_subtotal = $estimate->bill_subtotal;
        $invoice->bill_discount_type = $estimate->bill_discount_type;
        $invoice->bill_discount_percentage = $estimate->bill_discount_percentage;
        $invoice->bill_discount_amount = $estimate->bill_discount_amount;
        $invoice->bill_amount_before_tax = $estimate->bill_amount_before_tax;
        $invoice->bill_tax_type = $estimate->bill_tax_type;
        $invoice->bill_tax_total_percentage = $estimate->bill_tax_total_percentage;
        $invoice->bill_tax_total_amount = $estimate->bill_tax_total_amount;
        $invoice->bill_final_amount = $estimate->bill_final_amount;
        $invoice->bill_adjustment_description = $estimate->bill_adjustment_description;
        $invoice->bill_adjustment_amount = $estimate->bill_adjustment_amount;
        $invoice->bill_notes = '';
        $invoice->bill_terms = config('system.settings_invoices_default_terms_conditions');
        $invoice->bill_status = $estimate->bill_status;
        $invoice->bill_invoice_type = 'onetime';
        $invoice->bill_type = 'invoice';
        $invoice->bill_visibility = 'visible';
        $invoice->bill_uniqueid = str_unique();

        //estimate notes
        if (request('copy_estimate_notes') == 'on') {
            $invoice->bill_notes = $estimate->bill_notes;
        }

        //estimate terms
        if (request('copy_estimate_terms') == 'on') {
            $invoice->bill_terms = $estimate->bill_terms;
        }

        $invoice->save();

        //clone line items
        foreach ($lines as $line) {
            $lineitem = $line->replicate();
            $lineitem->lineitem_created = now();
            $lineitem->lineitem_updated = now();
            $lineitem->lineitemresource_type = 'invoice';
            $lineitem->lineitemresource_id = $invoice->bill_invoiceid;
            $lineitem->save();

            //replicate [inline taxes]
            if ($estimate->bill_tax_type == 'inline') {
                //get all the taxes for original lineitem
                $taxes = \App\Models\Tax::Where('tax_lineitem_id', $line->lineitem_id)->get();
                foreach ($taxes as $tax_x) {
                    //get clean tax item for cloning
                    if ($tax = \App\Models\Tax::Where('tax_id', $tax_x->tax_id)->first()) {
                        $new_tax = $tax->replicate();
                        $new_tax->tax_lineitem_id = $lineitem->lineitem_id;
                        $new_tax->taxresource_type = 'estimate';
                        $new_tax->taxresource_id = $lineitem->bill_invoiceid;
                        $new_tax->save();
                    }
                }
            }
        }

        //clone taxes
        if ($estimate->bill_tax_type == 'summary') {
            foreach ($taxes as $tax) {
                $newtax = $tax->replicate();
                $newtax->tax_created = now();
                $newtax->tax_updated = now();
                $newtax->taxresource_type = 'invoice';
                $newtax->taxresource_id = $invoice->bill_invoiceid;
                $newtax->save();
            }
        }

        //update estimate with this invoices id
        $estimate->bill_converted_to_invoice = 'yes';
        $estimate->bill_converted_to_invoice_invoiceid = $invoice->bill_invoiceid;
        $estimate->save();

        return $invoice;

    }

}