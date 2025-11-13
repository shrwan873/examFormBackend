<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\FormRepositoryInterface;
use App\Repositories\Eloquent\FormRepository;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Repositories\Eloquent\SubmissionRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FormRepositoryInterface::class, FormRepository::class);
        $this->app->bind(SubmissionRepositoryInterface::class, SubmissionRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
    public function boot(): void 
    {
        
    }
}
