<?php

namespace App\CoreLogic\Services;

use App\Events\Offer\OfferCreated;
use App\Events\Offer\OfferDeleted;
use App\Events\Offer\OfferUpdated;
use App\Models\Offer;
use App\Models\States\Offer\OfferStates;
use App\CoreLogic\Enum\Offer\OfferTypeEnum;
use App\CoreLogic\Enum\Offer\PromoCodeTypeEnum;
use App\CoreLogic\Enum\Offer\VoucherTypeEnum;
use App\CoreLogic\Repositories\OfferRepository;
use Carbon\Carbon;

class OfferService extends Service
{
    protected string $repositoryName = OfferRepository::class;

    /**
     * @param array $offer
     * @return bool|Offer
     */
    public function create(array $offer): bool|Offer
    {
        $repository = $this->repository;
        $this->prepareData($offer);
        $products = $this->formatProducts($offer);
        $offer_type = $offer["offer_type"];
        unset($offer["products"]);

        if (OfferTypeEnum::tryFrom($offer_type) === OfferTypeEnum::PROMO_CODE) {
            $offerModel = $repository->create($offer);
            $offerModel->products()->sync($products, ['detach' => false]);
        } elseif (OfferTypeEnum::tryFrom($offer_type) === OfferTypeEnum::VOUCHER) {
            $code_type = $offer["code_type"];
            $offer['code_type'] = PromoCodeTypeEnum::FIXED;
            if (VoucherTypeEnum::AUTOMATIC === VoucherTypeEnum::tryFrom($code_type)) {
                $offerModel = $this->createMultiple($offer);
                $offerModel->products()->sync($products, ['detach' => false]);
            } else {
                $offerModel = $repository->create($offer);
                $offerModel->products()->sync($products, ['detach' => false]);
            }
        }

        if ($offerModel) {
            OfferCreated::dispatch($offerModel->fresh());
            return $offerModel;
        }
        return false;
    }

    /**
     * @param array $payload
     * @param $offer
     * @return bool|Offer
     */
    public function update(array $payload, $offer): bool|Offer
    {
        $this->prepareData($payload);
        $products = $this->formatProducts($payload);
        unset($payload["products"]);
        if (collect($payload)->get('status') != null) {
            if ($payload['statusText'] != $offer->status) {
                $offer->status->transitionTo($payload['status']);
            }
            unset($payload['status'], $payload['statusText']);
        }
        $updatedBool = $offer->update($payload);
        /*Note: To save product minimum quantity in pivot table*/
        $offer->products()->sync($products);
        OfferUpdated::dispatch($offer->fresh());
        return $updatedBool;
    }

    /**
     * @param $payload
     * @return array
     */
    private function formatProducts($payload): array
    {
        $products = [];
        if (collect($payload)->has('products') && collect($payload['products'])->count() > 0) {
            $products = collect($payload['products'])->map(function ($item, $productId) {
                return ["offerable_id" => $productId, 'data' => ['min_quantity' => $item]];
            })->values()->toArray();
        }
        return $products;
    }

    /**
     * @param $inputData
     * @return mixed
     * @throws \Spatie\ModelStates\Exceptions\InvalidConfig
     */
    private function prepareData(&$inputData)
    {
        $status = collect($inputData)->get('status') ? collect($inputData)->get('status') : 'active';
        $settings = collect($inputData)->only(Offer::getCustomColumns())->toArray();
        if (collect($settings)->count() > 0) {
            $inputData['settings'] = $settings;
        }
        /*Note: during step1 start_at will not consider*/
        if (collect($inputData)->get('start_at')) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $inputData['start_at']);
            if ($startDate->greaterThan(Carbon::now())) {
                $status = Offer::STATUS_SCHEDULED;
            }
        }
        if ($status != null) {
            $inputData['status'] = OfferStates::make($status, $inputData);
            $inputData['statusText'] = $status;
        }
        return $inputData;
    }

    /**
     * @param Offer $offer
     * @return void
     */
    public function archive(Offer $offer): void
    {
        $offer->status->transitionTo(Offer::STATUS_ARCHIVED);
        OfferDeleted::dispatch($offer->fresh());
    }

    /**
     * @return mixed
     */
    public function getPromoCodesForExpiry()
    {
        return $this->repository->whereNotIn(
            'status',
            [Offer::STATUS_EXPIRED, Offer::STATUS_COMPLETED]
        )->whereDate(
            'expired_at',
            '<=',
            Carbon::now()->toDateString()
        )
            ->whereTime('expired_at', '<', Carbon::now()->toTimeString());
    }

    /**
     * @return mixed
     */
    public function getScheduledPromoCodes()
    {
        return $this->repository->where('status', '=', Offer::STATUS_SCHEDULED)
            ->whereDate('start_at', '<=', Carbon::now()->toDateString())
            ->whereTime('start_at', '<', Carbon::now()->toTimeString());
    }

    /**
     * @param $offer
     * @return mixed
     */
    public function createMultiple($offer)
    {
        $quantity = $offer['quantity'];
        if (isset($offer['code']) && $offer['code'] !== '') {
            $code = $offer['code'];
        } else {
            $code = $this->generateCode();
        }
        do {
            $offer['code'] = $quantity > 1 ? $code . $quantity : $code;
            $offerModel = $this->repository->create($offer);
            $quantity--;
        } while ($quantity >= 1);

        return $offerModel;
    }

    /**
     * @return mixed
     */
    public function generateCode()
    {
        $code = \Str::random(6);
        if (Offer::where('code', $code)->exists()) {
            return $this->generateCode();
        }
        return $code;
    }

    /**
     * @param Offer $offer
     * @return Offer
     */
    public function get(Offer $offer): Offer
    {
        return $offer;
    }
}
