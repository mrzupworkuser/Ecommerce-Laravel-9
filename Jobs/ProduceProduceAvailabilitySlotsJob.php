<?php

namespace App\Jobs;

use App\Models\ProductAvailability;
use App\Models\States\ProductAvailabilitySlot\Available;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProduceProduceAvailabilitySlotsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ProductAvailability $availability
    ) {
    }

    /**
     * @return void
     */
    public function handle()
    {
        $totalAssetCapacityPerSlot = $this->availability->product->inventory->assets->sortBy('total_capacity')->first()->total_capacity;
        foreach (
            range(1, $totalAssetCapacityPerSlot) as $assetCapacity
        ) {
            $this->availability->slots()->createMany(
                $this->getSlots()
            );
        }
    }

    /**
     * @return array
     */
    public function getSlots(): array
    {
        $slotDuration = $this->getSlotDuration();
        $slots = [];

        foreach ($this->getSlotsRange() as $entry) {
            $slot = Carbon::parse($entry);
            $slots[] = [
                'start_at' => $slot->format('Y-m-d H:i:00'),
                'end_at' => $slot->addMinutes($slotDuration)->format('Y-m-d H:i:00'),
                'duration' => $slotDuration,
                'product_availability_id' => $this->availability->getKey(),
                'product_id' => $this->availability->product_id,
                'status' => Available::class,
                'domain_id' => $this->availability->domain_id,
                'tenant_id' => $this->availability->tenant_id,
            ];
        }

        return $slots;
    }

    /**
     * @return Collection
     */
    public function getSlotsRange(): Collection
    {
        $slotDays = $this->getAllDaySlotsRange();

        if ($this->availability->all_day) {
            return $slotDays;
        }

        $periods = $this->getSlotPeriods();

        return $slotDays->map(function ($day) use ($periods) {
            return $periods->map(function ($period) use ($day) {
                return $day->format('Y-m-d') . ' ' . $period;
            });
        })->flatten();
    }

    /**
     * @return Collection
     */
    public function getSlotPeriods(): Collection
    {
        $totalOperableMinutes = $this->availability->start_time->diffInMinutes($this->availability->end_time->subMinutes($this->getSlotDuration()));

        $slots = $totalOperableMinutes / $this->getSlotDuration();
        $periods = collect();
        foreach (range(0, $slots) as $slot) {
            $periods->push($this->availability->start_time->addMinutes($slot * $this->getSlotDuration())->format('G:i'));
        }

        return $periods;
    }

    /**
     * @return Collection
     */
    public function getAllDaySlotsRange(): Collection
    {
        $carbonDays = $this->availability->start_date->toPeriod(
            $this->availability->end_date,
            'days',
            1
        );
        $days = collect();

        foreach ($carbonDays as $cDay) {
            if (in_array($cDay->dayOfWeek, $this->availability->available_days, false)) {
                $days->push($cDay);
            }
        }

        return $days;
    }

    /**
     * @return int
     */
    public function getSlotDuration(): int
    {
        switch ($this->availability->duration_type) {
            case ProductAvailability::DURATION_UNIT_TYPE_HOURS:
                return $this->availability->duration * 60;
            case ProductAvailability::DURATION_UNIT_TYPE_MINUTES:
                return $this->availability->duration;
        }
    }
}
