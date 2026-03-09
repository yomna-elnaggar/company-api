<?php

namespace App\Http\Controllers;

use App\Interfaces\CRUDRepositoryInterface;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Services\CityService;
use App\Http\Resources\BranchResource;
use Illuminate\Http\Request;
use Exception;

class BranchController extends ApiController
{
    protected $itemRepository;
    protected $model = Branch::class;

    public function __construct(CRUDRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }
 
    public function index(Request $request)
    {
        try {
            $filters = [
                'search'     => $request->search ?? null,
                'city_id'    => $request->city_id ?? null,
                'company_id' => $request->company_id ?? null,
                'active'     => $request->active ?? null,
            ];

            // ========== COUNTS ==========
            $counts = $this->itemRepository->getCount($this->model, $request->all());

            // ========== PAGINATION & ITEMS ==========
            $items = $this->itemRepository->getAllItems($this->model, $filters, 50);

            $companies = Company::active()->get();
            $cities = app(CityService::class)->getAllCities();

            // ========== FINAL RESULT ==========
            $result = [
                'items'     => [
                    'data' => BranchResource::collection($items),
                    'pagination' => [
                        'current_page' => $items->currentPage(),
                        'last_page'    => $items->lastPage(),
                        'per_page'     => $items->perPage(),
                        'total'        => $items->total(),
                    ],
                ],
                'counts'    => $counts,
                'cities'    => $cities,
                'companies' => $companies,
            ];

            return ApiController::respondWithSuccess(
                'Branches retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to retrieve branches',
                $e->getMessage(),
                500
            );
        }
    }

    public function create()
    {
        try {
            $result = [
                'cities'    => app(CityService::class)->getAllCities(),
                'companies' => Company::active()->get(),
            ];

            return ApiController::respondWithSuccess(
                'Form data retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to retrieve form data',
                $e->getMessage(),
                500
            );
        }
    }

    public function show($id)
    {
        try {
            $branch = clone $this->itemRepository->getItemById($this->model, $id);
            $branch->load('company');
            if (!$branch) {
                return ApiController::respondWithNotFound();
            }
            return ApiController::respondWithSuccess(
                'Branch retrieved successfully',
                new BranchResource($branch)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to retrieve branch',
                $e->getMessage(),
                404
            );
        }
    }

    public function edit($id)
    {
        try {
            $item = $this->itemRepository->getItemById($this->model, $id);

            if (!$item) {
                return ApiController::respondWithNotFound();
            }

            $result = [
                'item'      => $item,
                'cities'    => app(CityService::class)->getAllCities(),
                'companies' => Company::active()->get(),
            ];

            return ApiController::respondWithSuccess(
                'Branch data retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to retrieve branch data',
                $e->getMessage(),
                500
            );
        }
    }

    public function store(BranchRequest $request)
    {
        try {
            $branch = $this->itemRepository->createItem($this->model, $request->validated());
            return ApiController::respondWithSuccess(
                'Branch created successfully',
                new BranchResource($branch)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to create branch',
                $e->getMessage(),
                500
            );
        }
    }

    public function update(BranchRequest $request, $id )
    {
        try {
            $branch = $this->itemRepository->updateItem($this->model, $id, $request->validated());
            if (!$branch) {
                return ApiController::respondWithNotFound();
            }
            return ApiController::respondWithSuccess(
                __('messages.UpdatedMessage'),
                new BranchResource($branch)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to update branch',
                $e->getMessage(),
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->itemRepository->deleteItem($this->model, $id);
            if (!$deleted) {
                return ApiController::respondWithNotFound();
            }
            return ApiController::respondWithSuccess(
                'Branch deleted successfully'
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to delete branch',
                $e->getMessage(),
                500
            );
        }
    }

    public function toggleStatus($id)
    {
        try {
            $branch = $this->itemRepository->toggleStatus($this->model, $id);
            if (!$branch) {
                return ApiController::respondWithNotFound();
            }
            return ApiController::respondWithSuccess(
                __('messages.status_toggled'),
                new BranchResource($branch)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to update branch status',
                $e->getMessage(),
                500
            );
        }
    }

    public function getBranches($company_id)
    {
        try {
            $branches = Branch::where('company_id', $company_id)->get();
            
            return ApiController::respondWithSuccess(
                'Branches retrieved successfully',
                BranchResource::collection($branches)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                'Failed to retrieve branches',
                $e->getMessage(),
                500
            );
        }
    }
}
