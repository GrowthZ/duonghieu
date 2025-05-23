<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for product items
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class ItemRepository {

    /**
     * The items repository instance.
     */
    protected $items;

    /**
     * Inject dependecies
     */
    public function __construct(Product $items) {
        $this->items = $items;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object items collection
     */
    public function search($id = '') {

        $items = $this->items->newQuery();

        // all client fields
        $items->selectRaw('*');

        //joins
        // $items->leftJoin('categories', 'categories.category_id', '=', 'items.item_categoryid');
        // $items->leftJoin('pinned', function ($join) {
        //     $join->on('pinned.pinnedresource_id', '=', 'items.item_id')
        //         ->where('pinned.pinnedresource_type', '=', 'item');
        //     if (auth()->check()) {
        //         $join->where('pinned.pinned_userid', auth()->id());
        //     }
        // });

        //default where
        $items->whereRaw("1 = 1");

        //count items sold
        // $items->selectRaw('(SELECT COUNT(DISTINCT l.lineitem_id)
        //                         FROM lineitems l
        //                         JOIN invoices i ON l.lineitemresource_id = i.bill_invoiceid
        //                         WHERE l.lineitem_linked_product_id = items.item_id
        //                         AND l.lineitemresource_type = "invoice"
        //                         AND i.bill_status = "paid") as count_sold');

        // //sum items sold
        // $items->selectRaw('(SELECT COALESCE(SUM(l.lineitem_total), 0)
        //                         FROM lineitems l
        //                         JOIN invoices i ON l.lineitemresource_id = i.bill_invoiceid
        //                         WHERE l.lineitem_linked_product_id = items.item_id
        //                         AND l.lineitemresource_type = "invoice"
        //                         AND i.bill_status = "paid") as sum_sold');
        // //filters: id
        // if (request()->filled('filter_item_id')) {
        //     $items->where('item_id', request('filter_item_id'));
        // }
        // if (is_numeric($id)) {
        //     $items->where('id', $id);
        // }

        // //filter: rate (min)
        // if (request()->filled('filter_item_rate_min')) {
        //     $items->where('item_rate', '>=', request('filter_item_rate_min'));
        // }

        // //filter: rate (max)
        // if (request()->filled('filter_item_rate_max')) {
        //     $items->where('item_rate', '>=', request('filter_item_rate_max'));
        // }

        // //filter category
        // if (is_array(request('filter_item_categoryid')) && !empty(array_filter(request('filter_item_categoryid')))) {
        //     $items->whereIn('item_categoryid', request('filter_item_categoryid'));
        // }

        //search: various client columns and relationships (where first, then wherehas)
        // if (request()->filled('search_query') || request()->filled('query')) {
        //     $items->where(function ($query) {
        //         $query->orWhere('item_description', 'LIKE', '%' . request('search_query') . '%');
        //         $query->orWhere('item_rate', '=', request('search_query'));
        //         $query->orWhere('item_unit', '=', request('search_query'));
        //         $query->orWhereHas('category', function ($q) {
        //             $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
        //         });
        //     });
        // }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('products', request('orderby'))) {
                $items
                    ->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            // switch (request('orderby')) {
            // case 'category':
            //     $items->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
            //         ->orderBy('category_name', request('sortorder'));
            //     break;
            // case 'count_sold':
            //     $items->orderBy('count_sold', request('sortorder'));
            //     break;
            // case 'sum_sold':
            //     $items->orderBy('sum_sold', request('sortorder'));
            //     break;
            // }
        } else {
            //default sorting
            $items
                ->orderBy('id', 'desc');
        }

        //eager load
      

        // Get the results and return them.
        return $items->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $item = new $this->items;

        //data
        $item->item_categoryid = request('item_categoryid');
        $item->item_creatorid = auth()->id();
        $item->item_description = request('item_description');
        $item->item_unit = request('item_unit');
        $item->item_rate = request('item_rate');
        $item->item_notes_estimatation = request('item_notes_estimatation');

        //save and return id
        if ($item->save()) {
            return $item->item_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$item = $this->items->find($id)) {
            return false;
        }

        //general
        $item->item_categoryid = request('item_categoryid');
        $item->item_description = request('item_description');
        $item->item_unit = request('item_unit');
        $item->item_rate = request('item_rate');
        $item->item_notes_estimatation = request('item_notes_estimatation');

        //save
        if ($item->save()) {
            return $item->item_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}