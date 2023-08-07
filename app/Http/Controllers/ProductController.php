<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //    for($i=0;$i<=20000;$i++){
        //         Product::create([
        //             'name' => 'Projector',
        //             'category_id' => rand(1,5),
        //             'description' => 'Projecktor Here',
        //             'price' => 25000,
        //         ]);
        //     }
        //     dd("DONE");
        $paginate = request('paginate', 5);
        $searchTerm = request('search', '');

        $sortField = request('sort_field', 'created_at');
        if (!in_array($sortField, ['id', 'name', 'description', 'price'])) {
            $sortField = 'created_at';
        }
        $sortDirection = request('sort_direction', 'created_at');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $filled = array_filter(request([
            'id', 'name', 'description', 'price',
        ]));

        $products = Product::when(count($filled) > 0, function ($query) use ($filled) {
            foreach ($filled as $column => $value) {
                $query->where($column, 'LIKE', '%' . $value . '%');
            }

        })
            ->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })
            ->when(request('category_id', '') != '', function ($query) {
                $query->where('category_id', request('category_id'));

            })->orderBy($sortField, $sortDirection)->paginate($paginate);

        // return ProductResource::collection($products);
        return response()->json($products);
    }

    public function export()
    {
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            return Excel::download(new ProductExport, 'users.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }
    public function exportPdf()
    {
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            $data = [];
            $pdf = PDF::loadView('product', ['data' => $data]);
            return $pdf->output();
            // return $pdf->download('itsolutionstuff.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function import()
    {
        Excel::import(new ProductImport, request()->file('file'));

        return back();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        # dd($request->all());

        $request->validate([
            'name' => 'required',
            // 'description' => 'required',
            'price' => 'required',
            'file' => 'required',
            // 'gallery_images' => 'required',
        ]);

        try {
            $input = $request->all();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = $file->getClientOriginalName();
                $file->storeAs('uploads/product', $filename, 'public');
                $input['feature_image'] = $filename;
            }
            $galleryImages = [];

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $uploadedPath = $file->store('uploads/product/gallery', 'public'); // Store in 'storage/app/public/uploads'
                    $galleryImages[] = $uploadedPath;
                    // $galleryImages[] = $uploadedPath;
                }
                $input['gallery_images'] = implode(",", $galleryImages);
            }

            Product::create($input);
            return response()->json([
                'status' => 'success',
                'message' => 'Product Created Successfully!!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        $product->update($request->all());

        return $product;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
