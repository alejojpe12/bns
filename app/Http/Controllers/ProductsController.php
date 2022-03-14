<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class ProductsController extends Controller
{
    //

    Public function index()
    {
        $products=Product::orderBy('created_at', 'desc')->simplePaginate(16);
        return view('products')->with('products', $products);
    }

    public function showOwnProducts()
    {
        $products= Product::where('user_id',Auth::id())->orderBy('created_at', 'desc')->get();
        return view('dashboard')->with('products',$products);
    }

    public function show($id){
        $product=Product::find($id);
        return view('product')->with('product', $product);
    }


    public function store(Request $request){
        $request->validate([
            'title'=>'required',
            'desc-sm'=>'required',
            'desc-full'=>'required',
            'price'=>'required|numeric',
            'img'=>'required',

        ],[
            'title.required'=>'Please enter a title',            
            'desc-sm.required'=>'Please enter a short description',            
            'desc-full.required'=>'Please enter a complete description',            
            'price.required'=>'Please enter the product price',            
            'price.numeric'=>'The price should be a number',
            'img.required'=>'Please submit an image',
        ],[
            'desc-sm'=> 'short description',
            'desc-full'=> 'full description',
            'img'=> 'product image',
        ]);

        $path=$request->file('img')->store('product_images');

        $product=new Product();
        $product->title=$request->input('title');
        $product->short_desc=$request->input('desc-sm');
        $product->long_desc=$request->input('desc-full');
        $product->price=$request->input('price');
        $product->image_url=$path;
        $product->user_id=Auth::id();


        $product->save();
        
        return redirect('/product/'.$product->id);
    }

    public function edit($id){
        $product=Product::find($id);
        return view('edit')->with('product', $product);
    }

    public function update(Request $request, $id){
        $request->validate([
            'price'=>'numeric'
        ]);

        $product=Product::find($id);
        if($request->hasFile('img'))
        {
            $path=$request->file('img')->store('product_images');
        }

        if(!empty($request->input('title')))
        {
            $product->title=$request->input('title');
        }

        if(!empty($request->input('desc-sm')))
        {
            $product->short_desc=$request->input('desc-sm');
        }

        if(!empty($request->input('desc-full')))
        {
            $product->long_desc=$request->input('desc-full');
        }

        if(!empty($request->input('price')))
        {
            $product->price=$request->input('price');
        }

        $product->save();

        return redirect('/product/'.$product->id);
    }

    public function destroy($id){
        $product=Product::find($id);
        $product->delete();
        return redirect()->action([ProductsController::class,'showOwnProducts']);
    }
}
