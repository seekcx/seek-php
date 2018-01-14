<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Repositories;
use Spatie\Regex\Regex;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        $this->bindMorphMap();
    }

    /**
     * 绑定多态模型名称映射
     *
     * @return void
     */
    protected function bindMorphMap()
    {
        Relation::morphMap([
            'column' => 'App\Entities\Column',
            'topic'  => 'App\Entities\Topic',
        ]);
    }

    /**
     * 绑定 Eloquent Repositories
     *
     * @return void
     */
    protected function bindEloquentRepositories()
    {
        collect([
            Repositories\Contracts\UserRepository::class   => Repositories\UserRepositoryEloquent::class,
            Repositories\Contracts\TopicRepository::class  => Repositories\TopicRepositoryEloquent::class,
            Repositories\Contracts\ColumnRepository::class => Repositories\ColumnRepositoryEloquent::class
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
