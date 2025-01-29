<?php

namespace App\Jobs;

use App\Models\ProductAvailabilitySlot;
use App\Models\States\ProductAvailabilitySlot\Hold;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReleaseSlotHoldJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public ProductAvailabilitySlot $slot,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->slot->status->equals(Hold::class)) {
            $this->slot->release();
        }
    }
}
