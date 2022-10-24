<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class ViewTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var array
     */
    protected $relations = [];

    protected Factory $factory;
    protected Model $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createNewFactory();
        $this->model = $this->makeNewModel();
    }

    /**
     * Return the Eloquent model name to be used in tests.
     *
     * @return string
     */
    abstract protected function getViewModelName(): string;

    private function createNewFactory(): Factory
    {
        return Factory::factoryForModel(
            $this->getViewModelName()
        )->new();
    }

    /**
     * Instance a new Eloquent model.
     *
     * @return Model
     */
    protected function instanceNewViewModel(): Model
    {
        $model = $this->getViewModelName();

        return new $model();
    }

    /**
     * Make a new Eloquent model.
     *
     * @return Model
     *
     */
    protected function makeNewModel(): Model
    {
        return $this->factory->make();
    }

    /**
     * @param Model  $model
     * @param string $relation
     * @param string $class
     *
     * @return void
     */
    private function makeRelation(Model $model, string $relation, string $class): void
    {
        $type = $this->instanceNewViewModel()->{$relation}();

        if ($type instanceof HasOne || $type instanceof HasMany) {
            $factory = Factory::factoryForModel($class);
            $instance = $factory::new();
            if (method_exists($instance, 'view')) {
                $instance->view($model->getKey())->make();
            }
        } elseif ($type instanceof MorphOne) {
            $instance = Factory::factoryForModel($class)->new();
            $morphClass = $type->getMorphClass();
            $morphId = $type->getForeignKeyName();
            $morphType = $type->getMorphType();
            $instance->create([
                $morphType => $morphClass,
                $morphId => $model->getKey()
            ]);
        }
    }

    /**
     * Find a Eloquent model.
     *
     * @return void
     */
    public function testFindUsingEloquent(): void
    {
        $modelFound = $this->instanceNewViewModel()
            ->newQuery()
            ->find($this->model->getKey());
        $created = $this->model->getAttributes();
        $found = $modelFound->getAttributes();
        $expected = array_intersect_key($created, $found);
        $this->assertNotEmpty($expected);
        $this->assertEquals($expected, $created);
    }

    /**
     * Relations.
     *
     * @return void
     */
    public function testRelationships(): void
    {
        if (empty($this->relations)) {
            $this->assertTrue(true);
        }

        foreach ($this->relations as $relation => $class) {
            $model = $this->makeNewModel();
            $this->makeRelation($model, $relation, $class);
            $type = $model->{$relation}();
            $related = $model->{$relation};

            if ($type instanceof BelongsTo || $type instanceof HasOne || $type instanceof MorphOne) {
                $this->assertInstanceOf($class, $related);
            } elseif ($type instanceof HasMany) {
                $this->assertCount(1, $related);
                $this->assertInstanceOf($class, $related->first());
            }
        }
    }
}
