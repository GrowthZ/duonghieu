@foreach ($items as $item)
    <!--each row-->
    <tr id="item_{{ $item->id }}" class="{{ $item->pinned_status ?? '' }}">
        @if (config('visibility.items_col_checkboxes'))
            <td class="items_col_checkbox checkitem" id="items_col_checkbox_{{ $item->id }}">
                <!--list checkbox-->
                <span class="list-checkboxes display-inline-block w-px-20">
                    <input type="checkbox" id="listcheckbox-items-{{ $item->id }}" name="ids[{{ $item->id }}]"
                        class="listcheckbox listcheckbox-items filled-in chk-col-light-blue items-checkbox"
                        data-actions-container-class="items-checkbox-actions-container"
                        data-item-id="{{ $item->id }}" data-unit="{{ $item->item_unit }}" data-quantity="1"
                        data-description="{{ $item->item_description }}" data-type="{{ $item->item_type }}"
                        data-length="{{ $item->item_dimensions_length }}"
                        data-width="{{ $item->item_dimensions_width }}" data-tax-status="{{ $item->item_tax_status }}"
                        data-has-estimation-notes="{{ $item->has_estimation_notes }}"
                        data-estimation-notes="{{ $item->estimation_notes_encoded }}"
                        data-rate="{{ $item->item_rate }}">
                    <label for="listcheckbox-items-{{ $item->id }}"></label>
                </span>
            </td>
        @endif
        <td class="items_col_description" id="items_col_description_{{ $item->id }}">
            {{-- @if (config('settings.trimmed_title'))
        {{ runtimeProductStripTags(str_limit($item->item_description ?? '---', 45)) }}
        @else --}}
            {{ $item->description }}
            {{-- @endif --}}
        </td>
        <td class="items_col_rate" id="items_col_rate_{{ $item->id }}">
            {{ $item->quantity }}
        </td>

        <td class="items_col_unit" id="items_col_unit_{{ $item->id }}">{{ $item->price_per_pos ?? 0 }}</td>
        <td class="items_col_unit" id="items_col_unit_{{ $item->id }}">{{ $item->price ?? 0}}</td>
        <td class="items_col_unit" id="items_col_unit_{{ $item->id }}">{{ $item->country_of_origin ?? 0}}</td>
        <td class="items_col_unit" id="items_col_unit_{{ $item->id }}">{{ (int)$item->status === 0 ?'Hết hàng' : 'Còn hàng'}}</td>
        



      

        @if (config('visibility.items_col_action'))
            <td class="items_col_action actions_column" id="items_col_action_{{ $item->id }}">
                <!--action button-->
                <span class="list-table-action font-size-inherit">
                    <!--delete-->
                    @if (config('visibility.action_buttons_delete'))
                        <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                            class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                            data-confirm-title="{{ cleanLang(__('lang.delete_product')) }}"
                            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                            data-url="{{ url('/') }}/items/{{ $item->id }}">
                            <i class="sl-icon-trash"></i>
                        </button>
                    @endif
                    @if (config('visibility.action_buttons_edit'))
                        <!--edit-->
                        <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                            class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ urlResource('/items/' . $item->id . '/edit') }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.edit_product')) }}"
                            data-action-url="{{ urlResource('/items/' . $item->id . '?ref=list') }}"
                            data-action-method="PUT" data-action-ajax-class=""
                            data-action-ajax-loading-target="items-td-container">
                            <i class="sl-icon-note"></i>
                        </button>
                      
                       
                    @endif
                    <!--more button (team)-->
                   

                   

                </span>
                <!--action button-->
            </td>
        @endif
    </tr>
@endforeach
<!--each row-->
