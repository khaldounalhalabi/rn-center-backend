<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Transaction\StoreUpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\Transaction\ITransactionService;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    private ITransactionService $transactionService;

    public function __construct(ITransactionService $transactionService)
    {

        $this->transactionService = $transactionService;

        // place the relations you want to return them within the response
        $this->relations = ['actor'];
    }

    public function index()
    {
        $items = $this->transactionService->indexWithPagination($this->relations);
        if ($items) {
            return $this->apiResponse(TransactionResource::collection($items['data']), self::STATUS_OK, __('site.get_successfully'), $items['pagination_data']);
        }

        return $this->noData([]);
    }

    public function show($transactionId)
    {
        /** @var Transaction|null $item */
        $item = $this->transactionService->view($transactionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new TransactionResource($item), self::STATUS_OK, __('site.get_successfully'));
        }

        return $this->noData(null);
    }

    public function store(StoreUpdateTransactionRequest $request)
    {
        /** @var Transaction|null $item */
        $item = $this->transactionService->store($request->validated(), $this->relations);
        if ($item) {
            return $this->apiResponse(new TransactionResource($item), self::STATUS_OK, __('site.stored_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.something_went_wrong'));
    }

    public function update($transactionId, StoreUpdateTransactionRequest $request)
    {
        /** @var Transaction|null $item */
        $item = $this->transactionService->update($request->validated(), $transactionId, $this->relations);
        if ($item) {
            return $this->apiResponse(new TransactionResource($item), self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->noData(null);
    }

    public function destroy($transactionId)
    {
        $item = $this->transactionService->delete($transactionId);
        if ($item) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.delete_successfully'));
        }

        return $this->noData(false);
    }

    public function export(Request $request)
    {
        $ids = $request->ids ?? [];

        return $this->transactionService->export($ids);
    }

    public function getImportExample()
    {
        return $this->transactionService->getImportExample();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
        ]);

        $this->transactionService->import();
    }
}
