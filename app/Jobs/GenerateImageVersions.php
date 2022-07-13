<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\ImageTrait;

class GenerateImageVersions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ImageTrait;



    public $imageId;
    public $sizeConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageId, $sizeConfig)
    {
        $this->imageId = $imageId;
        $this->sizeConfig = $sizeConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->deleteImageFile($this->imageId, true);
        $this->genVariants($this->imageId, $this->sizeConfig);
    }
}
