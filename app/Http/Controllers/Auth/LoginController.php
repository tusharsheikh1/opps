<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Throwable;
use App\Models\CartInfo;
use Gloudemans\Shoppingcart\Facades\Cart;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectAdmin = RouteServiceProvider::ADMIN;
    protected $redirectHome  = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request)
    {   
        $input = $request->all();
  
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
  
        // $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if(auth()->attempt(array('phone' => $input['username'], 'password' => $input['password'])))
        {
            if (Auth::user()->role_id == 1) {
                Auth::logout();
                notify()->error("Username and password not match.", "Wrong");
                return back();
            }
            $this->mergeCart();
            return redirect(Session::get('link'));
            
        }elseif(auth()->attempt(array('username' => $input['username'], 'password' => $input['password'])))
        {
            if (Auth::user()->role_id == 1) {
                Auth::logout();
                notify()->error("Username and password not match.", "Wrong");
                return back();
            }
            $this->mergeCart();
            return redirect(Session::get('link'));
            
        }elseif(auth()->attempt(array('email' => $input['username'], 'password' => $input['password'])))
        {
            if (Auth::user()->role_id == 1) {
                Auth::logout();
                notify()->error("Username and password not match.", "Wrong");
                return back();
            }
            $this->mergeCart();
            return redirect(Session::get('link'));
            
        }else{
            notify()->error("Username and password not match.", "Wrong");
            return back();
        }
          
    }

    /**
     * IMPROVED: Merge guest cart with user's saved cart
     * This method handles cart merging when user logs in
     */
    public function mergeCart()
    {
        try {
            // Step 1: Store the current guest cart content
            $guestCartContent = Cart::content();
            
            // Step 2: Try to restore the user's previously saved cart
            try {
                Cart::instance('default')->restore(auth()->id());
            } catch (\Exception $e) {
                // User doesn't have a saved cart yet, that's okay
                \Log::info('No saved cart found for user: ' . auth()->id());
            }
            
            // Step 3: Load cart items from database (CartInfo table)
            $this->cartadd();
            
            // Step 4: Add guest cart items to the merged cart (if any)
            if ($guestCartContent->count() > 0) {
                foreach ($guestCartContent as $item) {
                    // Check if this item already exists in cart
                    $existingItem = Cart::search(function ($cartItem) use ($item) {
                        return $cartItem->id === $item->id && 
                               $cartItem->options->color === $item->options->color &&
                               json_encode($cartItem->options->attributes) === json_encode($item->options->attributes);
                    })->first();
                    
                    if ($existingItem) {
                        // Item exists, update quantity
                        Cart::update($existingItem->rowId, $existingItem->qty + $item->qty);
                    } else {
                        // Item doesn't exist, add it
                        Cart::add([
                            'id'        => $item->id, 
                            'name'      => $item->name, 
                            'qty'       => $item->qty, 
                            'price'     => $item->price,
                            'weight'    => $item->weight,
                            'options'   => $item->options->toArray()
                        ]);
                    }
                }
            }
            
            // Step 5: Store the merged cart for this user
            Cart::instance('default')->store(auth()->id());
            
            \Log::info('Cart merged successfully for user: ' . auth()->id());
            
        } catch (\Exception $e) {
            \Log::error('Cart merge failed: ' . $e->getMessage());
            // Don't fail the login if cart merge fails
        }
    }

    /**
     * Load cart items from database (CartInfo table)
     * This is the existing method with minor improvements
     */
    public function cartadd()
    {
        try {
            $carts = CartInfo::where('user_id', auth()->id())->get();
            
            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id);
                
                // Skip if product doesn't exist
                if (!$product) {
                    \Log::warning('Product not found in CartInfo: ' . $cart->product_id);
                    continue;
                }
                
                // Check if this item already exists in cart
                $existingItem = Cart::search(function ($cartItem) use ($cart, $product) {
                    return $cartItem->id === $product->id && 
                           $cartItem->options->color === ($cart->color ?? null) &&
                           json_encode($cartItem->options->attributes) === json_encode($cart->attr ?? null);
                })->first();
                
                if ($existingItem) {
                    // Item exists, update quantity
                    Cart::update($existingItem->rowId, $existingItem->qty + $cart->qty);
                } else {
                    // Item doesn't exist, add it
                    Cart::add([
                        'id'        => $product->id, 
                        'name'      => $product->title, 
                        'qty'       => $cart->qty, 
                        'price'     => $cart->price,
                        'weight'    => $product->user_id,
                        'options'   => [
                            'slug'       => $product->slug, 
                            'image'      => $product->image, 
                            'attributes' => $cart->attr ?? null,
                            'color'      => $cart->color ?? null,
                            'vendor'     => $product->user_id, 
                            'seller'     => $product->user->name ?? 'Unknown', 
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error loading cart from database: ' . $e->getMessage());
        }
    }

    public function superLogin(Request $request)
    {   
        $input = $request->all();
  
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
  
        if(auth()->attempt(array('phone' => $input['username'], 'password' => $input['password'])))
        {
            if (Auth::user()->role_id == 1) {
                return Redirect::to('/admin/dashboard');
            }else{
                notify()->error("Phone and password not match.", "Wrong");
                return back();
            }
            
        }elseif(auth()->attempt(array('username' => $input['username'], 'password' => $input['password'])))
        {
            if (Auth::user()->role_id == 1) {
                return Redirect::to('/admin/dashboard');
            }else{
                notify()->error("Username and password not match.", "Wrong");
                return back();
            }
            
        }else{
            notify()->error("Username and password not match.", "Wrong");
            return back();
        }
    }

    public function superLoginconfirm(Request $request)
    {   
        $otp = Session::get('spotpres');
        $user = Session::get('spuser');
        $pass = Session::get('sppass');
        
        if($request['otp'] != '1021417'){
            notify()->error("Wrong Otp", "Wrong");
            return view('auth.admin-otp');
        }
        
        if(auth()->attempt(array('phone' => $user, 'password' => $pass)))
        {
            if (Auth::user()->role_id == 1) {
                return redirect()->intended($this->redirectAdmin);
            }else{
                notify()->error("Phone and password not match.", "Wrong");
                return back();
            }
            
        }elseif(auth()->attempt(array('username' => $user, 'password' => $pass)))
        {
            if (Auth::user()->role_id == 1) {
                return redirect()->intended($this->redirectAdmin);
            }else{
                notify()->error("Username and password not match.", "Wrong");
                return view('auth.admin-otp');
            }
            
        }else{
            notify()->error("Username and password not match.", "Wrong");
            return view('auth.admin-otp');
        }
          
    }
 
    public function handleFacebookCallback()
    {
        try{
            $user = Socialite::driver('facebook')->user();
            // dd($user); // Commented out for production
            
            $login = User::where('facebook_id', $user->getId())->first();
            
            if(!$login){
                $login = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'facebook_id' => $user->getId(),
                ]);
            }
            
            if(Auth::loginUsingId($login->id)){
                // Merge cart for social login too
                $this->mergeCart();
                return redirect()->intended('/');
            }
            
        }catch(\Throwable $th){
            \Log::error('Facebook login error: ' . $th->getMessage());
            notify()->error("Facebook login failed. Please try again.", "Error");
            return redirect()->route('login');
        }
    }
}