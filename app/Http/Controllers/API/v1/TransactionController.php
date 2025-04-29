<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Transaction\StoreUpdateTransactionRequest;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\BalanceService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    private TransactionService $transactionService;

    public function __construct()
    {
        $this->transactionService = TransactionService::make();
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

    public function balance()
    {
        return $this->apiResponse(
            BalanceResource::make(BalanceService::make()->getBalance()),
            self::STATUS_OK,
            trans('site.get_successfully'),
        );
    }
}
