<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private $product, $totalPage = 10;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = $this->product->getResults($request->all(), $this->totalPage);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $name = Str::kebab($request->name);
            $extension = $request->image->extension();
            $nameFile = "{$name}.{$extension}";
            $data['image'] = $nameFile;

            $upload = $request->image->storeAs("products", $nameFile);
            if (!$upload)
                return response()->json(['error' => 'Fail Upload'], 500);
        }

        $product = $this->product->create($data);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->product->with('category')->find($id);
        if (!$product)
            return response()->json(['error' => 'Not Found'], 404);

        return response()->json($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = $this->product->find($id);
        if (!$product)
            return response()->json(['error' => 'Not Found'], 404);

        $data = $request->all();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            if ($product->image) {
                if (Storage::exists("products/{$product->image}"))
                    Storage::delete("products/{$product->image}");
            }

            $name = Str::kebab($request->name);
            $extension = $request->image->extension();
            $nameFile = "{$name}.{$extension}";
            $data['image'] = $nameFile;

            $upload = $request->image->storeAs("products", $nameFile);
            if (!$upload)
                return response()->json(['error' => 'Fail Upload'], 500);
        }
        $product->update($data);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = $this->product->find($id);
        if (!$product)
            return response()->json(['error' => 'Not Found'], 404);

        if ($product->image) {
            if (Storage::exists("products/{$product->image}"))
                Storage::delete("products/{$product->image}");
        }

        $product->delete();

        return response()->noContent();
    }
}
