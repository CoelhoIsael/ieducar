<?php

namespace Tests;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Models\LegacyModel;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class EloquentTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var array
     */
    protected $relations = [];

    protected Model $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = $this->createNewModel();
    }

    /**
     * Return the Eloquent model name to be used in tests.
     *
     * @return string
     */
    abstract protected function getEloquentModelName();

    /**
     * Return attributes to be used in create action.
     *
     * @return array
     */
    protected function getAttributesForCreate()
    {
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        return $factory->make()->toArray();
    }

    /**
     * Return attributes to be used in update action.
     *
     * @return array
     */
    protected function getAttributesForUpdate()
    {
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        return $factory->make()->toArray();
    }

    /**
     * Instance a new Eloquent model.
     *
     * @return Model
     */
    protected function instanceNewEloquentModel()
    {
        $model = $this->getEloquentModelName();

        return new $model();
    }

    /**
     * Create a new Eloquent model.
     *
     * @return Model
     *
     * @see Model::save()
     *
     */
    protected function createNewModel()
    {
        $model = $this->instanceNewEloquentModel();

        $model->fill($this->getAttributesForCreate());
        $model->save();

        return $model;
    }

    /**
     * Create a Eloquent model.
     *
     * @return void
     */
    public function testCreateUsingEloquent()
    {
        $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());
    }

    /**
     * Update a Eloquent model.
     *
     * @return void
     */
    public function testUpdateUsingEloquent()
    {
        $modelUpdated = clone $this->model;

        $modelUpdated->fill($this->getAttributesForUpdate());
        $modelUpdated->save();

        $this->assertDatabaseMissing($modelUpdated->getTable(), $this->model->getAttributes());
        $this->assertDatabaseHas($modelUpdated->getTable(), $this->removeTimestamps($modelUpdated->getAttributes()));
    }

    private function removeTimestamps(array $attributes): array
    {
        if (array_key_exists('updated_at', $attributes)) {
            unset($attributes['updated_at']);
        }

        return $attributes;
    }

    /**
     * Delete a Eloquent model.
     *
     * @return void
     *
     * @throws Exception
     *
     */
    public function testDeleteUsingEloquent()
    {
        $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());

        $this->model->delete();

        if (in_array(SoftDeletes::class, class_uses($this->model), true) || in_array(LegacySoftDeletes::class, class_uses($this->model), true)) {
            $this->assertSoftDeleted($this->model, deletedAtColumn: $this->model->getDeletedAtColumn());
        } else {
            $this->assertDatabaseMissing($this->model->getTable(), $this->model->getAttributes());
        }
    }

    /**
     * Find a Eloquent model.
     *
     * @return void
     */
    public function testFindUsingEloquent()
    {
        $modelFound = $this->instanceNewEloquentModel()
            ->newQuery()
            ->find($this->model->getKey());

        $created = $this->model->getAttributes();
        $found = $modelFound->getAttributes();

        $expected = array_intersect_key($created, $found);

        $this->assertEquals($expected, $created);
    }

    /**
     * Relations.
     *
     * @return void
     */
    public function testRelationships()
    {
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        if (empty($this->relations)) {
            $this->assertTrue(true);
        }

        foreach ($this->relations as $relation => $class) {
            if (is_array($class)) {
                $method = 'has' . ucfirst($relation);
                $model = $factory->{$method}()->create();

                $this->assertCount(1, $model->$relation);
                $this->assertInstanceOf($class[0], $model->$relation->first());
            } else {
                $model = $factory->create();
                $this->assertInstanceOf($class, $model->{$relation});
            }
        }
    }

    protected function getLegacyAttributes(): array
    {
        return [];
    }

    public function testHasLegacyAttributes()
    {
        if (!empty($this->getLegacyAttributes()) && get_parent_class($this->getEloquentModelName()) === LegacyModel::class) {
            $this->assertEquals($this->createNewModel()->legacy, $this->getLegacyAttributes());
        }

        $this->assertTrue(true);
    }
}
