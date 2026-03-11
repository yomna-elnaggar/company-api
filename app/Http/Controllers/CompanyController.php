<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Requests\CreateCompanyRequest; 
use App\Interfaces\CRUDRepositoryInterface;
use App\Models\Company;
use App\Models\Branch;
use App\Models\CompanySetting; 
use App\Models\User;
use App\Services\CityService;
use App\Services\PackageService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; 
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyPackageResource;
use App\Helpers\Image;
use Illuminate\Http\JsonResponse;

class CompanyController extends ApiController
{
    private CRUDRepositoryInterface $itemRepository;

    private $model = Company::class;

    private $images = ['image', 'commerce_letter', 'electronic_contract_website'];

    /**
     * Instantiate a new controller instance.
     */
    public function __construct(CRUDRepositoryInterface $itemRepository, CityService $cityService, PackageService $packageService)
    {
        $this->itemRepository = $itemRepository;
        $this->cityService = $cityService;
        $this->packageService = $packageService;
    }

    public function index(Request $request)
    {
        $data = $request->all();

        // 1. Export Support (Parity with Yomna)
        // if ($request->action === 'export') {
        //     return $this->exportExcel($request);
        // }

        $activeIds = null;
        if ($request->filled('have_package')) {
            $activeIds = $this->packageService->getCompanyIdsWithActivePackages();
            if ($request->have_package == '1') {
                $request->merge(['ids' => $activeIds]); 
            } elseif ($request->have_package == '0') {
                $request->merge(['not_ids' => $activeIds]);
            }
            $data = $request->all();
        }

        $items = $this->itemRepository->getAllItems($this->model, $data, 20);
        $counts = $this->itemRepository->getCount($this->model);

        // Fetch expired count (Companies who are NOT in activeIds)
        if ($activeIds === null) {
            $activeIds = $this->packageService->getCompanyIdsWithActivePackages();
        }
        $expired_companies = $this->itemRepository->getCount($this->model, ['not_ids' => $activeIds]); 

        return ApiController::respondWithSuccess(null, [
            'auth' => auth()->user(),
            'items' => [
                'data' => CompanyResource::collection($items),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page'    => $items->lastPage(),
                    'per_page'     => $items->perPage(),
                    'total'        => $items->total(),
                    'links'        => $items->links(),
                ],
            ],
            'counts' => $counts,
            'expired_companies' => $expired_companies,
        ]);
    }

    public function expired(Request $request)
    {
        $activeIds = $this->packageService->getCompanyIdsWithActivePackages();
        $data = $request->all();
        $data['not_ids'] = $activeIds;

        $items = $this->itemRepository->getAllItems($this->model, $data, 50);

        return ApiController::respondWithSuccess(null, [
            'items' => CompanyResource::collection($items),
            'counts' => $this->itemRepository->getCount($this->model),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'links'        => $items->links(),
            ],
        ]);
    }

    public function show($id)
    {
        $item = $this->itemRepository->getItemById($this->model, $id);
        
        // Using attributes directly to trigger model internal caching (for use in CompanyResource)
        $activePackageData = $item->active_package; 
        $cityData = $item->city; // Cache city as well

        if ($activePackageData) {
            $result = [
                'activePackage' => new CompanyPackageResource($activePackageData),
                'vehicleCount' => $activePackageData['vehicle_count'] ?? 0, 
                'transaction' => $activePackageData['last_transaction'] ?? null, 
            ];
        } else {
            $result = [
                'activePackage' => null,
                'vehicleCount' => 0,
                'transaction' => null,
            ];
        }

        $result['cities'] = $this->cityService->getAllActiveCities();
        $result['packages'] = $this->packageService->getAllActivePackages();

        return response()->json([
            'status' => true,
            'message' => __('messages.retrieved_successfully'),
            'data' => [
                'item' => new CompanyResource($item),
                'result' => $result,
            ],
        ]);
    }

    public function create(Company $item)
    {
        $cities = $this->cityService->getAllCities();
        $packages = $this->packageService->getAllActivePackages();

        return ApiController::respondWithSuccess(null, [
            'item'   => $item,
            'result' => [
                'cities' => $cities,
                'packages' => $packages,
            ],
        ]);
    }

    public function store(CreateCompanyRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $data = $validated;

            // 1. Upload images
            foreach ($this->images as $imageField) {
                if ($request->hasFile($imageField)) {
                    $data[$imageField] = Image::uploadToPublic($request->file($imageField), 'companies');
                }
            }

            // 2. Create company
            $item = $this->itemRepository->createItem($this->model, $data);

            // 3. Create User Locally
            $userData = [
                'name'       => $validated['name'] ?? $validated['company_name'],
                'mobile'     => $validated['mobile'],
                'email'      => $validated['email'],
                'password'   => $validated['password'], // User model handles hashing via mutator
                'image'      => $data['image'] ?? null,
                'username'   => $validated['username'] ?? $validated['email'],
                'company_id' => $item->id,
                'active'     => true,
            ];
            User::create($userData);

            // 4. Create main branch
            $this->itemRepository->createItem(Branch::class, [
                'title'        => $validated['company_name'] . ' - ' . __('Main Branch'),
                'phone'        => $validated['mobile'],
                'email'        => $validated['email'],
                'manager_name' => $validated['name'] ?? $validated['company_name'],
                'active'       => true,
                'company_id'   => $item->id,
                'city_id'      => $item->city_id,
            ]);

            // 5. Create settings
            CompanySetting::firstOrCreate(['company_id' => $item->id]);

            // 6. Subscription via Package Microservice
            if ($request->filled('package_id')) {
                Log::info("Attempting to subscribe company {$item->id} to package {$request->package_id}");
                $this->packageService->subscribeCompany([
                    'company_id'      => $item->id,
                    'package_id'      => $request->package_id,
                    'num_of_cars'     => $request->vehicle_number,
                    'price'           => $request->price,
                    'price_with_tax'  => $request->price_with_tax,
                    'subscribed_at'   => $request->start_date ?? now(),
                    'expires_at'      => $request->end_date,
                    'payment_status'  => $request->payment_status ?? 'unpaid',
                ]);
            }

            DB::commit();
            return ApiController::respondWithSuccess(__('messages.AddedMessage'), $item, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Company creation failed: ' . $e->getMessage());
            return ApiController::respondWithError(__('messages.FailedMessage'), $e->getMessage(), 500);
        }
    }

    public function update(CreateCompanyRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            foreach ($this->images as $imageField) {
                if ($request->hasFile($imageField)) {
                    $data[$imageField] = Image::uploadToPublic($request->file($imageField), 'companies');
                }
            }

            // 1. Update company
            $item = $this->itemRepository->updateItem($this->model, $id, $data);

            // 2. Update User Locally (Manager's image/password)
            $user = User::where('company_id', $id)->first();
            if ($user) {
                $userData = [];
                if (isset($data['password'])) $userData['password'] = $data['password'];
                if (isset($data['image'])) $userData['image'] = $data['image'];
                if (isset($data['mobile'])) $userData['mobile'] = $data['mobile'];
                if (isset($data['email'])) $userData['email'] = $data['email'];
                if (isset($data['name'])) $userData['name'] = $data['name'];
                
                if (!empty($userData)) {
                    $user->update($userData);
                }
            }

            // 3. Update Subscription via Service (Parity with Yomna)
            if ($request->filled('package_id')) {
                $this->packageService->subscribeCompany([
                    'company_id'      => $id,
                    'package_id'      => $request->package_id,
                    'num_of_cars'     => $request->vehicle_number,
                    'price'           => $request->price,
                    'price_with_tax'  => $request->price_with_tax,
                    'subscribed_at'   => $request->start_date,
                    'expires_at'      => $request->end_date,
                    'payment_status'  => $request->payment_status,
                ]);
            }

            DB::commit();
            return ApiController::respondWithSuccess(__('messages.UpdatedMessage'), $item);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::respondWithError(__('messages.FailedMessage'), $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Should call WalletService to delete wallet if needed
            $this->itemRepository->deleteItem($this->model, $id);
            return ApiController::respondWithSuccess('Company deleted successfully');
        } catch (\Exception $e) {
            return ApiController::respondWithError('Failed to delete company', $e->getMessage(), 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // Placeholder for Excel Export if Maatwebsite/Excel is integrated
        return response()->json(['message' => 'Export functionality would call a shared Export Service']);
    }
}
