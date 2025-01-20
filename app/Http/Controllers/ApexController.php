<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Size;
use App\Models\StockSummary;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ApexController extends Controller
{
    public function editOrder(Request $request, $orderId)
    {
        $sale = Sale::with(['saleItems', 'saleItems.product', 'customer', 'user']) // Ensure nested loading for product
            ->where('id', $orderId)
            ->firstOrFail();

        $allcategories = Category::with('parent')->get()->map(function ($category) {
            $category->hierarchy_string = $category->hierarchy_string; // Access it
            return $category;
        });
        $colors = Color::orderBy('created_at', 'desc')->get();
        $sizes = Size::orderBy('created_at', 'desc')->get();
        $allemployee = Employee::orderBy('created_at', 'desc')->get();

      
        // Pass the retrieved sale to the Inertia view
        return Inertia::render('Apex/EditBill', [
            'product' => null,
            'error' => null,
            'loggedInUser' => Auth::user(),
            'allcategories' => $allcategories,
            'allemployee' => $allemployee,
            'colors' => $colors,
            'sizes' => $sizes,
            'sale' => $sale,
        ]);
    }

    public function updateOrder(Request $request)
    {
        
       //dd($request);
        if (!Gate::allows('hasRole', ['Admin', 'Cashier'])) {
            abort(403, 'Unauthorized');
        }
        // Combine countryCode and contactNumber to create the phone field


        $customer = null;

        $products = $request->input('products');
        $totalAmount = collect($products)->reduce(function ($carry, $product) {
            return $carry + ($product['quantity'] * $product['selling_price']);
        }, 0);

        $totalCost = collect($products)->reduce(function ($carry, $product) {
            return $carry + ($product['quantity'] * $product['cost_price']);
        }, 0);

        $totalDiscount = collect($products)->reduce(function ($carry, $product) {
            if (isset($product['discount']) && $product['discount'] > 0 && isset($product['apply_discount']) && $product['apply_discount'] != false) {
                // Calculate the discount amount per product
                $discountAmount = ($product['selling_price'] - $product['discounted_price']) * $product['quantity'];
                return $carry + $discountAmount;
            }
            return $carry;
        }, 0);
    
        DB::beginTransaction(); // Start a transaction

        try {
            $sale = Sale::with(['saleItems', 'saleItems.product', 'customer', 'user']) // Ensure nested loading for product
                ->where('order_id', $request->input('orderId'))
                ->firstOrFail();
              //  dd($sale->type );
                $updateStockSummaries = true; 
                $billCategory = $sale->type; 
               
            foreach ($products as $product) {
                // Check stock before saving sale items
                $productModel = Product::find($product['id']);
               // dd($product['id']);
                if ($productModel) {
                    $stockchanged = false;

                    if ($product['selected']) {
                        $newStockQuantity = $productModel->stock_quantity - $product['quantity'];
                        $stockchanged = true;
                    } else {
                        $newStockQuantity = $productModel->stock_quantity;
                    }

                    $saleItem = SaleItem::where('sale_id', $sale->id)
                        ->where('product_id', $product['id'])
                        ->first();
                     
                       
                    if ($saleItem && $product['selected']) {
                        if ($newStockQuantity < 0) {
                            // Rollback transaction and return error
                            DB::rollBack();
                            return response()->json([
                                'message' => "Insufficient stock for product: {$productModel->name}
                                ({$productModel->stock_quantity} available)",
                            ], 423);
                        }

                        if ($productModel->expire_date && now()->greaterThan($productModel->expire_date)) {
                            // Rollback transaction and return error
                            DB::rollBack();
                            return response()->json([
                                'message' => "The product '{$productModel->name}' has expired (Expiration Date: {$productModel->expire_date->format('Y-m-d')}).",
                            ], 423); // HTTP 422 Unprocessable Entity
                        }
                        
                        $saleItem->update([
                            'status' => $stockchanged ? 'sold' : 'pending',

                        ]);
                            
                    
                        $stockSummary = DB::table('stock_summaries')
                        ->where('product_id', $product['id'])
                        ->first();
        
                       
                     
                        // Update the relevant bill category count
                        DB::table('stock_summaries')
                            ->where('product_id', $product['id'])
                            ->update([
                                $billCategory => DB::raw("$billCategory + {$product['quantity']}"),
                            ]);
                         
                        
                // Recalculate and update total_stocks_remaining
                $updatedStockSummary = DB::table('stock_summaries')
                ->where('product_id', $product['id'])
                ->first();

                if ($updatedStockSummary) {
                    $totalIssued = 
                        $updatedStockSummary->daily_franchise +
                        $updatedStockSummary->retail +
                        $updatedStockSummary->courier_transfer +
                        $updatedStockSummary->pending +
                        $updatedStockSummary->meeting_order +
                        $updatedStockSummary->staff_issued +
                        $updatedStockSummary->director_issued +
                        $updatedStockSummary->return +
                        $updatedStockSummary->promotion +
                        $updatedStockSummary->sample +
                        $updatedStockSummary->damaged;

                    $newTotalRemaining = $updatedStockSummary->received - $totalIssued;

                    DB::table('stock_summaries')
                        ->where('product_id', $product['id'])
                        ->update([
                            'total_stocks_remaining' => $newTotalRemaining,
                        ]);
                }
               // dd($updatedStockSummary);
            

                    
                        if ($stockchanged) {
                            StockTransaction::create([
                                'product_id' => $product['id'],
                                'transaction_type' => 'Sold',
                                'quantity' => $product['quantity'],
                                'transaction_date' => now(),
                                'supplier_id' => $productModel->supplier_id ?? null,
                            ]);

    
                            // Update stock quantity
                            $productModel->update([
                                'stock_quantity' => $newStockQuantity,
                            ]);
                        }
                    }
                }
            }
           // dd($products );
  // Update the stock_summaries table only if any item changed from pending to sold
/*   if ($updateStockSummaries) {
    foreach ($products as $product) {
        $productModel = Product::find($product['id']);
        if ($productModel) {
            $stockSummary = DB::table('stock_summaries')
                ->where('product_id', $product['id'])
                ->first();

            if ($stockSummary) {
                // Update the relevant bill category count
                DB::table('stock_summaries')
                    ->where('product_id', $product['id'])
                    ->update([
                        $billCategory => DB::raw("$billCategory + {$product['quantity']}"),
                    ]);

                // Recalculate and update total_stocks_remaining
                $updatedStockSummary = DB::table('stock_summaries')
                    ->where('product_id', $product['id'])
                    ->first();

                    dd($updatedStockSummary);
                if ($updatedStockSummary) {
                    $totalIssued = 
                        $updatedStockSummary->daily_franchise +
                        $updatedStockSummary->retail +
                        $updatedStockSummary->courier_transfer +
                        $updatedStockSummary->pending +
                        $updatedStockSummary->meeting_order +
                        $updatedStockSummary->staff_issued +
                        $updatedStockSummary->director_issued +
                        $updatedStockSummary->return +
                        $updatedStockSummary->promotion +
                        $updatedStockSummary->sample +
                        $updatedStockSummary->damaged;

                    $newTotalRemaining = $updatedStockSummary->received - $totalIssued;

                    DB::table('stock_summaries')
                        ->where('product_id', $product['id'])
                        ->update([
                            'total_stocks_remaining' => $newTotalRemaining,
                        ]);
                }
            }
        }
    }
} */

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Sale recorded successfully!'], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while processing the sale.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Customer details saved successfully!',
            'data' => $customer,
        ], 201);
    }

    public function displayStock(Request $request)
    {
        // Fetch all records from the stock_summaries table
        $allStockTransactions = DB::table('stock_summaries')
            ->select(
                'id',
                'product_id',
                'product_name',
                'received',
                'daily_franchise',
                'retail',
                'courier_transfer',
                'pending',
                'meeting_order',
                'staff_issued',
                'director_issued',
                'return',
                'promotion',
                'sample',
                'damaged',
                'total_stocks_remaining',
                'created_at'
            )
            ->get();
    
        $totalStockTransactions = $allStockTransactions->count();
    
        return Inertia::render('StockSummary/Index', [
            'allStockTransactions' => $allStockTransactions,
            'totalStockTransactions' => $totalStockTransactions,
        ]);
    }
    
    
}
