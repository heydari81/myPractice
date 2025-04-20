<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewProduct implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $product_id;
    public function __construct($product_id)
    {
      $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Find all admin users
            $admins = User::where('is_admin', true)->get();
            $product = Product::find($this->product_id);
            if ($admins->isEmpty()) {
                Log::warning('No admins found to notify for new product: ' . $product->name);
                return;
            }

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\NewProductNotification($product));
            }

            Log::info('Notified admins of new product: ' . $product->name);
        } catch (\Exception $e) {
            Log::error('Failed to notify admins of new product: ' . $e->getMessage());
            throw $e;
        }
    }
}
