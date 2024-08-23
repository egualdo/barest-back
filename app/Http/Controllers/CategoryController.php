<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Category\StoreRequest;
use App\Http\Requests\Landing\Category\UpdateCategoryRequest;
use App\Http\Requests\Landing\GroupCategory\StoreRequest as GroupCategoryStoreRequest;
use App\Http\Requests\Landing\GroupCategory\UpdateRequest as GroupCategoryUpdateRequest;
use App\Http\Requests\Landing\SubCategory\StoreRequest as SubCategoryStoreRequest;
use App\Http\Requests\Landing\SubCategory\UpdateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\GroupCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
  public function allgroups()
  {
    $data=GroupCategory::activos()->with(['categoryLevel2','categoryLevel2.categoryLevel3'])->get();
    return $this->showAll($data);
  }

  public function allSubCategories(){
    $data=SubCategory::activos()->with(['categoryLevel2','categoryLevel2.categoryLevel1'])->get();
    return $this->showAll($data);
  }

  public function all()
  {
    $data=Category::activos()
                  ->with(['categoryLevel1','categoryLevel3'])
                  ->get();

    return $this->showAll($data);
  }

  public function allfeatured()
  {
    $data=Category::activos()
                  ->where('featured',1)
                  ->with(['categoryLevel1','categoryLevel3'])
                  ->get();

    return $this->showAll( $data );
  }

  public function categoryByPlan()
  {
    $plansActive=Auth::user()->has_active_plan();

    $categories=Category::activos()
                        ->where('only_market',$plansActive)
                        // ->orWhere('featured',1)
                        ->with(['categoryLevel1','categoryLevel3'])
                        ->get();

    return $this->showAll( $categories );
  }

  public function store(StoreRequest $request)
  {
    $category = $request->validated();

    try {

      $response = Category::create($category);

      return $this->success( $response );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function groupCategoryStore(GroupCategoryStoreRequest $request)
  {
    $group = $request->validated();

    try {

      GroupCategory::create($group);

      return $this->success( $group );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function subCategoryStore(SubCategoryStoreRequest $request)
  {
    $subCategory = $request->validated();

    try {

      SubCategory::create($subCategory);

      return $this->success( $subCategory );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function groupShow(GroupCategory $groupCategory)
  {
    return $this->showOne( $groupCategory );
  }

  public function categoryShow(Category $category)
  {
    return $this->showOne( $category );
  }

  public function subCategoryShow(SubCategory $subCategory)
  {
    return $this->showOne( $subCategory );
  }

  public function update(UpdateCategoryRequest $request, Category $categ)
  {
    $validated = $request->validated();

    try {

      $categ->update($validated);

      return $this->success( $categ );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function groupCategoryUpdate(GroupCategoryUpdateRequest $request, GroupCategory $groupCategory)
  {
    $validated = $request->validated();

    try {
      $groupCategory->update($validated);

      return $this->success( $groupCategory );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function subCategoryUpdate(UpdateRequest $request, SubCategory $subCategory)
  {
    $validated = $request->validated();

    try {
      $subCategory->update($validated);

      return $this->success( $subCategory );

    } catch (\Exception $exception) {
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function destroy(Category $category)
  {
    $category->delete();

    return $this->success(true);
  }

  public function groupCategoryDestroy(GroupCategory $groupCategory)
  {
    $groupCategory->delete();

    return $this->success(true);
  }

  public function subCategoryDestroy(SubCategory $subCategory)
  {
    $subCategory->delete();

    return $this->success(true);
  }

  public function categoriesByGroups($post_type_id){

    $categories = GroupCategory::select('id','name')
                              ->with([
                                'categoryLevel2' => function($query) {
                                  $query->select(
                                            'category_level_2.id',
                                            'category_level_2.name',
                                            'group_id',
                                            DB::raw('COUNT(category_level_3.id) AS categoriesLvl3_count')
                                        )
                                        ->leftJoin('category_level_3', 'category_level_2.id', 'category_level_3.category_id')
                                        ->groupBy('category_level_2.id')
                                        ->orderByRaw('COUNT(category_level_3.id) = NULL DESC, COUNT(category_level_3.id) DESC');
                                },
                                'categoryLevel2.categoryLevel3:id,name,category_id'
                              ])
                              ->where('post_type_id', $post_type_id)
                              ->get();

    /* $categories = GroupCategory::select('id','name')
                              ->withCount([
                                'posts'
                              ])
                              ->with([
                                'categoryLevel2' => function($query) {
                                  $query->withCount('posts');
                                },
                                'categoryLevel2:id,name,group_id',
                                'categoryLevel2.categoryLevel3' => function($query) {
                                  $query->withCount('posts');
                                },
                                'categoryLevel2.categoryLevel3:id,name,category_id'
                              ])
                              ->where('post_type_id', $post_type_id)
                              ->get(); */

    return $this->showAll($categories);

  }

  public function categoriesForCarousel($post_type_id){
    $categories = Category::select('category_level_2.id', 'category_level_2.name', 'category_level_2.icon')
                          ->join('category_level_1' ,'category_level_2.group_id', 'category_level_1.id')
                          ->where('category_level_1.post_type_id', $post_type_id)
                          ->where('category_level_2.featured', 1)
                          ->get();

    return $this->showAll($categories);
  }
}
