<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Repositories;
use Spatie\Regex\Regex;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\Resource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindEloquentRepositories();
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        Resource::withoutWrapping();

        $this->addRules();
        $this->setCarbonFormat();
    }

    /**
     * 绑定 Eloquent Repositories
     *
     * @return void
     */
    protected function bindEloquentRepositories()
    {
        collect([
            Repositories\Contracts\UserRepository::class => Repositories\UserRepositoryEloquent::class
        ])->each(function ($repository, $contract) {
            $this->app->bind($contract, $repository);
        });
    }

    /**
     * 设置 Carbon 字符串格式
     *
     * @return void
     */
    public function setCarbonFormat()
    {
        Carbon::setToStringFormat('Y-m-d h:i:s T');
    }

    /**
     * 添加自定义验证规则
     *
     * @return void
     */
    public function addRules()
    {
        Validator::extend('name', function ($attribute, $value, $parameters, $validator) {
            return Regex::match('/^[\w]{2,12}$/u', $value)->hasMatch();
        });
    }
}
