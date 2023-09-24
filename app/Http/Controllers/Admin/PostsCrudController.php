<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\GuideRequest as StoreRequest;
use App\Http\Requests\GuideRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;

/**
 * Class GuideCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PostsCrudController extends CrudController {
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup() {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel(Post::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/posts');
        $this->crud->setEntityNameStrings('post', 'posts');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->allowAccess('show');

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->addColumn(['name' => 'title', 'type' => 'text', 'label'
        => 'Title']);
        $this->crud->addField(['name' => 'title', 'type' => 'text', 'label'
        => 'Title']);

        $this->crud->addColumn(['name' => 'slug', 'type' => 'text', 'label'
        => "Slug"]);
        $this->crud->addField(['name' => 'slug', 'type' => 'text', 'label'
        => "Slug"]);

//        $this->crud->addColumn(['name' => 'content', 'type' => 'markdown',
//                                   'label' => 'Content']);
        $this->crud->addField(['name' => 'content', 'type' => 'textarea',
                                  'label' => 'Content']);

        $this->crud->addButtonFromView('line', 'Publish', 'publish');
    }

    public function publish() {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('Publish');
    }

}
