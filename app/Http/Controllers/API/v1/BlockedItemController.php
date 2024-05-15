<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\BlockedItem\StoreUpdateBlockedItemRequest;
use App\Http\Resources\BlockedItemResource;
use App\Models\BlockedItem;
use App\Services\BlockedItem\IBlockedItemService;
use Illuminate\Http\Request;

class BlockedItemController extends ApiController
{
    private IBlockedItemService $blockedItemService;

    public function __construct(IBlockedItemService $blockedItemService)
    {

        $this->blockedItemService = $blockedItemService;

        // place the relations you want to return them within the response
        $this->relations = [];
    }

    public function index()
    {
        $items = $this->blockedItemService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(BlockedItemResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($blockedItemId)
    {
        /** @var BlockedItem|null $item */
        $item = $this->blockedItemService->view($blockedItemId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BlockedItemResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateBlockedItemRequest $request)
    {
        /** @var BlockedItem|null $item */
        $item = $this->blockedItemService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new BlockedItemResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($blockedItemId, StoreUpdateBlockedItemRequest $request)
    {
        /** @var BlockedItem|null $item */
        $item = $this->blockedItemService->update($request->validated(), $blockedItemId, $this->relations);
        if ($item) {
            return $this->apiResponse(new BlockedItemResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($blockedItemId)
    {
        $item = $this->blockedItemService->delete($blockedItemId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->blockedItemService->export($ids);
    }

    public function getImportExample()
    {
        return $this->blockedItemService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->blockedItemService->import();
    }
}
