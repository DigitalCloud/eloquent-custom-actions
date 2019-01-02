# Laravel Eloquent Custom Actions.

The package idea inspired from laravel eloquent events functionallity and eloquent scope code style. If you like the Event driven development approach, this package can dramatically clean your model codes.

## Installation

You can install the package via composer:

```bash
composer require digitalcloud/eloquent-custom-actions
```

## Usage Example

Without this package, to simulate the eloquent events, you will end with:

```php

class User extends Authenticatable
    
    public function verify($mobile)
    {
        $userMobile = new UserMobile([
            'mobile' => $mobile, 'status' => self::STATUS_VERIFIED
        ]);

        if(app()->events->until(
            event(new MobileVerifying($userMobile)) !== false
        )){
            $userMobile = $this->mobiles()->save($userMobile);
            event(new MobileVerified($userMobile));
            return $userMobile;
        }
        
        return false;
    }
}
``` 

To simplify your `User` model, declare `action{MethodName}` method and remove all event related codes, the package will automatically fire `before{Method}` and `after{Method}` events when `$user->verify($mobile)` invoked.

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) {
        return $userMobile = $this->mobiles()->save([
            'mobile' => $mobile, 'status' => self::STATUS_VERIFIED
        ]);
    }
}

```

## Use dispatchesEvents

As eloquent events, you can map the dispatched events using `$dispatchesEvents` proparity

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) { }
    
    protected $dispatchesEvents = [
        'beforeVerify' => MobileVerifying::class,
        'afterVerify' => MobileVerified::class
    ];
}

```

## Use EventServiceProvider

You can map events to listener as usual in EventServiceProvider, you can use both string event name or the mapped events from the `dispatchesEvents`:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    // ...
    
    protected $listen = [
        MobileVerifying::class => [ SomeListener::class ],
        MobileVerified::class => [ SomeListener::class ],
        
        // or
        
        'eloquent.beforeVerify: App\User' => [ SomeListener::class ],
        'eloquent.afterVerify: App\User' => [ SomeListener::class ],
    ];
    
    // ...
}

```

## Use Model Observer
As eloquent observable, you can map the observable events using `$observables` proparity

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) { }
    
    protected $observables = [
        'beforeVerify', 'afterVerify'
    ];
}

```

and then you cn add beforeVerify and afterVerify functions in the ModelObserver class same as other eloqunt functions.

```php
<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    // Default eloquent actions
    public function created(User $user){ }

    // Custom eloquent actions
    public function beforeVerify(User $user){ }

    public function afterVerify(User $user){ }
}
```

## Stopping The Propagation Of An Event
As mentioned in the Laravel docs:

> Sometimes, you may wish to stop the propagation of an event to other listeners. You may do so by returning `false` from your listener's handle method.

If any `before{Action}` listener return `false` the process will be stoped, and the real action will not excute.

## Roadmap

We currently working on:

- [ ] Support model boot method
- [ ] Support model policy
- [ ] Rollback the `before{Action}` effect if one listener return false


